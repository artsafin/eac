<?php

namespace Eprst\Eac\Command;

use Eprst\Eac\Command\Helper\CommonArgs;
use Eprst\Eac\Command\Helper\CompileCommandArgs;
use Eprst\Eac\Factory\JsPass;
use Eprst\Eac\Factory\PassFactory;
use Eprst\Eac\Factory\PassInterface;
use Eprst\Eac\Service\Path;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class CompileCommand extends Command
{

    /**
     * @var CommonArgs
     */
    private $commonArgs;

    /**
     * compileArgs
     *
     * @var CompileCommandArgs
     */
    private $compileArgs;

    protected function configure()
    {
        $this
            ->setName('eac:compile')
            ->setDescription('Compile assets of specified source files');

        $this->commonArgs  = new CommonArgs();
        $this->compileArgs = new CompileCommandArgs();

        $this->commonArgs->addArguments($this);
        $this->compileArgs->addArguments($this);

        foreach (PassFactory::getPassArguments() as $arg) {
            $arg->addArguments($this);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cwd         = getcwd();
        $sourceFiles = $this->commonArgs->getSources($input);
        $webroot     = $this->commonArgs->getWebroot($input);
        $modeAliases = $this->commonArgs->getModes($input);

        $compileDir = $this->compileArgs->getCompileDir($input, $webroot, $cwd);
        $prefix     = $this->compileArgs->getPrefix($input, $compileDir, $webroot);
        $isReplace  = $this->compileArgs->isReplace($input);

        $output->writeln("Processing sources:\n\t<info>". implode("</info>\n\t<info>", $sourceFiles) . "</info>");
        $output->writeln("Compile directory: <info>{$compileDir}</info>");
        $output->writeln("Web root: <info>{$webroot}</info>");

        $passes = PassFactory::createByAlias($modeAliases, compact(array('cwd', 'webroot', 'compileDir')));

        if (empty($passes)) {
            throw new \RuntimeException("You must specify at least one mode.");
        }

        foreach ($passes as $mode) {
            $this->runPass($mode, $output, $isReplace, $sourceFiles, $webroot, $prefix);
        }
    }

    /**
     * @param PassInterface $pass
     * @param OutputInterface      $output
     * @param bool                 $isReplace
     * @param array                $sourceFiles
     * @param string               $webroot
     * @param string               $prefix
     */
    private function runPass(PassInterface $pass, OutputInterface $output, $isReplace, $sourceFiles, $webroot, $prefix)
    {
        $chunkManager = $pass->getChunkManager();
        $resolver     = $pass->getAssetResolver();
        $compiler     = $pass->getCompiler();
        $generator    = $pass->getTagGenerator();

        $tempFileMap = array();

        $chunks = $resolver->resolve($sourceFiles, $webroot);

        foreach ($chunks as $chunk) {
            $output->write("Chunk <info>{$chunk->getName()}</info>: ");

            if (empty($chunk->assets)) {
                $output->writeln("contains no assets.");
                continue;
            }

            $compileFile = $compiler->compile($chunk->assets);

            if (file_exists($compileFile)) {
                $output->write("<info>{$compileFile}</info>");
            } else {
                throw new \RuntimeException("Failed to write <info>{$compileFile}</info>.");
            }

            $compiledSrc = Path::prepend(basename($compileFile), $prefix);
            $compiledSrc = str_replace(DIRECTORY_SEPARATOR, '/', $compiledSrc);

            $output->writeln(" -> <info>{$compiledSrc}</info>");

            if (!isset($tempFileMap[$chunk->sourceFile])) {
                $tempFileMap[$chunk->sourceFile] = Path::append(sys_get_temp_dir(), 'EAC' . sha1($chunk->sourceFile));
                copy($chunk->sourceFile, $tempFileMap[$chunk->sourceFile]);
            }

            $replacement = $generator->generate($compiledSrc);
            $source      = $chunkManager->replaceChunk(file_get_contents($tempFileMap[$chunk->sourceFile]),
                                                       $chunk->id,
                                                       $replacement);
            file_put_contents($tempFileMap[$chunk->sourceFile], $source);
        }

        $output->writeln('');
        $output->writeln('Writing ' . count($tempFileMap) . ' file(s)' . ($isReplace ? ' with replace flag' : ''));
        foreach ($tempFileMap as $target => $source) {
            $target = $target . ($isReplace ? '' : '.eac');
            if (!copy($source, $target)) {
                throw new \RuntimeException("Copy failed {$source} -> {$target}");
            }
            unlink($source);
        }
    }
}
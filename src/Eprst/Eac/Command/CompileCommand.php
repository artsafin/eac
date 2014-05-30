<?php

namespace Eprst\Eac\Command;

use Eprst\Eac\Command\Helper\CommonArgsHelper;
use Eprst\Eac\Service\Factory\JsMode;
use Eprst\Eac\Service\Factory\ModeFactoryInterface;
use Eprst\Eac\Service\Path;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class CompileCommand extends Command
{
    const OPTION_COMPILE_DIR = 'out';
    const OPTION_COMPILE_DIR_DEFAULT = '<webroot>/assets-min';

    const OPTION_PREFIX = 'prefix';
    const OPTION_PREFIX_DEFAULT = 'smart choice';

    const OPTION_YUIC = 'yuicompressor';

    const OPTION_WRITE_REPLACE = 'replace';

    const OPTION_MODES = 'mode';

    /**
     * @var CommonArgsHelper
     */
    private $argsHelper;

    protected function configure()
    {
        $this->argsHelper = new CommonArgsHelper();

        $this
            ->setName('eac:compile')
            ->setDescription('Compile assets of specified source files');

        $this->argsHelper->addArguments($this);

        $this->addOption(self::OPTION_COMPILE_DIR,
                         null,
                         InputOption::VALUE_REQUIRED,
                         'Directory to put compiled files in',
                         self::OPTION_COMPILE_DIR_DEFAULT)
             ->addOption(self::OPTION_PREFIX,
                         null,
                         InputOption::VALUE_REQUIRED,
                         'Web server prefix to put in src="" attribute for compiled assets',
                         self::OPTION_PREFIX_DEFAULT)
             ->addOption(self::OPTION_YUIC,
                         null,
                         InputOption::VALUE_REQUIRED,
                         'YUI Compressor jar',
                         'yuicompressor.jar')
             ->addOption(self::OPTION_WRITE_REPLACE,
                         null,
                         InputOption::VALUE_NONE,
                         'Put modified content to source file instead of .eac file')
             ->addOption(self::OPTION_MODES,
                         substr(self::OPTION_MODES, 0, 1),
                         InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                         '');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sourceFiles = $this->argsHelper->getSources($input);
        $webroot     = $this->argsHelper->getWebroot($input);

        $compileDir  = $input->getOption(self::OPTION_COMPILE_DIR);
        if ($compileDir == self::OPTION_COMPILE_DIR_DEFAULT) {
            $compileDir = str_replace('<webroot>', $webroot, $compileDir);
            if (!is_dir($compileDir)) {
                mkdir($compileDir, 0750, true);
            }
        }
        if (!Path::isAbsolute($compileDir)) {
            $compileDir = Path::prepend($compileDir, getcwd());
        }
        $yuicPath = $input->getOption(self::OPTION_YUIC);
        $javaPath = 'java';

        $prefix = $input->getOption(self::OPTION_PREFIX);
        if ($prefix == self::OPTION_PREFIX_DEFAULT) {
            $prefix = true;
        }

        $isReplace = $input->getOption(self::OPTION_WRITE_REPLACE);

        $output->writeln("Processing sources:\n\t<info>". implode("</info>\n\t<info>", $sourceFiles) . "</info>");
        $output->writeln("Compile directory: <info>{$compileDir}</info>");
        $output->writeln("Web root: <info>{$webroot}</info>");

        /** @var ModeFactoryInterface[] $modes */
        $modes = array();
        foreach ($input->getOption(self::OPTION_MODES) as $mode) {
            switch ($mode) {
                case 'js':
                    $modes[] = new JsMode($yuicPath, $javaPath, $compileDir, $webroot);
                    break;
                default:
                    throw new \RuntimeException("Unsupported mode {$mode}");
            }
        }

        if (empty($modes)) {
            throw new \RuntimeException("You must specify at least one mode.");
        }

        foreach ($modes as $mode) {
            $this->runMode($mode, $output, $isReplace, $sourceFiles, $webroot, $prefix, $compileDir);
        }
    }

    /**
     * @param ModeFactoryInterface $mode
     * @param OutputInterface      $output
     * @param bool                 $isReplace
     * @param array                $sourceFiles
     * @param string               $webroot
     * @param string               $prefix
     * @param string               $compileDir
     */
    private function runMode(ModeFactoryInterface $mode, OutputInterface $output, $isReplace, $sourceFiles, $webroot, $prefix, $compileDir)
    {
        $chunkManager = $mode->getChunkManager();
        $resolver     = $mode->getAssetResolver();
        $compiler     = $mode->getCompiler();
        $generator    = $mode->getTagGenerator();

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

            $resourcePrefix = $this->getResourcePrefix($prefix, $compileDir, $webroot);

            if ($resourcePrefix === false) {
                throw new \RuntimeException("Compile dir is not under web root, though you must specify "
                                            . "--" . self::OPTION_PREFIX
                                            . " option.");
            }

            $compiledSrc = Path::prepend(basename($compileFile), $resourcePrefix);
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

    private function getResourcePrefix($desiredPrefix, $compileDir, $webroot)
    {
        if ($desiredPrefix === true) {
            $compileDir = realpath($compileDir);
            $webroot = realpath($webroot);

            if (strpos($webroot, $compileDir) === 0) {
                $desiredPrefix = str_replace($webroot, '', $compileDir);
            } else {
                return false;
            }
        }

        return $desiredPrefix;
    }
}
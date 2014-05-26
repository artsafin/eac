<?php

namespace Eprst\Eac\Command;

use Eprst\Eac\Command\Helper\CommonArgsHelper;
use Eprst\Eac\Service\AssetCompiler;
use Eprst\Eac\Service\Extractor\SgmlCommentChunk;
use Eprst\Eac\Service\Extractor\XPathTagExtractor;
use Eprst\Eac\Service\Path;
use Eprst\Eac\Service\ScriptTagGenerator;
use Eprst\Eac\Service\SgmlTagAssetResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class CompileCommand extends Command
{
    const OPTION_COMPILE_DIR = 'out';
    const OPTION_COMPILE_DIR_DEFAULT = 'web root';

    const OPTION_PREFIX = 'prefix';
    const OPTION_PREFIX_DEFAULT = 'smart choice';

    const OPTION_YUIC = 'yuicompressor';

    /**
     * @var CommonArgsHelper
     */
    private $argsHelper;

    protected function configure()
    {
        $this->argsHelper = new CommonArgsHelper();

        $this
            ->setName('compile')
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
                         'Prefix for compiled filenames',
                         self::OPTION_PREFIX_DEFAULT)
             ->addOption(self::OPTION_YUIC,
                         null,
                         InputOption::VALUE_REQUIRED,
                         'YUI Compressor jar',
                         'yuicompressor.jar');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sourceFiles = $this->argsHelper->getSources($input);
        $webroot     = $this->argsHelper->getWebroot($input);

        $compileDir  = $input->getOption(self::OPTION_COMPILE_DIR);
        if ($compileDir == self::OPTION_COMPILE_DIR_DEFAULT) {
            $compileDir = $webroot;
        }
        if (!Path::isAbsolute($compileDir)) {
            $compileDir = Path::prepend($compileDir, getcwd());
        }
        $yuicPath    = $input->getOption(self::OPTION_YUIC);

        $prefix = $input->getOption(self::OPTION_PREFIX);
        if ($prefix == self::OPTION_PREFIX_DEFAULT) {
            $prefix = true;
        }

        $output->writeln("<info>Processing sources:</info>\n\t". implode("\n\t", $sourceFiles));
        $output->writeln("<info>Compile directory:</info> ". $compileDir);
        $output->writeln("<info>Web root:</info> ". $webroot);

        $chunkManager = new SgmlCommentChunk();

        $resolver = new SgmlTagAssetResolver($chunkManager, new XPathTagExtractor('//script'), 'src');
        $assetsData = $resolver->resolveAssets($sourceFiles, $webroot);

        $assetCompiler = new AssetCompiler($compileDir, $yuicPath, 'java');

        $assetTag = new ScriptTagGenerator();

        foreach ($assetsData as $sourceIdentifier => $assetFiles) {

            if (empty($assetFiles)) {
                continue;
            }

            $compileFile = $assetCompiler->compile($assetFiles, array('js_compressor'), 'js');

            if (file_exists($compileFile)) {
                $output->writeln("Asset for <info>{$sourceIdentifier}</info>: <info>{$compileFile}</info>");
            } else {
                $output->writeln("Failed to write <info>{$compileFile}</info>");
            }

            list($sourceFile, $chunkId) = explode('#', $sourceIdentifier);

            $tag = $assetTag->generate($compileFile, $webroot, $prefix);

            $chunkManager->replaceChunk(file_get_contents($sourceFile), $chunkId, $tag);
        }
    }
}
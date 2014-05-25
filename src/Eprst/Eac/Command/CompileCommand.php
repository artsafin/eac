<?php

namespace Eprst\Eac\Command;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\AssetManager;
use Assetic\AssetWriter;
use Assetic\Factory\AssetFactory;
use Assetic\Filter\FilterCollection;
use Assetic\Filter\Yui;
use Assetic\FilterManager;
use Eprst\Eac\Command\Helper\CommonArgsHelper;
use Eprst\Eac\Service\Extractor\XPathTagExtractor;
use Eprst\Eac\Service\Path;
use Eprst\Eac\Service\ScriptTagResolver;
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

        $resolver = new ScriptTagResolver(new XPathTagExtractor('//script'));
        $resources = $resolver->resolveResources($sourceFiles, $webroot);

        $fm = new FilterManager();
        $fm->set('js_compressor',
                 new FilterCollection(array(
                                          new Yui\JsCompressorFilter($yuicPath, 'java')
                                      )));
        $fm->set('css_compressor',
                 new FilterCollection(array(
                                          new Yui\CssCompressorFilter($yuicPath, 'java')
                                      )));

        $am = new AssetManager();

        $af = new AssetFactory($webroot);
        $af->setAssetManager($am);
        $af->setFilterManager($fm);

        $assetOptions = array(
        );

        $sourceToAssetName = array();

        $i = 0;
        foreach ($resources as $sourceFile => $compileFiles) {
            $assets = new AssetCollection();
            foreach ($compileFiles as $f) {
                $assets->add(new FileAsset($f));
            }
            $assetName = sprintf('assets_%s', $i++);
            $am->set($assetName, $assets);

            $name = $af->generateAssetName(array("@{$assetName}"), array('js_compressor')) . ".js";

            $sourceToAssetName[$sourceFile] = $name;

            $output->writeln("Asset for <info>{$sourceFile}</info>: <info>{$name}</info>");

            $asset = $af->createAsset(array("@{$assetName}"), array('js_compressor', 'css_compressor'), array(
                'name' => $name,
                'output' => '*'
            ));

            $w = new AssetWriter($compileDir);
            $w->writeAsset($asset);
        }
    }
}
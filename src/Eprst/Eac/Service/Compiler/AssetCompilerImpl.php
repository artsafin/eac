<?php


namespace Eprst\Eac\Service\Compiler;

use Assetic\AssetWriter;
use Assetic\Factory\AssetFactory;
use Assetic\Filter\Yui;
use Assetic\Filter\FilterCollection;
use Assetic\FilterManager;

class AssetCompilerImpl implements AssetCompiler
{
    /**
     * @var AssetFactory
     */
    private $af;
    private $compileDir;
    private $filters;

    public function __construct($filters, $compileDir, $webroot, $yuicPath, $javaPath)
    {
        $fm = new FilterManager();
        $fm->set('js_compressor',
                 new FilterCollection(array(
                                          new Yui\JsCompressorFilter($yuicPath, $javaPath)
                                      )));
        $fm->set('css_compressor',
                 new FilterCollection(array(
                                          new Yui\CssCompressorFilter($yuicPath, $javaPath)
                                      )));

        $this->af = new AssetFactory($webroot);

        $this->compileDir = $compileDir;
        $this->filters = $filters;
    }

    public function compile($assetFiles)
    {
        $compileFile = $this->af->generateAssetName($assetFiles, $this->filters);

        $asset = $this->af->createAsset($assetFiles,
                                        $this->filters,
                                        array(
                                            'name'   => $compileFile,
                                            'output' => '*'
                                        ));

        $w = new AssetWriter($this->compileDir);
        $w->writeAsset($asset);

        $assetFullPath = Path::append($this->compileDir, $asset->getTargetPath());

        return $assetFullPath;
    }
} 
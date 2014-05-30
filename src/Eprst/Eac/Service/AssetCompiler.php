<?php


namespace Eprst\Eac\Service;

use Assetic\AssetManager;
use Assetic\AssetWriter;
use Assetic\Factory\AssetFactory;
use Assetic\Filter\Yui;
use Assetic\Filter\FilterCollection;
use Assetic\FilterManager;

class AssetCompiler
{
    /**
     * @var AssetFactory
     */
    private $af;
    private $compileDir;

    public function __construct($compileDir, $webroot, $yuicPath, $javaPath)
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

        $am = new AssetManager();

        $this->af = new AssetFactory($webroot);
        $this->af->setAssetManager($am);
        $this->af->setFilterManager($fm);

        $this->compileDir = $compileDir;
    }

    public function compile($assetFiles, $compressors)
    {
        $compileFile = $this->af->generateAssetName($assetFiles, $compressors);

        $asset = $this->af->createAsset($assetFiles,
                                        $compressors,
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
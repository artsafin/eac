<?php


namespace Eprst\Eac\Service\Compiler;

use Assetic\AssetWriter;
use Assetic\Factory\AssetFactory;
use Assetic\Filter\FilterCollection;
use Assetic\FilterManager;
use Eprst\Eac\Service\Path;

class AssetCompilerImpl implements AssetCompiler
{
    /**
     * @var AssetFactory
     */
    private $af;
    private $compileDir;

    public function __construct($filterObjs, $compileDir, $webroot)
    {
        $fm = new FilterManager();
        $fm->set('filters', new FilterCollection($filterObjs));

        $this->af = new AssetFactory($webroot);
        $this->af->setFilterManager($fm);

        $this->compileDir = $compileDir;
    }

    public function compile($assetFiles)
    {
        $compileFile = $this->af->generateAssetName($assetFiles, 'filters');

        $asset = $this->af->createAsset($assetFiles,
                                        'filters',
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
<?php

namespace Eprst\Eac\Service\AssetResolver;

class ChunkData
{
    public $sourceFile = '';

    public $id = 0;

    public $attributes = array();

    public $assets = array();

    function __construct($sourceFile, $id, $attrs)
    {
        $this->sourceFile = $sourceFile;
        $this->id         = $id;
        $this->attributes = $attrs;
    }

    public function addUniqueAsset($asset)
    {
        if (!in_array($asset, $this->assets)) {
            $this->assets[] = $asset;
        }
    }

    public function getName()
    {
        return sprintf('%s#%s', $this->sourceFile, $this->id);
    }
} 
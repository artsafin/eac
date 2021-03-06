<?php

namespace Eprst\Eac\Factory;

use Eprst\Eac\Service\AssetResolver\AssetResolverInterface;
use Eprst\Eac\Service\Chunk\ChunkManagerInterface;
use Eprst\Eac\Service\Compiler\AssetCompiler;
use Eprst\Eac\Service\TagGenerator\TagGeneratorInterface;
use Eprst\Eac\Service\TagReader\TagReaderInterface;

interface PassInterface
{
    /**
     * @return ChunkManagerInterface
     */
    public function getChunkManager();

    /**
     * @return AssetResolverInterface
     */
    public function getAssetResolver();

    /**
     * @return AssetCompiler
     */
    public function getCompiler();

    /**
     * @return TagGeneratorInterface
     */
    public function getTagGenerator();
}
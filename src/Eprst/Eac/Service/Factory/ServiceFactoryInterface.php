<?php

namespace Eprst\Eac\Service\Factory;

use Eprst\Eac\Service\AssetResolver\AssetResolverInterface;
use Eprst\Eac\Service\Chunk\ChunkManagerInterface;
use Eprst\Eac\Service\Compiler\AssetCompiler;
use Eprst\Eac\Service\TagReader\TagReaderInterface;
use Eprst\Eac\Service\TagWriter\TagGeneratorInterface;

interface ServiceFactoryInterface
{
    /**
     * @return ChunkManagerInterface
     */
    public function getChunkManager();

    /**
     * @return TagReaderInterface
     */
    public function getTagReader();

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
<?php


namespace Eprst\Eac\Service\AssetResolver;


interface AssetResolverInterface
{
    /**
     * Return a list of absolute paths to script files
     *
     * @param array  $files
     * @param string $root
     *
     * @return ChunkData[]
     */
    public function resolve($files, $root);
} 
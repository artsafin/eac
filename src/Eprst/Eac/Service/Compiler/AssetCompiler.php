<?php


namespace Eprst\Eac\Service\Compiler;


interface AssetCompiler
{
    /**
     * Compile multiple files into one
     *
     * @param array $files Files to be compiled into single file
     *
     * @return string Full path to a single compiled file
     */
    public function compile($files);
} 
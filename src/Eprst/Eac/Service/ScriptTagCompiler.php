<?php

namespace Eprst\Eac\Service;

use Eprst\Eac\Service\Extractor\ExtractorInterface;
use Symfony\Component\Finder\Finder;

class ScriptTagCompiler
{
    /**
     * @var ExtractorInterface
     */
    private $extractor;

    public function __construct(ExtractorInterface $extractor = null)
    {
        $this->extractor = $extractor;
    }

    public function getCompileFileNames($files, $root)
    {
        $compileFiles = array();

        foreach ($files as $file) {

            $compileFiles[$file] = array();

            $text = file_get_contents($file);

            /** @var ScriptTagDto[] $tags */
            $tags = $this->extractor->extract($text);

            foreach ($tags as $t) {
                $path = new Path($t->src);
                $compileFiles[$file][] = $path->prepend($root);
            }
        }

        return $compileFiles;
    }

    public function compile($glob, $relative, $compileDir)
    {
        $sources = $this->getCompileFileNames($glob, $relative);
    }
} 
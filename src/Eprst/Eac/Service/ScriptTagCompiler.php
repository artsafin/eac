<?php

namespace Eprst\Eac\Service;

use Eprst\Eac\Service\Extractor\ExtractorInterface;

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
                $f = $path->prepend($root);
                if (file_exists($f)) {
                    $compileFiles[$file][] = $f;
                }
            }

            $compileFiles[$file] = array_unique($compileFiles[$file]);
        }

        return $compileFiles;
    }

    public function compile($glob, $relative, $compileDir)
    {
        $sources = $this->getCompileFileNames($glob, $relative);
    }
} 
<?php

namespace Eprst\Eac\Service;

use Eprst\Eac\Service\Extractor\ChunkManagerInterface;
use Eprst\Eac\Service\Extractor\TagExtractorInterface;

class SgmlTagAssetResolver
{
    /**
     * @var TagExtractorInterface
     */
    private $extractor;

    /**
     * @var ChunkManagerInterface
     */
    private $chunkManager;

    /**
     * @var string
     */
    private $tagAttribute;

    /**
     * @param ChunkManagerInterface $chunkManager
     * @param TagExtractorInterface $extractor
     * @param string                $tagAttribute Tag attribute which contains asset URI
     */
    public function __construct(ChunkManagerInterface $chunkManager, TagExtractorInterface $extractor, $tagAttribute)
    {
        $this->extractor    = $extractor;
        $this->chunkManager = $chunkManager;
        $this->tagAttribute = $tagAttribute;
    }

    /**
     * Return a list of absolute paths to script files
     *
     * @param array $files
     * @param string $root
     *
     * @return array
     */
    public function resolveAssets($files, $root)
    {
        $compileFiles = array();

        foreach ($files as $file) {

            $compileFiles[$file] = array();

            $text = file_get_contents($file);

            $tags = $this->extractor->extract($text);

            foreach ($tags as $t) {
                if (empty($t[$this->tagAttribute])) {
                    continue;
                }
                $f = Path::prepend($t[$this->tagAttribute], $root);
                if (file_exists($f)) {
                    $compileFiles[$file][] = $f;
                }
            }

            $compileFiles[$file] = array_unique($compileFiles[$file]);
        }

        return $compileFiles;
    }
} 
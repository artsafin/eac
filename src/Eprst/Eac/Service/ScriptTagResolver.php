<?php

namespace Eprst\Eac\Service;

use Eprst\Eac\Service\Extractor\ExtractorInterface;

class ScriptTagResolver
{
    /**
     * @var ExtractorInterface
     */
    private $extractor;

    public function __construct(ExtractorInterface $extractor = null)
    {
        $this->extractor = $extractor;
    }

    /**
     * Return a list of absolute paths to script files
     *
     * @param array $files
     * @param string $root
     *
     * @return array
     */
    public function resolveResources($files, $root)
    {
        $compileFiles = array();

        foreach ($files as $file) {

            $compileFiles[$file] = array();

            $text = file_get_contents($file);

            /** @var ScriptTagDto[] $tags */
            $tags = $this->extractor->extract($text);

            foreach ($tags as $t) {
                if (empty($t['src'])) {
                    continue;
                }
                $f = Path::prepend($t['src'], $root);
                if (file_exists($f)) {
                    $compileFiles[$file][] = $f;
                }
            }

            $compileFiles[$file] = array_unique($compileFiles[$file]);
        }

        return $compileFiles;
    }
} 
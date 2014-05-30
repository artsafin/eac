<?php

namespace Eprst\Eac\Service\AssetResolver;

use Eprst\Eac\Service\Chunk\ChunkManagerInterface;
use Eprst\Eac\Service\Path;
use Eprst\Eac\Service\TagReader\TagReaderInterface;

class SgmlTagAssetResolver implements AssetResolverInterface
{
    /**
     * @var TagReaderInterface
     */
    private $reader;

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
     * @param TagReaderInterface $reader
     * @param string                $tagAttribute Tag attribute which contains asset URI
     */
    public function __construct(ChunkManagerInterface $chunkManager, TagReaderInterface $reader, $tagAttribute)
    {
        $this->reader    = $reader;
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

            $text = file_get_contents($file);

            $chunks = $this->chunkManager->extractChunks($text);

            foreach ($chunks as $chunkId => $chunkData) {

                list($chunkText, $chunkAttrs) = $chunkData;

                $sourceId = sprintf("%s#%s", $file, $chunkId);

                $compileFiles[$sourceId] = array();

                $tags = $this->reader->read($chunkText);

                foreach ($tags as $t) {
                    if (empty($t[$this->tagAttribute])) {
                        continue;
                    }
                    $resourceRef = $t[$this->tagAttribute];
                    if (Path::isRemote($resourceRef)) {
                        $compileFiles[$sourceId][] = $resourceRef;
                    } else {
                        $localFile = Path::prepend($resourceRef, $root);
                        if (file_exists($localFile)) {
                            $compileFiles[$sourceId][] = $localFile;
                        }
                    }
                }
                $compileFiles[$sourceId] = array_unique($compileFiles[$sourceId]);
            }
        }

        return $compileFiles;
    }
} 
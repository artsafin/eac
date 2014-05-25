<?php


namespace Eprst\Eac\Service\Extractor;


interface ChunkManagerInterface
{
    /**
     * @param $text
     *
     * @return array
     */
    public function extractChunks($text);

    /**
     * @param string $text
     * @param int    $chunkId
     * @param string $replacement
     *
     * @return string
     */
    public function replaceChunk($text, $chunkId, $replacement);
} 
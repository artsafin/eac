<?php


namespace Eprst\Eac\Service;


class SgmlCommentScope
{
    private $commentIdentifier;

    public function __construct($commentIdentifier = 'eac:compile')
    {
        $this->commentIdentifier = $commentIdentifier;
    }

    private function buildRegexp()
    {
        return sprintf('<!--\s*?%s\s*?-->(.*?)<!--\s*?\/%s\s*?-->', $this->commentIdentifier, $this->commentIdentifier);
    }

    public function extractChunks($fromText)
    {
        $re = sprintf("/%s/", $this->buildRegexp());

        $results = array();

        if (preg_match_all($re, $fromText, $matches, PREG_SET_ORDER)) {
            $results = array_map(function($item){
                return $item[1];
            }, $matches);
        }
        return $results;
    }

    public function replaceChunk($text, $chunkReplacements)
    {
        $re = sprintf("/%s/", $this->buildRegexp());

        if (!preg_match_all($re, $text, $matches, PREG_OFFSET_CAPTURE)) {
            return $text;
        }

        foreach ($chunkReplacements as $chunkId => $replaceWithText) {
            if (!isset($matches[$chunkId])) {
                continue;
            }
            list($match, $offset) = $matches[$chunkId];
            $len = mb_strlen($match);

            $text = substr($text, 0, $offset) . $replaceWithText . substr($text, $offset + $len);
        }

        return $text;
    }
} 
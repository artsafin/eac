<?php


namespace Eprst\Eac\Service\Extractor;

class SgmlCommentChunk implements ChunkManagerInterface
{
    private $commentIdentifier;

    public function __construct($commentIdentifier = 'eac:compile')
    {
        $this->commentIdentifier = $commentIdentifier;
    }

    private function buildRegexp()
    {
        return sprintf('<!--\s*?%s(.*?(?=-->))-->(.*?)<!--\s*?\/%s\s*?-->', $this->commentIdentifier, $this->commentIdentifier);
    }

    public function extractChunks($fromText)
    {
        $re = sprintf("/%s/ms", $this->buildRegexp());

        $results = array();

        if (preg_match_all($re, $fromText, $matches, PREG_SET_ORDER)) {
            $results = array_map(function($item){
                if ($argsStr = trim($item[1])) {
                    $argsParsed = array_reduce(explode(',', $argsStr), function(&$acc, $item){
                        $keyVal = array_map('trim', explode('=', $item, 2));
                        if (count($keyVal) > 1) {
                            $acc[$keyVal[0]] = $keyVal[1];
                        } else if (!empty($keyVal[0])) {
                            $acc[$keyVal[0]] = true;
                        }
                        return $acc;
                    }, array());
                } else {
                    $argsParsed = array();
                }

                $chunk = $item[2];
                return array($chunk, $argsParsed);
            }, $matches);
        }
        return $results;
    }

    public function replaceChunk($text, $chunkId, $replaceWithText)
    {
        $re = sprintf("/%s/", $this->buildRegexp());

        if (!preg_match_all($re, $text, $matches, PREG_OFFSET_CAPTURE)) {
            return $text;
        }

        if (!isset($matches[$chunkId])) {
            return $text;
        }

        list($match, $offset) = $matches[$chunkId];
        $len = mb_strlen($match);

        $text = substr($text, 0, $offset) . $replaceWithText . substr($text, $offset + $len);

        return $text;
    }
} 
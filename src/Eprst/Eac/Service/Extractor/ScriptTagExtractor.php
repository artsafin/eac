<?php

namespace Eprst\Eac\Service\Extractor;

use DOMDocument;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use Eprst\Eac\Service\ScriptTagDto;

class ScriptTagExtractor implements ExtractorInterface
{
    public function __construct()
    {
    }

    public function extract($text)
    {
        $doc = new DOMDocument();
        $doc->loadHTML($text);

        $xpath = new DOMXPath($doc);
        /** @var DOMNodeList|DomNode[] $nodes */
        $nodes = $xpath->query('//script');

        if (!$nodes->length) {
            return array();
        }

        $result = array();

        foreach ($nodes as $node) {
            $tag = new ScriptTagDto();
            foreach ($node->attributes as $attr) {
                $tag->{$attr->name} = (string) $attr->value;
            }
            $result[] = $tag;
        }

        return $result;
    }
}
<?php

namespace Eprst\Eac\Service\Extractor;

use DOMDocument;
use DOMNode;
use DOMNodeList;
use DOMXPath;

class XPathTagExtractor implements TagExtractorInterface
{
    /**
     * @var string
     */
    private $xpath;

    public function __construct($xpath)
    {
        $this->xpath = $xpath;
    }

    /**
     * {@inheritDoc}
     */
    public function extract($text)
    {
        $doc = new DOMDocument();
        libxml_use_internal_errors(true);

        $doc->loadHTML($text);

        $xpath = new DOMXPath($doc);
        /** @var DOMNodeList|DomNode[] $nodes */
        $nodes = $xpath->query($this->xpath);

        if (!$nodes->length) {
            return array();
        }

        $result = array();

        foreach ($nodes as $node) {
            $tag = array();
            foreach ($node->attributes as $attr) {
                $tag[$attr->name] = (string) $attr->value;
            }
            $result[] = $tag;
        }

        libxml_clear_errors();

        return $result;
    }
}
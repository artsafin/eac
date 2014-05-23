<?php

namespace Eprst\Eac\Service\Parser;

use DOMDocument;
use DOMXPath;
use Eprst\Eac\Service\Extractor\ExtractorInterface;

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
        $nodes = $xpath->query('//script');

    }
}
<?php


namespace Eprst\Eac\Service\Extractor;


interface ExtractorInterface
{
    /**
     * @param $text
     *
     * @return array
     */
    public function extract($text);
} 
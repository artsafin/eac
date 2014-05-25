<?php


namespace Eprst\Eac\Service\Extractor;


interface TagExtractorInterface
{
    /**
     * @param $text
     *
     * @return array
     */
    public function extract($text);
} 
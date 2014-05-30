<?php


namespace Eprst\Eac\Service\TagReader;


interface TagReaderInterface
{
    /**
     * @param $text
     *
     * @return array
     */
    public function read($text);
} 
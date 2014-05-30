<?php


namespace Eprst\Eac\Service\TagWriter;


interface TagGeneratorInterface
{
    /**
     * @param string $compiled
     *
     * @return string
     */
    public function generate($compiled);
} 
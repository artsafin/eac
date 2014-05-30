<?php


namespace Eprst\Eac\Service\TagWriter;


interface TagGeneratorInterface
{
    /**
     * @param array $attributes
     *
     * @return string
     */
    public function generate($attributes);
} 
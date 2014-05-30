<?php


namespace Eprst\Eac\Service\TagGenerator;


interface TagGeneratorInterface
{
    /**
     * @param string $compiled
     *
     * @return string
     */
    public function generate($compiled);
} 
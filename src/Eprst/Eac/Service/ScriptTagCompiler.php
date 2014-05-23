<?php

namespace Eprst\Eac\Service;

use Eprst\Eac\Service\Extractor\ExtractorInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ScriptTagCompiler
{
    /**
     * @var ExtractorInterface
     */
    private $extractor;

    public function __construct(ExtractorInterface $extractor = null)
    {
        $this->extractor = $extractor;
    }

    public function compile($glob)
    {
        /** @var SplFileInfo[] $files */
        $files = Finder::create()->files()->name($glob)->depth(0)->in(getcwd());

        foreach ($files as $file) {
            echo $file->getRealPath();
        }
    }
} 
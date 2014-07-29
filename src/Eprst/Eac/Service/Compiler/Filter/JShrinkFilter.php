<?php

namespace Eprst\Eac\Service\Compiler\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

class JShrinkFilter implements FilterInterface
{
    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $asset->setContent(\JShrink\Minifier::minify($asset->getContent()));
    }
}

<?php

namespace Eprst\Eac\Factory;

use Eprst\Eac\Service\AssetResolver\AssetResolverInterface;
use Eprst\Eac\Service\AssetResolver\HtmlTagAssetResolver;
use Eprst\Eac\Service\Chunk\ChunkManagerInterface;
use Eprst\Eac\Service\Chunk\HtmlCommentChunk;
use Eprst\Eac\Service\Compiler\AssetCompiler;
use Eprst\Eac\Service\TagGenerator\ScriptTagGenerator;
use Eprst\Eac\Service\TagGenerator\TagGeneratorInterface;
use Eprst\Eac\Service\TagReader\XPathTagReader;

class JsPass implements PassInterface
{

    /**
     * jsCompiler
     *
     * @var AssetCompiler
     */
    private $jsCompiler;

    /**
     * @param AssetCompiler $jsCompiler
     */
    function __construct(AssetCompiler $jsCompiler)
    {
        $this->chunkIdent    = 'eac:compile';
        $this->scriptXpath   = '//script';
        $this->scriptSrcAttr = 'src';
        $this->jsCompiler = $jsCompiler;
    }

    /**
     * @return ChunkManagerInterface
     */
    public function getChunkManager()
    {
        return new HtmlCommentChunk($this->chunkIdent);
    }

    /**
     * @return AssetResolverInterface
     */
    public function getAssetResolver()
    {
        return new HtmlTagAssetResolver($this->getChunkManager(),
                                        new XPathTagReader($this->scriptXpath),
                                        $this->scriptSrcAttr);
    }

    /**
     * @return AssetCompiler
     */
    public function getCompiler()
    {
        return $this->jsCompiler;
    }

    /**
     * @return TagGeneratorInterface
     */
    public function getTagGenerator()
    {
        return new ScriptTagGenerator();
    }
}
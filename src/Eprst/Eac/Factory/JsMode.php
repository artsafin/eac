<?php

namespace Eprst\Eac\Factory;

use Eprst\Eac\Service\AssetResolver\AssetResolverInterface;
use Eprst\Eac\Service\AssetResolver\HtmlTagAssetResolver;
use Eprst\Eac\Service\Chunk\ChunkManagerInterface;
use Eprst\Eac\Service\Chunk\HtmlCommentChunk;
use Eprst\Eac\Service\Compiler\AssetCompiler;
use Eprst\Eac\Service\Compiler\AsseticCompiler;
use Eprst\Eac\Service\Compiler\Filter\JShrinkFilter;
use Eprst\Eac\Service\TagGenerator\ScriptTagGenerator;
use Eprst\Eac\Service\TagGenerator\TagGeneratorInterface;
use Eprst\Eac\Service\TagReader\TagReaderInterface;
use Eprst\Eac\Service\TagReader\XPathTagReader;

class JsMode implements ModeFactoryInterface
{
    /**
     * @var
     */
    private $compileDir;
    /**
     * @var
     */
    private $webRoot;

    function __construct($compileDir, $webRoot)
    {
        $this->chunkIdent    = 'eac:compile';
        $this->scriptXpath   = '//script';
        $this->scriptSrcAttr = 'src';
        $this->compileDir    = $compileDir;
        $this->webRoot       = $webRoot;
    }

    /**
     * @return ChunkManagerInterface
     */
    public function getChunkManager()
    {
        return new HtmlCommentChunk($this->chunkIdent);
    }

    /**
     * @return TagReaderInterface
     */
    public function getTagReader()
    {
        return new XPathTagReader($this->scriptXpath);
    }

    /**
     * @return AssetResolverInterface
     */
    public function getAssetResolver()
    {
        return new HtmlTagAssetResolver($this->getChunkManager(), $this->getTagReader(), $this->scriptSrcAttr);
    }

    /**
     * @return AssetCompiler
     */
    public function getCompiler()
    {
        $filters = array(
            new JShrinkFilter()
        );

        return new AsseticCompiler($filters, $this->compileDir, $this->webRoot);
    }

    /**
     * @return TagGeneratorInterface
     */
    public function getTagGenerator()
    {
        return new ScriptTagGenerator();
    }
}
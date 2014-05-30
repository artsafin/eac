<?php

namespace Eprst\Eac\Factory;

use Eprst\Eac\Service\AssetResolver\AssetResolverInterface;
use Eprst\Eac\Service\AssetResolver\SgmlTagAssetResolver;
use Eprst\Eac\Service\Chunk\ChunkManagerInterface;
use Eprst\Eac\Service\Chunk\SgmlCommentChunk;
use Eprst\Eac\Service\Compiler\AssetCompiler;
use Eprst\Eac\Service\Compiler\AssetCompilerImpl;
use Eprst\Eac\Service\TagGenerator\ScriptTagGenerator;
use Eprst\Eac\Service\TagGenerator\TagGeneratorInterface;
use Eprst\Eac\Service\TagReader\TagReaderInterface;
use Eprst\Eac\Service\TagReader\XPathTagReader;
use Assetic\Filter\Yui;

class JsMode implements ModeFactoryInterface
{
    /**
     * @var
     */
    private $yuicPath;
    /**
     * @var
     */
    private $javaPath;
    /**
     * @var
     */
    private $compileDir;
    /**
     * @var
     */
    private $webRoot;

    function __construct($yuicPath, $javaPath, $compileDir, $webRoot)
    {
        $this->chunkIdent    = 'eac:compile';
        $this->scriptXpath   = '//script';
        $this->scriptSrcAttr = 'src';
        $this->yuicPath      = $yuicPath;
        $this->javaPath      = $javaPath;
        $this->compileDir    = $compileDir;
        $this->webRoot       = $webRoot;
    }

    /**
     * @return ChunkManagerInterface
     */
    public function getChunkManager()
    {
        return new SgmlCommentChunk($this->chunkIdent);
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
        return new SgmlTagAssetResolver($this->getChunkManager(), $this->getTagReader(), $this->scriptSrcAttr);
    }

    /**
     * @return AssetCompiler
     */
    public function getCompiler()
    {
        $filters = array(
            new Yui\JsCompressorFilter($this->yuicPath, $this->javaPath)
        );

        return new AssetCompilerImpl($filters, $this->compileDir, $this->webRoot);
    }

    /**
     * @return TagGeneratorInterface
     */
    public function getTagGenerator()
    {
        return new ScriptTagGenerator();
    }
}
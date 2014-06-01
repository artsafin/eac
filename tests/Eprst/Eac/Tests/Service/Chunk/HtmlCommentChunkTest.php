<?php

namespace Eprst\Eac\Tests\Service\Chunk;

use Eprst\Eac\Service\Chunk\HtmlCommentChunk;

class HtmlCommentChunkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HtmlCommentChunk
     */
    private $obj;

    protected function setUp()
    {
        $this->obj = new HtmlCommentChunk();
    }

    public function testExtractChunks()
    {
        $text = file_get_contents(__DIR__ . '/extract_chunks_test.html');

        $chunks = $this->obj->extractChunks($text);

        self::assertCount(8, $chunks);

        list($chunkText, $chunkAttrs) = $chunks[0];
        self::assertEquals("\n    Chunk 1\n    <script type=\"text/javascript\" src=\"/js/calculate.js\"></script>\n    End of chunk 1\n", $chunkText);
        self::assertEquals(array(), $chunkAttrs);

        list($chunkText, $chunkAttrs) = $chunks[1];
        self::assertEquals("\nChunk 2\n<script type=\"text/javascript\" src=\"/js/calculate.js\"></script>\nEnd of chunk 2\n", $chunkText);
        self::assertEquals(array(), $chunkAttrs);

        list($chunkText, $chunkAttrs) = $chunks[2];
        self::assertEquals("Chunk 3 in one line", $chunkText);
        self::assertEquals(array(), $chunkAttrs);

        list($chunkText, $chunkAttrs) = $chunks[3];
        self::assertEquals("Chunk 4 after chunk 3", $chunkText);
        self::assertEquals(array(), $chunkAttrs);

        list($chunkText, $chunkAttrs) = $chunks[4];
        self::assertEquals("", $chunkText);
        self::assertEquals(array(), $chunkAttrs);

        list($chunkText, $chunkAttrs) = $chunks[5];
        self::assertEquals("", $chunkText);
        self::assertEquals(array(
                               'bool_attr' => true,
                               'attribute' => 'value',
                               'number'    => 1,
                           ),
                           $chunkAttrs);

        list($chunkText, $chunkAttrs) = $chunks[6];
        self::assertEquals("", $chunkText);
        self::assertEquals(array(
                               'one_attribute_set' => true,
                           ),
                           $chunkAttrs);

        list($chunkText, $chunkAttrs) = $chunks[7];
        self::assertEquals("\nChunk 8\n", $chunkText);
        self::assertEquals(array(
                               'indented_and_multiline' => 'true',
                               'attributes' => 'yes',
                               'are' => "possible\nforsure",
                               'butdelimiter' => 'alwayscomma',
                           ),
                           $chunkAttrs);
    }

    public function testReplaceChunk()
    {
        $text = file_get_contents(__DIR__ . '/extract_chunks_test.html');

        $text = $this->obj->replaceChunk($text, 0, 'chunk 1 replaced');
        $text = $this->obj->replaceChunk($text, 1, 'chunk 2 replaced');
        $text = $this->obj->replaceChunk($text, 2, 'chunk 3 replaced');
        $text = $this->obj->replaceChunk($text, 3, 'chunk 4 replaced');
        $text = $this->obj->replaceChunk($text, 4, 'chunk 5 replaced');
        $text = $this->obj->replaceChunk($text, 5, 'chunk 6 replaced');
        $text = $this->obj->replaceChunk($text, 6, 'chunk 7 replaced');
        $text = $this->obj->replaceChunk($text, 7, 'chunk 8 replaced');

        $expected = file_get_contents(__DIR__ . '/extract_chunks_test_replaced.html');

        self::assertEquals($expected, $text);
    }
}
 
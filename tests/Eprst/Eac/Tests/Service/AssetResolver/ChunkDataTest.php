<?php


namespace Eprst\Eac\Tests\Service\AssetResolver;


use Eprst\Eac\Service\AssetResolver\ChunkData;

class ChunkDataTest extends \PHPUnit_Framework_TestCase
{
    public function testUniqueAdd()
    {
        $chunkData = new ChunkData('source_file', 'id', array('whatever'));

        $chunkData->addUniqueAsset('d');
        $chunkData->addUniqueAsset('b');
        $chunkData->addUniqueAsset('c');
        $chunkData->addUniqueAsset('d');
        $chunkData->addUniqueAsset('a');

        self::assertEquals(array('d', 'b', 'c', 'a'), $chunkData->assets);
    }

    public function testGetName()
    {
        $chunkData = new ChunkData('source_file/path/relative', 'some-id', array('whatever'));

        self::assertEquals('source_file/path/relative#some-id', $chunkData->getName());
    }
}
 
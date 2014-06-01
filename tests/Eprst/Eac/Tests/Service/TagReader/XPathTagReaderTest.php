<?php


namespace Eprst\Eac\Tests\Service\TagReader;


use Eprst\Eac\Service\TagReader\XPathTagReader;

class XPathTagReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testRead()
    {
        $text = file_get_contents(__DIR__ . '/common-header.html');

        $obj = new XPathTagReader('//a[@href]');

        $tags = $obj->read($text);

        self::assertCount(5, $tags);

        $expectedHrefs = array(
            '/css/jquery.lightbox-0.5.css',
            '/js/jquery.lightbox-0.5.min.js',
            '/js/jquery.gomap-1.3.2.min.js',
            '/js/jquery-1.5.min.js',
            '/js/jquery.lightbox-0.5.min.js',
        );

        $hrefs = array_values(array_filter(array_map(function($item){
            return isset($item['href']) ? $item['href'] : null;
        }, $tags)));

        self::assertEquals($expectedHrefs, $hrefs);
    }
}
 
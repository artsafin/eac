<?php


namespace Eprst\Eac\Tests\Service;

use Eprst\Eac\Service\Path;

class PathTest extends \PHPUnit_Framework_TestCase
{
    public function remoteDataProvider()
    {
        return array(
            array('//code.jquery.com/foo.bar.js', true),
            array('http://code.jquery.com/foo.bar.js', true),
            array('https://code.jquery.com/foo.bar.js', true),
            array('ssh://code.jquery.com/foo.bar.js', true),
            array('whatever://code.jquery.com/foo.bar.js', true),
            array('code.jquery.com/foo.bar.js', false),
            array('/absoulte/local/foo.bar.js', false),
            array('relative/local/foo.bar.js', false),
        );
    }

    public function prependDataProvider()
    {
        $_ = DIRECTORY_SEPARATOR;

        return array(
            //Path::prepend (
            //    $basePath                         $path1, [$pathN], [$pathM]         )     ===    PHPUnit Expected Result
            array('//code.jquery.com/foo.bar.js',   'prepend/whatever/path',    '/maybe-absolute',  '//code.jquery.com/foo.bar.js'),
            array('/absolute/local/foo.bar.js',     'not-absolute',                                 "not-absolute{$_}absolute/local/foo.bar.js"),
            array('/absolute/local/foo.bar.js',     'not-absolute/',                                "not-absolute{$_}absolute/local/foo.bar.js"),
            array('/absolute/local/foo.bar.js',     'not-absolute\\',                               "not-absolute{$_}absolute/local/foo.bar.js"),
            array('\\absolute/local/foo.bar.js',    'not-absolute\\',                               "not-absolute{$_}absolute/local/foo.bar.js"),
            array('\\absolute/local\\foo.bar.js',   'not-absolute\\',                               "not-absolute{$_}absolute/local\\foo.bar.js"),
            array('\\absolute/local\\foo.bar.js',   '\\absolute\\',                                 "\\absolute{$_}absolute/local\\foo.bar.js"),

            array('relative/local/foo.bar.js',      '/etc/',                                        "/etc{$_}relative/local/foo.bar.js"),
            array('relative\\local/foo.bar.js',     '/etc/',                                        "/etc{$_}relative\\local/foo.bar.js"),
            array('relative/local/foo.bar.js',      'rel/',                                         "rel{$_}relative/local/foo.bar.js"),
            array('relative/local/foo.bar.js',      'rel',                                          "rel{$_}relative/local/foo.bar.js"),
            array('relative/local/foo.bar.js',      'rel\\',                                        "rel{$_}relative/local/foo.bar.js"),
            array('relative/local/foo.bar.js',      'first\\', 'second/', '/third',                 "first{$_}second{$_}third{$_}relative/local/foo.bar.js"),
            array('relative/local/foo.bar.js',      '/first\\', 'second/', '/third',                "/first{$_}second{$_}third{$_}relative/local/foo.bar.js"),
        );
    }

    public function appendDataProvider()
    {
        $_ = DIRECTORY_SEPARATOR;

        return array(
            //Path::append (
            //    $basePath                         $path1, [$pathN], [$pathM]         )     ===    PHPUnit Expected Result
            array('//code.jquery.com/foo.bar.js',   'append/whatever/path',    '/maybe-absolute',   "//code.jquery.com/foo.bar.js{$_}append/whatever/path{$_}maybe-absolute"),
            array('/absolute/local/foo.bar.js',     'apppend-path',                                 "/absolute/local/foo.bar.js{$_}apppend-path"),
            array('/absolute/local/foo.bar.js',     'apppend-path/',                                "/absolute/local/foo.bar.js{$_}apppend-path/"),
            array('/absolute/local/foo.bar.js',     'apppend-path\\',                               "/absolute/local/foo.bar.js{$_}apppend-path\\"),
            array('\\absolute/local/foo.bar.js',    'apppend-path\\',                               "\\absolute/local/foo.bar.js{$_}apppend-path\\"),
            array('\\absolute/local\\foo.bar.js',   'apppend-path\\',                               "\\absolute/local\\foo.bar.js{$_}apppend-path\\"),
            array('\\absolute/local\\foo.bar.js',   '\\absolute\\',                                 "\\absolute/local\\foo.bar.js{$_}absolute\\"),

            array('relative/local/foo.bar.js',      '/etc/',                                        "relative/local/foo.bar.js{$_}etc/"),
            array('relative\\local/foo.bar.js',     '\\etc/',                                       "relative\\local/foo.bar.js{$_}etc/"),
            array('relative/local/foo.bar.js',      'rel/',                                         "relative/local/foo.bar.js{$_}rel/"),
            array('relative/local/foo.bar.js',      'rel',                                          "relative/local/foo.bar.js{$_}rel"),
            array('relative/local/foo.bar.js',      'rel\\',                                        "relative/local/foo.bar.js{$_}rel\\"),
            array('relative/local/foo.bar.js',      'first\\', 'second/', '/third',                 "relative/local/foo.bar.js{$_}first{$_}second{$_}third"),
            array('relative/local/foo.bar.js',      '/first\\', 'second/', '/third\\',              "relative/local/foo.bar.js{$_}first{$_}second{$_}third\\"),
        );
    }

    public function isAbsoluteDataProvider()
    {
        return array(
            array('C:\windows\System32', true),
            array('D:\music', true),
            array('H:\work\path', true),
            array('h:\work\path', true),
            array('h:/work/path', true),
            array('h:/work\path', true),
            array('/music/beatles', true),
            array('\music/beatles', true),

            array('Windows\System32', false),
            array('work\path', false),
            array('music/beatles', false),
            array('music/beatles/', false),
            array('music/beatles\\', false),
        );
    }

    /**
     * @dataProvider remoteDataProvider
     */
    public function testIsRemote($path, $isRemote)
    {
        if ($isRemote) {
            self::assertTrue(Path::isRemote($path));
        } else {
            self::assertFalse(Path::isRemote($path));
        }
    }

    /**
     * @dataProvider prependDataProvider
     */
    public function testPrepend($basePath, $path1, $pathN = null, $expected = null)
    {
        $args = func_get_args();
        $expected = array_pop($args);

        $result = call_user_func_array(array(get_class(new Path), 'prepend'), $args);

        self::assertEquals($expected, $result);
    }

    /**
     * @dataProvider appendDataProvider
     */
    public function testAppend($basePath, $path1, $pathN = null, $expected = null)
    {
        $args = func_get_args();
        $expected = array_pop($args);

        $result = call_user_func_array(array(get_class(new Path), 'append'), $args);

        self::assertEquals($expected, $result);
    }

    /**
     * @dataProvider isAbsoluteDataProvider
     */
    public function testIsAbsolute($path, $isAbsolute)
    {
        if ($isAbsolute) {
            self::assertTrue(Path::isAbsolute($path));
        } else {
            self::assertFalse(Path::isAbsolute($path));
        }
    }
}
 
<?php

namespace Eprst\Eac\Service;

class Path
{
    private $path;
    /**
     * @var array
     */
    private $root = null;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function setRoot($root)
    {
        $this->root = $root;
    }

    public static function isRemote($path)
    {
        if (mb_substr($path, 0, 2) == '//') {
            return true;
        }

        $url = parse_url($path);

        $isHostPresent = isset($url['host']);
        $schemeIsFile = isset($url['scheme']) && $url['scheme'] == 'file';

        return $isHostPresent && !$schemeIsFile;
    }

    public static function isAbsolute($path, $root = null)
    {
        if (self::isRemote($path)) {
            return true;
        }

        $isWin = defined('PHP_WINDOWS_VERSION_BUILD');
        if ($isWin) {
            if ($root === null) {
                $isPathStartsWithRoot = preg_match('/^[a-z]:[\/\\\\]/i', $path);
            } else {
                $root = strtolower(realpath($root));
                $realpath = strtolower(realpath($path));
                if (!$root || !$realpath) {
                    return true;
                }
                $isPathStartsWithRoot = strlen($realpath) >= strlen($root)
                                        && substr($realpath, 0, strlen($root)) == $root;
            }
        } else {
            $root = ($root === null) ? '/' : $root;
            $isPathStartsWithRoot = strlen($path) >= strlen($root)
                                    && substr($path, 0, strlen($root)) == $root;
        }

        return $isPathStartsWithRoot;
    }

    private static function combinePathParts($path1, $path2)
    {
        return rtrim($path1, "\\/") . DIRECTORY_SEPARATOR . ltrim($path2, "\\/");
    }

    private static function combineMultiplePaths($toPath, $paths, $append = true)
    {
        if (self::isRemote($toPath)) {
            return $toPath;
        }

        $result = $toPath;

        $paths = $append ? $paths : array_reverse($paths);

        foreach ($paths as $part) {
            $result = $append ? self::combinePathParts($result, $part) : self::combinePathParts($part, $result);
        }

        return $result;
    }

    public static function prepend($basePath, $path1, $pathN = null)
    {
        return static::combineMultiplePaths($basePath, array_slice(func_get_args(), 1), false);
    }

    public static function append($basePath, $path1, $pathN = null)
    {
        return static::combineMultiplePaths($basePath, array_slice(func_get_args(), 1), true);
    }
}
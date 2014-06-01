<?php

namespace Eprst\Eac\Service;

class Path
{
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

        $isAbsoluteWin = function($path, $root) {
            if ($root === null) {
                $isPathStartsWithRoot = preg_match('/^([a-z]:)?[\/\\\\]/i', $path);
            } else {
                $root     = strtolower(realpath($root));
                $realpath = strtolower(realpath($path));
                if (!$root || !$realpath) {
                    return true;
                }
                $isPathStartsWithRoot = stripos($realpath, $root) === 0;
            }

            return $isPathStartsWithRoot;
        };

        $isAbsoluteNix = function($path, $root) {
            $root                 = ($root === null) ? '/' : $root;
            $isPathStartsWithRoot = stripos($path, $root) === 0;

            return $isPathStartsWithRoot;
        };

        $isWin = defined('PHP_WINDOWS_VERSION_BUILD');
        if ($isWin) {
            return $isAbsoluteWin($path, $root) || $isAbsoluteNix($path, $root);
        } else {
            return $isAbsoluteNix($path, $root);
        }
    }

    private static function combinePathParts($path1, $path2)
    {
        return rtrim($path1, "\\/") . DIRECTORY_SEPARATOR . ltrim($path2, "\\/");
    }

    private static function combineMultiplePaths($toPath, $paths, $append = true)
    {
        $result = $toPath;

        $paths = $append ? $paths : array_reverse($paths);

        foreach ($paths as $part) {
            $result = $append ? self::combinePathParts($result, $part) : self::combinePathParts($part, $result);
        }

        return $result;
    }

    public static function prepend($basePath, $path1, $pathN = null)
    {
        if (self::isRemote($basePath)) {
            return $basePath;
        }
        return static::combineMultiplePaths($basePath, array_slice(func_get_args(), 1), false);
    }

    public static function append($basePath, $path1, $pathN = null)
    {
        return static::combineMultiplePaths($basePath, array_slice(func_get_args(), 1), true);
    }
}
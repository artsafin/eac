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
        $this->url = parse_url($this->path);
    }

    public function setRoot($root)
    {
        $this->root = $root;
    }

    public function isRemote()
    {
        $isHostPresent = isset($this->url['host']);
        $schemeIsFile = isset($this->url['scheme']) && $this->url['scheme'] == 'file';

        return $isHostPresent && !$schemeIsFile;
    }

    public static function isAbsolute($path, $root = null)
    {
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

    private function combinePathParts($path1, $path2)
    {
        return rtrim($path1, "\\/") . DIRECTORY_SEPARATOR . ltrim($path2, "\\/");
    }

    private function combineMultiplePaths($paths)
    {
        if ($this->isRemote()) {
            return $this->path;
        }

        $result = $this->path;

        foreach ($paths as $part) {
            $result = $this->combinePathParts($part, $result);
        }

        return $result;
    }

    public function prepend($path1, $pathN = null)
    {
        return $this->combineMultiplePaths(array_reverse(func_get_args()));
    }
}
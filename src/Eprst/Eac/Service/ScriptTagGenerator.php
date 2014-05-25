<?php

namespace Eprst\Eac\Service;

class ScriptTagGenerator
{
    /**
     * @param $compileFile
     * @param $webroot
     * @param $prefix
     *
     * @return string
     */
    public function generate($compileFile, $webroot, $prefix)
    {
        $compileFile = realpath($compileFile);
        $webroot = realpath($webroot);

        $compileFile = mb_substr(realpath($compileFile), mb_strlen($webroot));

        return $this->getTag($prefix . '/' . ltrim($compileFile, "/\\"));
    }

    private function getTag($src)
    {
        return sprintf("<script type='text/javascript' src='%s'></script>", $src);
    }
} 
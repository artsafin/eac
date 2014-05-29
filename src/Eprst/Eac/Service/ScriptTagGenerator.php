<?php

namespace Eprst\Eac\Service;

class ScriptTagGenerator
{
    /**
     * @param $src
     *
     * @return string
     */
    public function generate($src)
    {
        return sprintf("<script type='text/javascript' src='%s'></script>", $src);
    }
}
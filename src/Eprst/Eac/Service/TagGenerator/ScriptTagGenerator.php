<?php

namespace Eprst\Eac\Service\TagWriter;

class ScriptTagGenerator implements TagGeneratorInterface
{
    /**
     * @param string $compiled
     *
     * @return string
     */
    public function generate($compiled)
    {
        return sprintf("<script type='text/javascript' src='%s'></script>", $compiled);
    }
}
<?php

namespace Eprst\Eac\Service\TagWriter;

class ScriptTagGenerator implements TagGeneratorInterface
{
    /**
     * @param array $attributes
     *
     * @return string
     */
    public function generate($attributes)
    {
        return sprintf("<script type='text/javascript' src='%s'></script>", $attributes['src']);
    }
}
<?php

namespace LuceneQueryParser\Node;

class AbstractNode
{
    function getClassName()
    {
        $classname = get_class($this);
        if ($pos = strrpos($classname, '\\')) return substr($classname, $pos + 1);
        return $pos;
    }
}


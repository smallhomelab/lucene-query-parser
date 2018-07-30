<?php

namespace LuceneQueryParser\Node;

class Field extends AbstractNode
{
    public $name;
    public $field;
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
        $this->field = $token['value'];
    }

    public function toString()
    {
        return $this->__toString();
    }

    public function __toString()
    {
        return ($this->name) ? "{$this->name}.{$this->field}" : $this->field;
    }

    public function toArray()
    {
        return $this->token;
    }

    public function toSimpleArray()
    {
        return $this->token['value'];
    }
}


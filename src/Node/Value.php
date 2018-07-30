<?php

namespace LuceneQueryParser\Node;

class Value extends AbstractNode
{
    /**
     * @var string
     */
    public $value;

    /**
     * @var array
     */
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
        $this->value = $token['value'];
    }

    public function toString()
    {
        return $this->__toString();
    }

    public function __toString()
    {
        return $this->value;
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


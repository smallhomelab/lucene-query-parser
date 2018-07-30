<?php

namespace LuceneQueryParser\Node;

use LuceneQueryParser\InvalidOperatorException;

class RangeTerm extends AbstractNode
{
    public $op;
    public $value;
    public $token;

    public function __construct($op, Value $value)
    {
        $this->validate($op, $value);
        $this->value = $value;
        $this->token = $this->op = $op;
    }

    public function validate($left, $op, $right)
    {
    }

    public function toString()
    {
        return $this->__toString();
    }

    public function __toString()
    {
        return
            '(' .
            $this->op['value'] .
            ' ' .
            (isset($this->value)? $this->value->toString() : null) .
            ')';
    }

    public function toArray()
    {
        return [
            'op' => $this->op,
            'value' => isset($this->value)? $this->value->toArray() : null];
    }

    public function toSimpleArray()
    {
        return [
            $this->op['value'],
            isset($this->value) ? $this->value->toSimpleArray() : null,
        ];
    }
}


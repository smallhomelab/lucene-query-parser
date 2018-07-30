<?php

namespace LuceneQueryParser\Node;

use LuceneQueryParser\InvalidOperatorException;

class BooleanOp extends AbstractNode
{
    public $left;
    public $op;
    public $right;
    public $token;

    public function __construct($left, $op, $right)
    {
        $this->validate($left, $op, $right);
        $this->left = $left;
        $this->token = $this->op = $op;
        $this->right = $right;
    }

    public function validate($left, $op, $right)
    {
        if (!$left || !$op || !$right) {
            $formattedString = ($left? $left->toString() : null) . ' ' . ($op['value'] ?? null) . ' ' . ( $right ? $right->toString() : null);
            throw new InvalidOperatorException ('Syntax error on near  \'' . $formattedString . '\' position:'. $op['position']);
        }
    }

    public function toString()
    {
        return $this->__toString();
    }

    public function __toString()
    {
        return
            '(' .
            (isset($this->left)? $this->left->toString() : null) .
            ' ' .
            $this->op['value'] .
            ' ' .
            (isset($this->right)? $this->right->toString() : null) .
            ')';
    }

    public function toArray()
    {
        return [
            'left' => isset($this->left)? $this->left->toArray() : null,
            'op' => $this->op,
            'right' => isset($this->right)? $this->right->toArray() : null];
    }

    public function toSimpleArray()
    {
        return [
            isset($this->left) ? $this->left->toSimpleArray() : null,
            $this->op['value'],
            isset($this->right) ? $this->right->toSimpleArray() : null,
        ];
    }
}


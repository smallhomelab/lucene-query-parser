<?php

namespace LuceneQueryParser\Node;

use LuceneQueryParser\InvalidOperatorException;
use LuceneQueryParser\ParserException;

class ContainOp extends AbstractNode
{
    public $left;
    public $op;
    public $right;
    public $token;

    public function __construct($left, $right)
    {
        $this->validate($left, $right);
        $this->left = $left;
        $this->right = $right;
        $this->op = ['value' => '<CONTAIN>'];
    }

    public function validate($left, $right)
    {
        if (!$left || !$right) {
            $formattedString = ($left? $left->toString() : null) . ' ' . ($op['value'] ?? null) . ' ' . ( $right ? $right->toString() : null);
            throw new InvalidOperatorException ('Syntax error on near  \'' . $formattedString . '\'');
        }

        if (!$left instanceof Field) {
            throw new ParserException('Expected <Field> but <' . $left->getClassName() . '> ' . $left->toString() . ' was given' );

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
            ' : ' .
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


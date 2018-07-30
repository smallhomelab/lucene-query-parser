<?php

namespace LuceneQueryParser;

use LuceneQueryParser\Node\Value;
use LuceneQueryParser\Node\BinOp;
use LuceneQueryParser\Node\Field;


class NodeVisitor
{
    public function visit($node)
    {
        if (is_a($node, BinOp::class)) {
            return $this->visitBinOp($node);
        }
        elseif (is_a($node, Field::class)) {
            return $this->visitIdentifier($node);
        }
        elseif ( is_a($node, Value::class)) {
            return $this->visitValue($node);
        }
        return $this->genericVisit($node);
    }

    public function genericVisit($node)
    {
        throw new InvalidOperatorException('Invalid Operator: '. print_r($node, true));
    }

    public function visitBinOp($node)
    {
        return $node->toString();
        if ($node->op['value'] == 'AND') {
            return '('. $this->visit($node->left) . ' AND ' . $this->visit($node->right) . ')';
        }
        elseif ($node->op['value'] == 'OR') {
            return '('. $this->visit($node->left) . ' OR ' . $this->visit($node->right) . ')';
        }
        elseif ($node->op['value'] == ':') {
            return '(' . $this->visit($node->left) . ' = ' . $this->visit($node->right) . ')';
        }
    }

    public function visitIdentifier($node)
    {
        return $node->toString();
    }

    public function visitValue($node)
    {
        return $node->value;
    }
}

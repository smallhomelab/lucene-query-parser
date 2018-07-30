<?php

namespace LuceneQueryParser\Node;

class RangeOp extends AbstractNode
{
    /**
     * @var Field
     */
    public $field;

    /**
     * @var CompareOp
     */
    public $lowerBound;

    /**
     * @var CompareOp
     */
    public $upperBound;

    /**
     * @var Value
     */
    public $lowerBoundValue;

    /**
     * @var Value
     */
    public $upperBoundValue;

    public $startInc;

    public $endInc;


    public function __construct(Field $field, Value $lowerBound, Value $upperBound, $startInc, $endInc)
    {
        $this->field = $field;
        $this->lowerBoundValue = $lowerBound;
        $this->upperBoundValue = $upperBound;
        $this->startInc = $startInc;
        $this->endInc = $endInc;


        $this->lowerBound  = new CompareOp($field, ['value' => '>' . (($startInc)? '=' : '')] , $lowerBound);
        $this->upperBound  = new CompareOp($field, ['value' => '<' . (($endInc)? '=' : '')] , $upperBound);

    }

    public function toString()
    {
        return $this->__toString();
    }

    public function __toString()
    {
        return implode('', [
            'field' => isset($this->field)? $this->field->toArray() : null,
            ':',
            'startInc' => ($this->startInc)? '[' : '{',
            'lowerBoundValue' => isset($this->lowerBoundValue)? $this->lowerBoundValue->toArray() : null,
            'TO',
            'upperBoundValue' => isset($this->upperBoundValue)? $this->upperBoundValue->toArray() : null,
            'endInc' => ($this->endInc)? ']' : '}',
        ]);
    }

    public function toArray()
    {
        return [
            'lowerBound' => isset($this->lowerBound)? $this->lowerBound->toArray() : null,
            'op' => ['value' => 'AND'],
            'upperBound' => isset($this->upperBound)? $this->upperBound->toArray() : null,
            ];
    }

    public function toSimpleArray()
    {
        return [
            isset($this->lowerBound)? $this->lowerBound->toSimpleArray() : null,
            'AND',
            isset($this->upperBound)? $this->upperBound->toSimpleArray() : null,
        ];
    }
}


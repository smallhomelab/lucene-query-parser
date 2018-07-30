<?php

namespace Test;

use LuceneQueryParser\InvalidOperatorException;
use PHPUnit\Framework\TestCase;
use LuceneQueryParser\Node\Field;
use LuceneQueryParser\Parser;
use LuceneQueryParser\ParserException;


class QueryFilterTest extends TestCase
{
    /**
     * @dataProvider parserSuccessData
     */
    public function testParserSuccess($filter, $parseTreeArray)
    {
        $parser = new Parser();
        $tree = $parser->parse($filter);
        //dd($tree->toSimpleArray());
        $this->assertEquals($parseTreeArray, $tree->toSimpleArray());
    }

    public function parserSuccessData()
    {
        return [
            [
                '(foo: bar)',
                ['foo', '<CONTAIN>', 'bar']
            ],
            [
                'foo: bar',
                ['foo', '<CONTAIN>', 'bar']
            ],
            [
                'foo: = "Bar"',
                ['foo', '=', 'Bar']
            ],
            [
                'foo: > 5',
                ['foo', '>', '5']
            ],
            [
                '(foo :>= 5)',
                ['foo', '>=', '5']
            ],
            [
                'foo:= "2005-01"x',
                [
                    ['foo', '=', '2005-01'],
                    'OR',
                    'x'
                ]
            ],
            [
                'foo: = "200" AND created_at: > "2015-01-01"',
                [
                    [ 'foo', '=', '200'],
                    'AND',
                    [ 'created_at', '>', '2015-01-01']
                ]
            ]

        ];
    }

    /**
     * @dataProvider parserRangeData
     */
    public function testParserRange($filter, $parseTreeArray)
    {
        $parser = new Parser();
        $tree = $parser->parse($filter);
        //dd($tree->toSimpleArray());
        $this->assertEquals($parseTreeArray, $tree->toSimpleArray());
    }

    public function parserRangeData()
    {
        return [
            [
                'foo: [bar1 TO bar2}',
                [['foo', '>=', 'bar1'], ['foo', '<' , 'bar2']]
            ]
        ];
    }

    /**
     * @dataProvider parserExceptionData
     */
    public function testParserException($filter, $exception)
    {
        $parser = new Parser();
        //$this->expectExceptionMessage($message);
        $this->expectException($exception);
        $tree = $parser->parse($filter);
    }

    public function parserExceptionData()
    {
        $expectingMessage = function ($type, $current, $next) {
            return "Expected the *$type* $current to be followed by whitespace or a ), was followed by $next";
        };


        return [
            ['foo := 2005:01', ParserException::class],
            ['foo := "2005-01"=', ParserException::class],
            ['foo: == boo', InvalidOperatorException::class],
        ];
    }

}
<?php

namespace LuceneQueryParser;

use LuceneQueryParser\Node\Field;
use LuceneQueryParser\Node\Value;
use LuceneQueryParser\Node\CompareOp;
use LuceneQueryParser\Node\ContainOp;
use LuceneQueryParser\Node\BooleanOp;
use LuceneQueryParser\Node\RangeOp;
use LuceneQueryParser\Node\BinOp;



class Parser
{
    const TC_WORD           = 0;   // Word
    const TC_PHRASE         = 1;   // Phrase (one or several quoted words)
    const TC_NUMBER         = 2;   // Nubers, which are used with syntax elements. Ex. roam~0.8
    const TC_SYNTAX_ELEMENT = 3;   // +  -  ( )  [ ]  { }  !  ||  && ~ ^


    const T_WHITESPACE = 0;
    const T_GENERIC_SYMBOL = 1;
    const T_FIELD = 2;
    const T_FIELD_SEPARATOR = 3;
    const T_FIELD_INDICATOR = 4;
    const T_VALUE = 5;
    const T_COMPARISON_OPERATOR = 6;
    const T_PRECEDENCE_OPERATOR = 7;
    const T_LOGIC_OPERATOR = 8;
    const T_RANGE_OPERATOR = 9;
    const T_RANGE_TO = 10;
    const T_WORD = 11;

    const REGEX_EMOTICON = '(?<=^|\s)(?:>:\-?\(|:\-?\)|<3|:\'\(|:\-?\|:\-?\/|:\-?\(|:\-?\*|:\-?\||:o\)|:\-?o|=\-?\)|:\-?D|:\-?p|:\-?P|:\-?b|;\-?p|;\-?P|;\-?b|;\-?\))';
    const REGEX_EMOJI = '[\x{2712}\x{2714}\x{2716}\x{271d}\x{2721}\x{2728}\x{2733}\x{2734}\x{2744}\x{2747}\x{274c}\x{274e}\x{2753}-\x{2755}\x{2757}\x{2763}\x{2764}\x{2795}-\x{2797}\x{27a1}\x{27b0}\x{27bf}\x{2934}\x{2935}\x{2b05}-\x{2b07}\x{2b1b}\x{2b1c}\x{2b50}\x{2b55}\x{3030}\x{303d}\x{1f004}\x{1f0cf}\x{1f170}\x{1f171}\x{1f17e}\x{1f17f}\x{1f18e}\x{1f191}-\x{1f19a}\x{1f201}\x{1f202}\x{1f21a}\x{1f22f}\x{1f232}-\x{1f23a}\x{1f250}\x{1f251}\x{1f300}-\x{1f321}\x{1f324}-\x{1f393}\x{1f396}\x{1f397}\x{1f399}-\x{1f39b}\x{1f39e}-\x{1f3f0}\x{1f3f3}-\x{1f3f5}\x{1f3f7}-\x{1f4fd}\x{1f4ff}-\x{1f53d}\x{1f549}-\x{1f54e}\x{1f550}-\x{1f567}\x{1f56f}\x{1f570}\x{1f573}-\x{1f579}\x{1f587}\x{1f58a}-\x{1f58d}\x{1f590}\x{1f595}\x{1f596}\x{1f5a5}\x{1f5a8}\x{1f5b1}\x{1f5b2}\x{1f5bc}\x{1f5c2}-\x{1f5c4}\x{1f5d1}-\x{1f5d3}\x{1f5dc}-\x{1f5de}\x{1f5e1}\x{1f5e3}\x{1f5ef}\x{1f5f3}\x{1f5fa}-\x{1f64f}\x{1f680}-\x{1f6c5}\x{1f6cb}-\x{1f6d0}\x{1f6e0}-\x{1f6e5}\x{1f6e9}\x{1f6eb}\x{1f6ec}\x{1f6f0}\x{1f6f3}\x{1f910}-\x{1f918}\x{1f980}-\x{1f984}\x{1f9c0}\x{3297}\x{3299}\x{a9}\x{ae}\x{203c}\x{2049}\x{2122}\x{2139}\x{2194}-\x{2199}\x{21a9}\x{21aa}\x{231a}\x{231b}\x{2328}\x{2388}\x{23cf}\x{23e9}-\x{23f3}\x{23f8}-\x{23fa}\x{24c2}\x{25aa}\x{25ab}\x{25b6}\x{25c0}\x{25fb}-\x{25fe}\x{2600}-\x{2604}\x{260e}\x{2611}\x{2614}\x{2615}\x{2618}\x{261d}\x{2620}\x{2622}\x{2623}\x{2626}\x{262a}\x{262e}\x{262f}\x{2638}-\x{263a}\x{2648}-\x{2653}\x{2660}\x{2663}\x{2665}\x{2666}\x{2668}\x{267b}\x{267f}\x{2692}-\x{2694}\x{2696}\x{2697}\x{2699}\x{269b}\x{269c}\x{26a0}\x{26a1}\x{26aa}\x{26ab}\x{26b0}\x{26b1}\x{26bd}\x{26be}\x{26c4}\x{26c5}\x{26c8}\x{26ce}\x{26cf}\x{26d1}\x{26d3}\x{26d4}\x{26e9}\x{26ea}\x{26f0}-\x{26f5}\x{26f7}-\x{26fa}\x{26fd}\x{2702}\x{2705}\x{2708}-\x{270d}\x{270f}]|\x{23}\x{20e3}|\x{2a}\x{20e3}|\x{30}\x{20e3}|\x{31}\x{20e3}|\x{32}\x{20e3}|\x{33}\x{20e3}|\x{34}\x{20e3}|\x{35}\x{20e3}|\x{36}\x{20e3}|\x{37}\x{20e3}|\x{38}\x{20e3}|\x{39}\x{20e3}|\x{1f1e6}[\x{1f1e8}-\x{1f1ec}\x{1f1ee}\x{1f1f1}\x{1f1f2}\x{1f1f4}\x{1f1f6}-\x{1f1fa}\x{1f1fc}\x{1f1fd}\x{1f1ff}]|\x{1f1e7}[\x{1f1e6}\x{1f1e7}\x{1f1e9}-\x{1f1ef}\x{1f1f1}-\x{1f1f4}\x{1f1f6}-\x{1f1f9}\x{1f1fb}\x{1f1fc}\x{1f1fe}\x{1f1ff}]|\x{1f1e8}[\x{1f1e6}\x{1f1e8}\x{1f1e9}\x{1f1eb}-\x{1f1ee}\x{1f1f0}-\x{1f1f5}\x{1f1f7}\x{1f1fa}-\x{1f1ff}]|\x{1f1e9}[\x{1f1ea}\x{1f1ec}\x{1f1ef}\x{1f1f0}\x{1f1f2}\x{1f1f4}\x{1f1ff}]|\x{1f1ea}[\x{1f1e6}\x{1f1e8}\x{1f1ea}\x{1f1ec}\x{1f1ed}\x{1f1f7}-\x{1f1fa}]|\x{1f1eb}[\x{1f1ee}-\x{1f1f0}\x{1f1f2}\x{1f1f4}\x{1f1f7}]|\x{1f1ec}[\x{1f1e6}\x{1f1e7}\x{1f1e9}-\x{1f1ee}\x{1f1f1}-\x{1f1f3}\x{1f1f5}-\x{1f1fa}\x{1f1fc}\x{1f1fe}]|\x{1f1ed}[\x{1f1f0}\x{1f1f2}\x{1f1f3}\x{1f1f7}\x{1f1f9}\x{1f1fa}]|\x{1f1ee}[\x{1f1e8}-\x{1f1ea}\x{1f1f1}-\x{1f1f4}\x{1f1f6}-\x{1f1f9}]|\x{1f1ef}[\x{1f1ea}\x{1f1f2}\x{1f1f4}\x{1f1f5}]|\x{1f1f0}[\x{1f1ea}\x{1f1ec}-\x{1f1ee}\x{1f1f2}\x{1f1f3}\x{1f1f5}\x{1f1f7}\x{1f1fc}\x{1f1fe}\x{1f1ff}]|\x{1f1f1}[\x{1f1e6}-\x{1f1e8}\x{1f1ee}\x{1f1f0}\x{1f1f7}-\x{1f1fb}\x{1f1fe}]|\x{1f1f2}[\x{1f1e6}\x{1f1e8}-\x{1f1ed}\x{1f1f0}-\x{1f1ff}]|\x{1f1f3}[\x{1f1e6}\x{1f1e8}\x{1f1ea}-\x{1f1ec}\x{1f1ee}\x{1f1f1}\x{1f1f4}\x{1f1f5}\x{1f1f7}\x{1f1fa}\x{1f1ff}]|\x{1f1f4}\x{1f1f2}|\x{1f1f5}[\x{1f1e6}\x{1f1ea}-\x{1f1ed}\x{1f1f0}-\x{1f1f3}\x{1f1f7}-\x{1f1f9}\x{1f1fc}\x{1f1fe}]|\x{1f1f6}\x{1f1e6}|\x{1f1f7}[\x{1f1ea}\x{1f1f4}\x{1f1f8}\x{1f1fa}\x{1f1fc}]|\x{1f1f8}[\x{1f1e6}-\x{1f1ea}\x{1f1ec}-\x{1f1f4}\x{1f1f7}-\x{1f1f9}\x{1f1fb}\x{1f1fd}-\x{1f1ff}]|\x{1f1f9}[\x{1f1e6}\x{1f1e8}\x{1f1e9}\x{1f1eb}-\x{1f1ed}\x{1f1ef}-\x{1f1f4}\x{1f1f7}\x{1f1f9}\x{1f1fb}\x{1f1fc}\x{1f1ff}]|\x{1f1fa}[\x{1f1e6}\x{1f1ec}\x{1f1f2}\x{1f1f8}\x{1f1fe}\x{1f1ff}]|\x{1f1fb}[\x{1f1e6}\x{1f1e8}\x{1f1ea}\x{1f1ec}\x{1f1ee}\x{1f1f3}\x{1f1fa}]|\x{1f1fc}[\x{1f1eb}\x{1f1f8}]|\x{1f1fd}\x{1f1f0}|\x{1f1fe}[\x{1f1ea}\x{1f1f9}]|\x{1f1ff}[\x{1f1e6}\x{1f1f2}\x{1f1fc}]';
    const REGEX_URL = '[+-]?[\w-]+:\/\/[^\s\/$.?#].[^\s\^~]*';
    const REGEX_PHRASE = '[+-]?"(?:""|[^"])*"';
    const REGEX_HASHTAG = '[+-]?#+[a-zA-Z0-9_]+';
    const REGEX_MENTION = '[+-]?@+[a-zA-Z0-9_]+(?:[a-zA-Z0-9_\.\-]+)?';
    const REGEX_NUMBER = '(?:[+-]?[0-9]+(?:[\.][0-9]+)*)(?:[eE][+-]?[0-9]+)?';
    const REGEX_DATE = '[+-]?\d{4}-\d{2}-\d{2}';
    const REGEX_WORD = '[+-]?[^\s\(\)\\\\^\<\>\[\]\{\}~=:]*';
    const REGEX_FIELD = '[+-]?[a-zA-Z\_]+(?:[a-zA-Z0-9_\.\-]+)?';
    //const REGEX_OPERATOR = '(?:[!><=~:]+)';
    const REGEX_OPERATOR = '[=~]|<=?|>=?';
    //(?:([!><=~]+|[:]+))

    protected $tokens = [];
    protected $tokenIndex = 0;

    /**
     * @param $input
     * @return ParseTree
     */
    public function parse($input)
    {
        $splitRegex = sprintf(
            '/(%s)/iu',
            implode(')|(', [
                self::REGEX_EMOTICON,
                self::REGEX_URL,
                self::REGEX_PHRASE,
                self::REGEX_WORD,
                self::REGEX_FIELD,
                self::REGEX_OPERATOR,
            ])
        );

        $segments = preg_split(
            //'#([a-z-_\\\][a-z0-9-_\\\:]*[a-z0-9_]{1})|((?:[0-9]+(?:[\.][0-9]+)*)(?:e[+-]?[0-9]+)?)|(\'(?:[^\']|\'\')*\')|("(?:[^"]|"")*")|([!><=~]{1,2})|(\s+)|(.)#i',
            $splitRegex,
            $input,
            -1,
            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE
        );

        $this->tokens = [];
        foreach ($segments as $segment) {
            $type = $this->getTokenType($segment[0]);
            $this->tokens[] = [
                'value' => $segment[0],
                'type'  => $type,
                'position' => $segment[1],
            ];
        }

        //dd($this->tokens);
        $parseTree = $this->parseExpr();
        $token = $this->currentToken();
        if($token) {
            throw new ParserException( 'Syntax error at \''. $token['value'] . '\' position: ' . $token['position']);
        }
        return $parseTree;
    }

    protected function eat($tokenType)
    {
        $token = $this->currentToken();
        if ($token['type'] == $tokenType['type'] && $token['value'] == $tokenType['value']) {
            $this->nextToken();
        }
        else {
            throw new ParserException( 'eat() error'. print_r($token, true) .print_r($tokenType, true));
        }
    }

    protected function parseExpr()
    {
        $node = $this->parseTerm();
        $token = $this->currentToken();
        while ($token && ($token['type'] == self::T_LOGIC_OPERATOR || $token['type'] == self::T_WORD || $token['type'] == self::T_VALUE )) {
            if ($token['type'] == self::T_LOGIC_OPERATOR) {
                $this->eat($token);
                $node = new BooleanOp($node, $token, $this->parseTerm());
            }
            else {
                $node = new BooleanOp($node, ['value' => 'OR'], $this->parseTerm());
            }

            //$token = $this->nextToken();
            $token = $this->currentToken();
        }

//        if ($token) {
//            throw new ParserException( 'Syntax Error in next to ' . (isset($node) ?  "'" . $node->toString() . "'": "'" . $token['value'] . "'" . ' position:' . $token['position']) );
//        }

        return $node;
    }

    protected function parseTerm()
    {
        $node = $this->parseFactor();
        $token = $this->currentToken();
        if ($token && in_array($token['type'] , [self::T_VALUE, self::T_WORD, self::T_COMPARISON_OPERATOR, self::T_RANGE_OPERATOR])) {
            if($token['type'] == self::T_COMPARISON_OPERATOR)
            {
                $this->eat($token);
                $node =  new CompareOp( $node, $token, $this->parseFactor());
            }
            elseif ($token['type'] == self::T_RANGE_OPERATOR) {
                $startInc = $endInc = false;

                $token = $this->currentToken();
                $startRangeToken = $token;
                ($startRangeToken['value'] == '[') AND $startInc = true;
                $this->eat(['type' => self::T_RANGE_OPERATOR, 'value' => $startRangeToken['value']]);


                $lowerBoundNode = $this->parseFactor();

                $token = $this->currentToken();
                $this->eat(['type' => self::T_RANGE_TO, 'value' => 'TO']);

                $upperBoundNode = $this->parseFactor();

                $endRangeToken = $this->currentToken();
                ($endRangeToken['value'] == ']') AND $endInc = true;
                $this->eat(['type' => self::T_RANGE_OPERATOR, 'value' => $endRangeToken['value']]);

                $node = new RangeOp( $node, $lowerBoundNode, $upperBoundNode, $startInc, $endInc);
            }
            else {
                $node =  new ContainOp( $node, $this->parseFactor());
            }
            //$token = $this->nextToken();
            //$token = $this->currentToken();
        }
        return $node;
    }

    protected function parseFactor()
    {
        $token = $this->currentToken();
        if($token) {
            if ($token['type'] == self::T_WORD) {
                $this->eat($token);
                $nextToken = $this->currentToken();
                if ($nextToken && $nextToken['type'] == self::T_FIELD_INDICATOR) {
                    $this->eat($nextToken);
                    return new Field($token);
                }
                else {
                    return new Value($token);
                }
            }
            elseif ($token['type'] == self::T_FIELD)
            {
                $this->eat($token);
                return new Field($token);
            }
            elseif ($token['type'] == self::T_VALUE) {
                $this->eat($token);
                return new Value($token);
            }
            elseif ($token['type'] == self::T_PRECEDENCE_OPERATOR && $token['value'] == '(') {
                $this->eat($token);
                $node = $this->parseExpr();

                $this->eat(['type' => self::T_PRECEDENCE_OPERATOR, 'value' => ')' ]);
                return $node;
            }

        }

    }

    protected function getTokenType(&$value)
    {
        $type = self::T_GENERIC_SYMBOL;

        switch (true) {
            case (trim($value) === ''):
                return self::T_WHITESPACE;

            case ($value == '.'):
                return self::T_FIELD_SEPARATOR;

            case ($value == ':'):
                return self::T_FIELD_INDICATOR;

            case (is_numeric($value) || is_numeric($value[0])):
                return self::T_VALUE;

            case ($value[0] === "'"):
                $value = str_replace("''", "'", substr($value, 1, strlen($value) - 2));
                return self::T_VALUE;

            case ($value[0] === '"'):
                $value = str_replace('""', '"', substr($value, 1, strlen($value) - 2));
                return self::T_VALUE;

            case ($value == '(' || $value == ')'):
                return self::T_PRECEDENCE_OPERATOR;

            case (in_array($value[0], ['=', '>', '<', '!'])):
                return self::T_COMPARISON_OPERATOR;

            case (in_array($value, ['{', '}', '[', ']'])):
                return self::T_RANGE_OPERATOR;

            case (in_array(strtolower($value), ['and', 'or', '&&', '||'])):
                return self::T_LOGIC_OPERATOR;

//            case (ctype_alpha($value[0]) && $value[strlen($value)-1] == ':'):
//                $value = substr($value, 0, strlen($value) -1);
//                return self::T_FIELD;

            case strtolower($value) == 'to':
                return self::T_RANGE_TO;

            case (ctype_alpha($value[0])):
                return self::T_WORD;

        }
        return $type;
    }

    protected function currentToken()
    {
        if (!isset($this->tokens[$this->tokenIndex])) {
            return false;
        }
        return $this->tokens[$this->tokenIndex];
    }

    protected function nextToken()
    {
        INCREMENT_TOKEN:
        ++$this->tokenIndex;

        if (!isset($this->tokens[$this->tokenIndex])) {
            return false;
        }

        if ($this->tokens[$this->tokenIndex]['type'] === self::T_WHITESPACE) {
            goto INCREMENT_TOKEN;
        }

        return $this->tokens[$this->tokenIndex];
    }

    protected function peekToken($increment = 1)
    {
        if (!isset($this->tokens[$this->tokenIndex + $increment])) {
            return false;
        }
        return $this->tokens[($this->tokenIndex + $increment)];
    }

}

<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * class holds a collection of methods used to manage math expressions.
 *
 * @deprecated since 6.2.0 - no longer used.
/**/
class TCMSMath
{
    /**
     * evaluate an expression.
     *
     * @param string $mathString
     *
     * @return string|int
     */
    public static function EvaluateExpression($mathString)
    {
        if ('-' == substr($mathString, 0, 1)) {
            $mathString = '0'.$mathString;
        }
        $mathString = str_replace(' ', '', $mathString);
        $mathString = strtolower($mathString);
        if (self::IsValidExpression($mathString)) {
            // use array as stack
            $stack = array();
            $done = false;
            $parseString = str_replace(' ', '', $mathString); // remove all spaces...
            //echo $parseString . "\n";
            //echo "[\n";
            while (!$done) {
                // push items onto stack unless item is: null, or )
                $item = self::GetMathWordFromString($parseString);
                //echo "#'".$item."';".$parseString."#\n";
                if ((!is_null($item)) && (')' != $item)) {
                    if ('-' == $item) {
                        $item = '-'.self::GetMathWordFromString($parseString);
                    }
                    // if the array is empty and the item is not numeric then we want to push a zero on the stack first
                    if ((count($stack) < 1) && (!is_numeric($item))) {
                        array_push($stack, '0');
                    }
                    array_push($stack, $item);
                } else {
                    // process contents on stack
                    self::ProcessExpressionStack($stack);
                    // are we done?
                    if (empty($parseString) && (count($stack) < 2)) {
                        $done = true;
                    }
                }
            }
            //echo "]\n";
            return $stack[0];
        } else {
            return 0;
        }
    }

    /**
     * return true if the expression is valid.
     *
     * @param string $sCodeFunction (math expression)
     *
     * @return bool
     */
    public static function IsValidExpression($sCodeFunction)
    {
        $bIsValid = true;
        $sCodeFunction = str_replace(' ', '', $sCodeFunction);
        $sCodeFunction = strtolower($sCodeFunction);
        $aAllowedSymbols = array('(', ')', '-', '+', '*', '/', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $iNumberOfOpeningBraces = 0;
        $iNumberOfClosingBraces = 0;
        for ($i = 0; $i < strlen($sCodeFunction); ++$i) {
            if (!in_array($sCodeFunction[$i], $aAllowedSymbols)) {
                $bIsValid = false;
            }
            if ('(' == $sCodeFunction[$i]) {
                ++$iNumberOfOpeningBraces;
            }
            if (')' == $sCodeFunction[$i]) {
                ++$iNumberOfClosingBraces;
            }
        }
        if ($iNumberOfOpeningBraces != $iNumberOfClosingBraces) {
            $bIsValid = false;
        }

        return $bIsValid;
    }

    protected static function GetMathWordFromString(&$mathString)
    {
        $result = '';
        preg_match("/^\d+\.*\d*|\(|\)|\+|\-|\*|\//", $mathString, $resultArray);
        //if ($mathString == '(3228219))') $trigger = true; else $trigger = false;
        //if ($trigger) echo "\n\n";
        if (array_key_exists(0, $resultArray)) {
            $result = $resultArray[0];
        }
        //if ($trigger) echo "|".$result."|\n";
        $resultLength = strlen($result);
        if (0 === $resultLength) {
            $result = null;
            $mathString = '';
        } else {
            $mathString = substr($mathString, $resultLength);
        }
        //if ($trigger) echo "<{$result}, {$mathString}>\n\n";
        return $result;
    }

    protected static function ProcessExpressionStack(&$stack)
    {
        $operator = '';
        $tempResult = array_pop($stack);
        $done = false;
        while (!$done && (!is_null($item = array_pop($stack)))) {
            if (is_numeric($item)) {
                if ('+' == $operator) {
                    $tempResult = $item + $tempResult;
                } elseif ('-' == $operator) {
                    $tempResult = $item - $tempResult;
                } elseif ('*' == $operator) {
                    $tempResult = $item * $tempResult;
                } elseif ('/' == $operator) {
                    $tempResult = $item / $tempResult;
                }
            } elseif (')' == $item) {
                array_push($stack, $tempResult);
                $done = true;
            } else {
                $operator = $item;
            }
        }
        if (!$done) {
            array_push($stack, $tempResult);
        }
    }
}

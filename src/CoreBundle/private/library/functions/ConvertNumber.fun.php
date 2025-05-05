<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// -----------------------------------------------------------------------------
/* function: ConvertNumber
*  to avoid represenation problems decimal values are stored as ints in the database.
*  this converts the int to a decimal representation and back
*  input   : int (string) - int to convert
*            dir (string) - sql2g -> sql to german, g2sql -> german to sql
*  output  : [return] (string) - converted string
*  20.06.2002, v1.0
*/
// ..............................................................................
function ConvertNumber($number, $dir, $digits = 2)
{
    if (0 == strcmp($dir, 'sql2g')) {
        // numbers are stored as ints... convert to float
        $cnumber = $number / pow(10, $digits);
        // and format
        $cnumber = number_format($cnumber, $digits, ',', '.');

        return $cnumber;
    } elseif (0 == strcmp($dir, 'g2sql')) {
        // remove sign
        $tempNum = $number;
        $sign = mb_substr($tempNum, 0, 1);
        if (0 == strcmp($sign, '-')) {
            $tempNum = mb_substr($tempNum, 1);
        } else {
            $sign = '';
        }
        // do we need to add decimal?
        $tmpPos = strpos($tempNum, ',');
        if (false === $tmpPos) {
            $decimals = 0;
        } else {
            $decimals = (strlen($tempNum) - 1) - $tmpPos;
        }
        $tempNum = preg_replace('/\\+|,|\\./', '', $tempNum); // remove plus(+), comma (,), and period (.)
        $tempNum = preg_replace('/\\A0/', '', $tempNum); // remove leading zero
        if (empty($tempNum)) {
            $tempNum = '0';
        } // set to zero if empty
        if ($decimals > $digits) {
            $tempNum = round($tempNum / pow(10, $decimals - $digits));
        } elseif ($decimals < $digits) {
            $tempNum = round($tempNum * pow(10, $digits - $decimals));
        }

        return $sign.$tempNum;
    }

    return null;
}

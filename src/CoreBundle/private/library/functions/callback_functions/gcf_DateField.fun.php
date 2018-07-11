<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once PATH_LIBRARY.'/functions/ConvertDate.fun.php';
function gcf_DateField($field, $row, $fieldName)
{
    if ('0000-00-00' == $field) {
        $returnString = 'kein Datum';
    } else {
        $dateArray = explode('-', $field);
        $germanDate = ConvertDate($field, 'sql2g');

        setlocale(LC_TIME, 'de_DE@euro', 'de_DE', 'de', 'ge', 'de_DE.ISO8859-1');
        $timeStamp = mktime(0, 0, 0, $dateArray[1], $dateArray[2], $dateArray[0]);
        $dayString = strftime('%a', $timeStamp);
        setlocale(LC_TIME, 0);

        $returnString = TGlobal::OutHTML("<!-- {$field} -->".$dayString.' '.$germanDate);
    }

    return $returnString;
}

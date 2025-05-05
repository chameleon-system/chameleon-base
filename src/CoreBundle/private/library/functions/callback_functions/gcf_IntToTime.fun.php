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
 * converts seconds in Hour:Minutes:Seconds output.
 *
 * @param string $sValue - the field value
 * @param array $aRow - the whole record array
 * @param string $sFieldName
 *
 * @return string
 */
function gcf_IntToTime($sValue, $aRow, $sFieldName)
{
    $returnString = gmdate('H:i:s', $sValue);

    return $returnString;
}

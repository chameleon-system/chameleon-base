<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function gcf_GetPublishedIcon($value, $row)
{
    $returnVal = '';

    if ('1' == $value) {
        $returnVal = '<img src="'.TGlobal::GetPathTheme().'/images/icons/accept.png" />';
    } else {
        $returnVal = '<img src="'.TGlobal::GetPathTheme().'/images/icons/delete.png" />';
    }

    return $returnVal;
}

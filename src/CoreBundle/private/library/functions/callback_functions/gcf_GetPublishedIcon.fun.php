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
        $returnVal = '<img src="/chameleon/blackbox/images/nav_icons/success.png" title="freigegeben" />';
    } else {
        $returnVal = '<img src="/chameleon/blackbox/images/nav_icons/error.png" title="nicht freigegeben" />';
    }

    return $returnVal;
}

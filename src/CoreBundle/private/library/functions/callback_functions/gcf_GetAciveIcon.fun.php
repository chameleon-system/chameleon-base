<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function gcf_GetAciveIcon($value, $row)
{
    $returnVal = '';

    if ('1' == $value) {
        $returnVal = '<img src="/chameleon/blackbox/images/icons/accept.png" title="'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.field_boolean.yes')).'" />';
    } else {
        $returnVal = '<img src="/chameleon/blackbox/images/icons/cancel.png" title="'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.field_boolean.no')).'" />';
    }

    return $returnVal;
}

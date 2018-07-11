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
function gcf_DateFieldGerman($field, $row, $fieldName)
{
    $sReturnValue = '';
    if ('0000-00-00' != $field && '0000-00-00 00:00:00' != $field) {
        $sReturnValue = ConvertDate($field, 'sql2g');
    }

    return TGlobal::OutHTML($sReturnValue);
}

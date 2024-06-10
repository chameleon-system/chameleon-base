<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function gcf_LookupName($field, $row, $fieldName = '')
{
    if (str_ends_with($fieldName, '_id')) {
        $tblName = substr($fieldName, 0, -3);
    } else {
        $tblName = $fieldName;
    }

    // check if the field has a counter and fix the lookup table name
    if (is_numeric(mb_substr($tblName, -1))) {
        $tblName = mb_substr($tblName, 0, -2);
    }
    $sClassName = TCMSTableToClass::GetClassName('Tdb', $tblName);

    /**
     * @var TCMSRecord $oRecord
     */
    $oRecord = new $sClassName();
    if ($oRecord->Load($row[$fieldName])) {
        $name = $oRecord->GetDisplayValue();
    } else {
        $name = '';
    }

    return $name;
}

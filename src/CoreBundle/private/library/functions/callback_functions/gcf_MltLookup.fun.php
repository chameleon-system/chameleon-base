<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function gcf_MltLookup($field, $row, $fieldName)
{
    $sClassName = TCMSTableToClass::GetClassName('Tdb', $_SESSION['_tmpCurrentTableName']);
    $oRecord = call_user_func($sTableName = $sClassName.'::GetNewInstance', $row['id']);
    /** @var $oMltTargetList TCMSRecordList */
    $oMltTargetList = $oRecord->GetMLT($fieldName);
    $aConnectedRecordNames = [];
    while ($oTargetRecord = $oMltTargetList->Next()) {
        $aConnectedRecordNames[] = $oTargetRecord->GetName();
    }
    $sConnectedRecordNames = implode(', ', $aConnectedRecordNames);

    return $sConnectedRecordNames;
}

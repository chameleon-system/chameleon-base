<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function gcf_MltLookupAllOnEmpty($field, $row, $fieldName)
{
    /** @var $oRecord TCMSRecord */
    $sClassName = TCMSTableToClass::GetClassName('Tdb', $_SESSION['_tmpCurrentTableName']);
    $oRecord = call_user_func($sTableName = $sClassName.'::GetNewInstance', $row['id']);
    /** @var $oMltTargetList TCMSRecordList */
    $oMltTargetList = $oRecord->GetMLT($fieldName);
    $aConnectedRecordNames = [];
    if ($oMltTargetList->Length() > 0) {
        while ($oTargetRecord = $oMltTargetList->Next()) {
            $aConnectedRecordNames[] = $oTargetRecord->GetName();
        }
        $sConnectedRecordNames = implode(', ', $aConnectedRecordNames);
    } else {
        $sListClassName = TCMSTableToClass::GetClassName('Tdb', $oMltTargetList->sTableName.'List');
        $oRecordList = call_user_func($sListClassName.'::GetList', null);
        while ($oRecord = $oRecordList->Next()) {
            $aConnectedRecordNames[] = $oRecord->GetName();
        }
        $sConnectedRecordNames = implode(', ', $aConnectedRecordNames);
    }

    return $sConnectedRecordNames;
}

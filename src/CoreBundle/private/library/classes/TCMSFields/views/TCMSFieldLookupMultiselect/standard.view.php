<?php
/** @var $oField TCMSField* */
/** @var $bFieldHasErrors boolean* */
/** @var $oConnectedMLTRecords TCMSRecordList* */
/** @var $oMLTRecords TCMSRecordList* */
$aRecordsConnected = $oConnectedMLTRecords->GetIdList();
?>
<select name="<?=TGlobal::OutHTML($oField->name); ?>[]" multiple size="10" id="<?=TGlobal::OutHTML($oField->name); ?>">
    <?php
    while ($oRecord = $oMLTRecords->Next()) {
        $selected = '';
        if (in_array($oRecord->id, $aRecordsConnected)) {
            $selected = 'selected="selected"';
        }
        echo '<option value="'.TGlobal::OutHTML($oRecord->id).'" '.$selected.'>'.TGlobal::OutHTML($oRecord->GetName()).'</option>';
        echo "\n";
    }
    ?>
</select>
<?php
if (array_key_exists('copyState', $data) && 'copied' == $data['copyState']) {
    $oTmpRecord = new TCMSRecord();
    /** @var $oTmpRecord TCMSRecord */
    $oTmpRecord = new TCMSRecord($data['sTableName']);
    $oTmpRecord->Load($data['id']);
    $sTmpName = '';
    if ($oTmpRecord->GetName()) {
        $sTmpName = '"'.TGlobal::OutJS($oTmpRecord->GetName()).'" ';
    } ?>
<script type="text/javascript">
    $(document).ready(function () {
        toasterMessage('<?=$sTmpName; ?><?=TGlobal::Translate('chameleon_system_core.template_engine.msg_copy_success'); ?>', 'MESSAGE');
    });
</script>
<?php
}
$styleLookup = array('oddrow', 'evenrow');
$rowCount = 0;
?>
<script type="text/javascript">
    <?php
    if (!empty($data['sMessages'])) {
        ?>
    $(document).ready(function () {
        <?php
        if (isset($aMessages) && is_array($aMessages) && count($aMessages) > 0) {
            foreach ($aMessages as $sFieldName => $sMessage) {
                echo "document.getElementById('fieldname_".$sFieldName."').className = 'requiredfieldfocus';";
            }
        } ?>

        toasterMessage('<?=$sMessages; ?>', '<?=TGlobal::OutJS($sMessageType); ?>');
    });
        <?php
    }
    ?>
    var sCurrentRecordName = '<?=TGlobal::OutJS($oTable->GetName()); ?>';

    function DeleteRecord() {
        var sConfirmText = '<?=TGlobal::Translate('chameleon_system_core.action.confirm_delete'); ?>';
        if (sCurrentRecordName != '') {
            sConfirmText += "\n \"" + sCurrentRecordName + '"';
        } else {
            sConfirmText += "\n \"<?=TGlobal::Translate('chameleon_system_core.text.unnamed_record'); ?>\"";
        }
        if (confirm(sConfirmText)) {
            CHAMELEON.CORE.showProcessingDialog();
            document.cmseditform.elements['module_fnc[contentmodule]'].value = 'Delete';
            document.cmseditform.submit();
        }
    }
</script>
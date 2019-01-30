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
    var sCurrentRecordName = '<?=TGlobal::OutJS($oTable->GetName()); ?>';

    function DeleteRecord() {
        var sConfirmText = '<?=TGlobal::Translate('chameleon_system_core.action.confirm_delete'); ?>';
        if (sCurrentRecordName != '') {
            sConfirmText += "\n \"" + sCurrentRecordName + '"';
        } else {
            sConfirmText += "\n \"<?=TGlobal::Translate('chameleon_system_core.text.unnamed_record'); ?>\"";
        }
        if (confirm(sConfirmText)) {
            CHAMELEON.CORE.showProcessingModal();
            document.cmseditform.elements['module_fnc[contentmodule]'].value = 'Delete';
            document.cmseditform.submit();
        }
    }
</script>
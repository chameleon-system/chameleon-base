<script type="text/javascript">
    if (!parent.framestosave) parent.framestosave = new Array();
</script>
<div>
<?php
if ($data['aPermission']['showlist'] && '1' == $data['only_one_record_tbl'] && array_key_exists('bIsLoadedFromIFrame', $data) && $data['bIsLoadedFromIFrame']) {
    ?>
    <script type="text/javascript">
        $(document).ready(function () {
            parent.$("iframe").each(function (iel, el) {
                if (el.contentWindow === window) {
                    parent.framestosave[parent.framestosave.length] = el.id;
                }
            });
        });

        // check if iframe size is to small and resize it
        setInterval(function() {
            var lastHeight = 0, curHeight = 0;
            curHeight = document.body.scrollHeight;
            if ( curHeight != lastHeight ) {
                parent.updateIframeSize('<?=TGlobal::OutHTML($sForeignField); ?>',document.body.scrollHeight);
            }
        },500);
    </script>
    <?php
}
if (array_key_exists('copyState', $data) && 'copied' == $data['copyState']) {
    /** @var $oTmpRecord TCMSRecord */
    $oTmpRecord = new TCMSRecord();
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
$rowCount = 0;
?>
    <script type="text/javascript">
        <?php
        if (!empty($data['showLangCopy'])) {
            echo "
    $(document).ready(function() {
      toasterMessage('".TGlobal::Translate('chameleon_system_core.cms_module_table_editor.msg_translation_created')."','WARNING');
    });
    ";
        }
        ?>

        <?php
        if (isset($aMessages) && is_array($aMessages) && count($aMessages) > 0) {
            ?>
        $(document).ready(function () {
            <?php
            foreach ($aMessages as $aMessage) {
                if (isset($aMessage['sMessageRefersToField'])) {
                    // add the class to field
                    echo "$('#fieldname_".$aMessage['sMessageRefersToField']."').addClass('fieldMsg ".$aMessage['sMessageType']."');\n";
                } ?>toasterMessage('<?=$aMessage['sMessage']; ?>', '<?=TGlobal::OutJS($aMessage['sMessageType']); ?>');<?php
            } ?>
        });
            <?php
        }
        ?>
        var sCurrentRecordName = '<?=str_replace("'", "\'", htmlspecialchars_decode($oTable->GetName())); ?>';

        <?php
        if ($bRevisionManagementActive) {
            ?>
        function AddNewRevision() {
            CreateModalDialogFromContainer('addNewRevisionDialog');
            $('#cmsrevisionname').val(sCurrentRecordName);
        }

        function SaveNewRevision() {
            document.cmseditform.elements['module_fnc[contentmodule]'].value = 'AddNewRevision';
            $("#cmseditform").prepend('<div id="hiddenTmpFormFields" style="display:none;"></div>');
            $("#cmsrevisionname").clone().prependTo("#hiddenTmpFormFields");
            var cmsrevisiondescriptionTmp = document.addNewRevisionForm.elements['cmsrevisiondescription'].value;
            $("#cmsrevisiondescription").clone().prependTo("#hiddenTmpFormFields");
            document.getElementById('cmsrevisiondescription').value = cmsrevisiondescriptionTmp;
            $('#cmseditform').submit();
        }


        function ActivateRecordRevision(id) {
            $("#dialog-confirm").dialog({
                resizable:false,
                height:180,
                width:300,
                modal:true,
                buttons:{
                    '<?=TGlobal::Translate('chameleon_system_core.record_revision.action_confirm_restore_revision'); ?>':function () {
                        $("#cmseditform").prepend('<input type="hidden" value="' + id + '" name="sRecordRevisionId">');
                        document.cmseditform.elements['module_fnc[contentmodule]'].value = 'ActivateRevision';
                        $('#cmseditform').submit();
                        $(this).dialog('close');
                    },
                    '<?=TGlobal::Translate('chameleon_system_core.action.abort'); ?>':function () {
                        $(this).dialog('close');
                    }
                }
            });
        }
            <?php
        }
        ?>
    </script>
<?php
if ($bRevisionManagementActive) {
            ?>
    <div id="addNewRevisionDialog" style="display:none;">
        <div class="tableeditcontainer">
            <form name="addNewRevisionForm" id="addNewRevisionForm" action="">
                <h1><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.record_revision.header_new_revision')); ?></h1>

                <div class="notice" style="margin-bottom: 5px;">
                    <?=TGlobal::Translate('chameleon_system_core.record_revision.new_revision_help'); ?>
                </div>

                <table border="0" cellpadding="3" cellspacing="0" width="100%">
                    <tr class="evenrow">
                        <td class="leftTD"><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.record_revision.new_revision_number')); ?></td>
                        <td class="rightTD"><?=($iLastRevisionNumber + 1); ?></td>
                    </tr>
                    <tr class="oddrow">
                        <td class="leftTD"><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.record_revision.name')); ?></td>
                        <td class="rightTD"><input type="text" name="cmsrevisionname" id="cmsrevisionname"
                                                   value="<?=TGlobal::OutHTML($oTable->GetName()); ?>"
                                                   style="width: 400px;"/></td>
                    </tr>
                    <tr class="evenrow">
                        <td class="leftTD"><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.record_revision.description')); ?></td>
                        <td class="rightTD"><textarea name="cmsrevisiondescription" id="cmsrevisiondescription"
                                                      style="width: 400px; height: 100px;" rows="10"
                                                      cols="30"></textarea></td>
                    </tr>
                    <tr class="evenrow">
                        <td class="leftTD">&nbsp;</td>
                        <td class="rightTD"><?php
                            echo TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.record_revision.action_new_revision'), 'javascript:SaveNewRevision()', URL_CMS.'/images/icons/control_fastforward_blue.png'); ?>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
<?php
        }
?>
<script type="text/javascript">
    if (!parent.framestosave) parent.framestosave = new Array();
</script>
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
            toasterMessage('<?=$sTmpName; ?><?=\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.template_engine.msg_copy_success'); ?>', 'MESSAGE');
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
      toasterMessage('".\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_table_editor.msg_translation_created')."','WARNING');
    });
    ";
        }

        if (isset($aMessages) && is_array($aMessages) && count($aMessages) > 0) {
            ?>
        $(document).ready(function () {
            <?php
            foreach ($aMessages as $aMessage) {
                if (isset($aMessage['sMessageRefersToField'])) {
                    // add the class to field
                    echo sprintf('$("#fieldname_%s").addClass("bg-" + CHAMELEON.CORE.MTTableEditor.mapChameleonMessageTypeToBootstrapStyle("%s"));',
                        TGlobal::OutJS($aMessage['sMessageRefersToField']),
                        TGlobal::OutJS($aMessage['sMessageType'])
                        );
                } ?>toasterMessage('<?=$aMessage['sMessage']; ?>', '<?=TGlobal::OutJS($aMessage['sMessageType']); ?>');<?php
            } ?>
        });
            <?php
        }
        ?>
    </script>

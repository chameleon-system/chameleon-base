<?php
/**
 * @deprecated since 6.2.0 - Chameleon has a new media manager
 **/
if (!empty($data['errorMessage'])) {
    echo '<div class="errorMessage">'.$data['errorMessage'].'</div>';
} else {
    if (count($data['dirListing']) > 0) {
        ?>
    <div style="padding-bottom: 10px;">
        <?=TGlobal::Translate('chameleon_system_core.cms_module_media_local_import.select_source_path'); ?>:
    </div>
    <form method="get" name="importForm" action="<?=PATH_CMS_CONTROLLER; ?>" accept-charset="UTF-8">
        <input type="hidden" name="pagedef" value="CMSMediaLocalImport"/>
        <input type="hidden" name="nodeID" value="<?=$data['nodeID']; ?>"/>
        <input type="hidden" name="module_fnc[contentmodule]" value="ImportFiles"/>
        <select name="directory" class="form-control form-control-sm">
            <?php
            foreach ($data['dirListing'] as $aDir) {
                $sValue = $aDir['directory'];
                if ('base' == $aDir['directory']) {
                    $sValue = '/';
                }

                $sName = $aDir['directory'];
                if ('base' == $aDir['directory']) {
                    $sName = TGlobal::Translate('chameleon_system_core.cms_module_media_local_import.root_path');
                }
                echo '<option value="'.$sValue.'">'.$sName.' ('.$aDir['filecount'].' '.TGlobal::Translate('chameleon_system_core.cms_module_media_local_media.file_count', array('%count%' => $aDir['filecount'])).")</option>\n";
            } ?>
        </select>

        <div style="padding-top: 10px;"><?php
            echo TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.cms_module_media_local_media.action_import'), "javascript:CHAMELEON.CORE.showProcessingModal();document.importForm.submit();", URL_CMS.'/images/icons/database_add.png'); ?></div>
    </form>

    <?php
    }

    if (isset($data['fileErrors'])) {
        foreach ($data['fileErrors'] as $errorMessage) {
            echo '<div class="errorMessage">'.$errorMessage.'</div>';
        }
    }

    if (isset($data['importSuccess'])) {
        ?>
    <div style="padding-top: 20px; padding-bottom: 10px;">
        <h1><?=TGlobal::Translate('chameleon_system_core.cms_module_media_local_media.import_result'); ?>:</h1></div>
    <script type="text/javascript">
        top.showFileList('<?=$nodeID; ?>');
    </script>
    <?php
        foreach ($data['importSuccess'] as $fileName) {
            echo $fileName.'<br />';
        }
    }
}
?>
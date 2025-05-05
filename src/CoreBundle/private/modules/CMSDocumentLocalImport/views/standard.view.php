<?php
if (!empty($data['errorMessage'])) {
    echo '<div class="alert alert-info">'.$data['errorMessage'].'</div>';
} else {
    if (count($data['dirListing']) > 0) {
        ?>
    <div style="padding-bottom: 10px;">
        <?php echo ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_local_import.select_target_folder'); ?>:
    </div>
    <form method="get" name="importForm" action="<?php echo PATH_CMS_CONTROLLER; ?>" accept-charset="UTF-8">
        <input type="hidden" name="pagedef" value="CMSDocumentLocalImport"/>
        <input type="hidden" name="nodeID" value="<?php echo $data['nodeID']; ?>"/>
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
                    $sName = ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_local_import.root_path');
                }
                echo '<option value="'.$sValue.'">'.$sName.' ('.
                    ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans(
                        'chameleon_system_core.document_local_import.file_count',
                        [
                            '%count%' => $aDir['filecount'],
                        ]
                    )
                    .")</option>\n";
            } ?>
        </select>

        <div style="padding-top: 10px;">
            <div><?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_local_import.mark_as_private')); ?></div>
            <input type="radio" name="private" value="1"
                   checked="checked"> <?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_boolean.yes')); ?>&nbsp;&nbsp;
            <input type="radio" name="private" value="0"> <?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_boolean.no')); ?>
        </div>
        <div style="padding-top: 10px;"><?php
            echo TCMSRender::DrawButton(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_local_import.action_import'), 'javascript:CHAMELEON.CORE.showProcessingModal();document.importForm.submit();', 'fas fa-file-import'); ?></div>
    </form>

    <?php
    }

    if (isset($data['fileErrors'])) {
        foreach ($data['fileErrors'] as $errorMessage) {
            echo '<div class="alert alert-danger">'.$errorMessage.'</div>';
        }
    }

    if (isset($data['importSuccess'])) {
        ?>
    <div style="padding-top: 20px; padding-bottom: 10px;">
        <h1><?php echo ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_local_import.import_result'); ?>:</h1></div>
    <script type="text/javascript">
        top.showFileList('<?php echo $nodeID; ?>');
    </script>
    <?php
        foreach ($data['importSuccess'] as $fileName) {
            echo $fileName.'<br />';
        }
    }
}
?>
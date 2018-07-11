<?php
$translator = \ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
?>

CHAMELEON.UPDATE_MANAGER.setConfig({
        text: {
            selectFile: "<?= $translator->trans('chameleon_system_core.cms_module_update.single_update_select_file'); ?>",
            progressBarRunning: "<?= $translator->trans('chameleon_system_core.cms_module_update.progress_bar_running'); ?>",
            progressBarFinished: "<?= $translator->trans('chameleon_system_core.cms_module_update.progress_bar_finished'); ?>",
            successfulQueriesShow: "<?= $translator->trans('chameleon_system_core.cms_module_update.show_successful_queries'); ?>",
            successfulQueriesHide: "<?= $translator->trans('chameleon_system_core.cms_module_update.hide_successful_queries'); ?>"
        }
    });

<?php

echo 'CHAMELEON.UPDATE_MANAGER.addPostUpdateCommand("ajaxProxyUpdateAllTables", "'.TGlobal::OutJS($translator->trans('chameleon_system_core.cms_module_update.updating_all_table_objects')).'");';
echo 'CHAMELEON.UPDATE_MANAGER.addPostUpdateCommand("ajaxProxyUpdateVirtualNonDbClasses", "'.TGlobal::OutJS($translator->trans('chameleon_system_core.cms_module_update.update_all_virtual_objects')).'");';

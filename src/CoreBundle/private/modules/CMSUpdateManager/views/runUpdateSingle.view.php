<?php

$translator = \ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
$updatesByBundle = $oUpdateManager->getAllUpdateFiles();
$bundlesWithUpdates = array_keys($updatesByBundle);

?>
<script type="text/javascript">
    $(document).ready(function () {
        CHAMELEON.UPDATE_MANAGER.setUpdateFiles(<?=json_encode($updatesByBundle); ?>);
        <?php include __DIR__.'/updateManagerConfig.inc.php'; ?>
        CHAMELEON.UPDATE_MANAGER.initSingleUpdate();
    });
</script>
<div id="updatemanager">
    <div class="card">
        <div class="card-header">
            <h1><?= $translator->trans('chameleon_system_core.cms_module_update.headline'); ?></h1>
        </div>
        <div class="card-body">
            <div id="infoContainer">
                <div class="mb-2">
                    <div id="infoContainerText">
                        <?= $translator->trans('chameleon_system_core.cms_module_update.single_file_update_help'); ?>
                    </div>
                    <div class="singleUpdateSelectChoices">
                        <div class="callout callout-info">
                            <h4><?= $translator->trans('chameleon_system_core.cms_module_update.single_update_select_bundle_label'); ?></h4>

                            <select id="singleUpdateSelectBundle" class="form-control form-control-sm">
                                <option value="NULL"><?= $translator->trans('chameleon_system_core.cms_module_update.single_update_select_bundle'); ?></option>
                                <?php
                                foreach ($bundlesWithUpdates as $bundleName) {
                                    echo "<option>$bundleName</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="callout callout-info">
                            <h4><?= $translator->trans('chameleon_system_core.cms_module_update.single_update_select_file'); ?></h4>
                            <select id="singleUpdateSelectFile" disabled="disabled"
                                    class="form-control form-control-sm">
                                <option value="NULL"><?= $translator->trans('chameleon_system_core.cms_module_update.single_update_select_bundle_first'); ?></option>
                            </select>
                        </div>
                    </div>
                    <ul>
                    </ul>
                    <a class="btn btn-primary disabled"
                       id="btnSetUpdate"><?= $translator->trans('chameleon_system_core.cms_module_update.action_select_single_update'); ?></a>
                </div>
                <div class="progress mb-3 position-relative" id="updateProgressBarContainer">
                    <div id="updateProgressBar" class="progress-bar progress-bar-animated" role="progressbar"
                         style="width: 1%;" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100"></div>
                    <h4 id="updateProgressBarText"
                        class="justify-content-center d-flex position-absolute w-100"><?= $translator->trans('chameleon_system_core.cms_module_update.progress_bar_initial'); ?>
                        (0%)</h4>
                </div>

                <a class="btn btn-warning" id="btnGoBack"
                   href="<?= PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript(array('pagedef' => 'main')); ?>"><?= $translator->trans('chameleon_system_core.action.return_to_main_menu'); ?></a>
                <a class="btn btn-success disabled" href="#" id="btnRunUpdates"
                   data-loading-text="<?= $translator->trans('chameleon_system_core.cms_module_update.action_update'); ?>"><?= $translator->trans('chameleon_system_core.cms_module_update.action_update'); ?></a>
                <div id="ajaxTimeoutContainer">
                    <label for="ajaxTimeoutSelect"><?= $translator->trans('chameleon_system_core.cms_module_update.select_ajax_timeout'); ?>
                        : </label>
                    <select id="ajaxTimeoutSelect" class="form-control form-control-sm">
                        <option value="120"><?= TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.ajax_timeout', array('%minutes%' => 120))); ?></option>
                    </select>
                </div>

                <?php include __DIR__.'/globalOutput.inc.php'; ?>

                <div class="clear"></div>
            </div>
            <div id="updateManagerOutput" class="d-none"></div>
        </div>
    </div>
</div>





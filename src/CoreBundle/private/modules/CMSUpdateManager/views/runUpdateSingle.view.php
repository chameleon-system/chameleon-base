<?php

$translator = \ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
$updatesByBundle = $oUpdateManager->getAllUpdateFiles();
$bundlesWithUpdates = array_keys($updatesByBundle);

?>
<script type="text/javascript">

    CHAMELEON.UPDATE_MANAGER.setUpdateFiles(<?=json_encode($updatesByBundle); ?>);

    <?php include __DIR__.'/updateManagerConfig.inc.php'; ?>

    $(document).ready(function(){
        CHAMELEON.UPDATE_MANAGER.initSingleUpdate();
    });

</script>

<div style="padding: 20px;">
    <div id="updatemanager">
        <h1 style="margin-top: 0;"><?=$translator->trans('chameleon_system_core.cms_module_update.headline'); ?></h1>
        <div id="infoContainer">
            <div id="singleUpdateSelectContainer">
                <div id="infoContainerText">
                    <?=$translator->trans('chameleon_system_core.cms_module_update.single_file_update_help'); ?>
                </div>
                <div class="singleUpdateSelectChoices">
                    <div>
                        <div>
                            <?= $translator->trans('chameleon_system_core.cms_module_update.single_update_select_bundle_label'); ?>&nbsp;
                        </div>
                        <div>
                             <select id="singleUpdateSelectBundle">
                                 <option value="NULL"><?=$translator->trans('chameleon_system_core.cms_module_update.single_update_select_bundle'); ?></option>
                                <?php
                                    foreach ($bundlesWithUpdates as $bundleName) {
                                        echo "<option>$bundleName</option>";
                                    }
                                ?>
                                </select>
                        </div>
                    </div>
                    <div>
                        <div>
                            <?= $translator->trans('chameleon_system_core.cms_module_update.single_update_select_file'); ?>&nbsp;
                        </div>
                        <div>
                            <select id="singleUpdateSelectFile" disabled="disabled">
                                <option value="NULL"><?=$translator->trans('chameleon_system_core.cms_module_update.single_update_select_bundle_first'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <ul>
                </ul>
                <a class="button disabled" id="btnSetUpdate"><?=$translator->trans('chameleon_system_core.cms_module_update.action_select_single_update'); ?></a>
            </div>
            <ul id="updateInfoList"></ul>

            <div id="updateProgressBarContainer">
                <div id="updateProgressBarInner"><?= $translator->trans('chameleon_system_core.cms_module_update.progress_bar_initial'); ?></div>
                <div id="updateProgressBar" class="orange" style="width: 0%;"></div>
            </div>

            <a class="btn btn-warning" id="btnGoBack" href="<?=PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript(array('pagedef' => 'main')); ?>"><?=$translator->trans('chameleon_system_core.action.return_to_main_menu'); ?></a>
            <a class="btn disabled" href="#" id="btnRunUpdates" data-loading-text="<?=$translator->trans('chameleon_system_core.cms_module_update.action_update'); ?>"><?=$translator->trans('chameleon_system_core.cms_module_update.action_update'); ?></a>
            <div id="ajaxTimeoutContainer">
                <label for="ajaxTimeoutSelect"><?=$translator->trans('chameleon_system_core.cms_module_update.select_ajax_timeout'); ?>: </label>
                <select id="ajaxTimeoutSelect">
                    <option value="120"><?=TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.ajax_timeout', array('%minutes%' => 120))); ?></option>
                </select>
            </div>

            <?php include __DIR__.'/globalOutput.inc.php'; ?>

            <div class="clear"></div>
        </div>
        <div id="updateManagerOutput" class="hide"></div>
    </div>

</div>
<?php
use ChameleonSystem\UpdateCounterMigrationBundle\Exception\InvalidMigrationCounterException;

/**
 * @var TCMSUpdateManager $oUpdateManager
 */
$translator = \ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
try {
    $updatesByBundle = $oUpdateManager->getAllUpdateFilesToProcess();
} catch (InvalidMigrationCounterException $e) {
    echo '<div style="padding: 20px;">';
    echo $translator->trans('chameleon_system_core.cms_module_update.error_invalid_counter_names');
    echo '<br /><br /><ul>';
    foreach ($e->getInvalidCounters() as $invalidCounter) {
        echo "<li>$invalidCounter</li>";
    }
    echo '</ul>';
    echo '</div>';

    return;
}
$blacklistedUpdates = $oUpdateManager->getUpdateBlacklist();
?>
<script type="text/javascript">

    CHAMELEON.UPDATE_MANAGER.setUpdateFiles(<?=json_encode($updatesByBundle); ?>);

    <?php include __DIR__.'/updateManagerConfig.inc.php'; ?>

    CHAMELEON.UPDATE_MANAGER.addPostUpdateCommand('ajaxProxyClearCache', "<?=TGlobal::OutJS($translator->trans('chameleon_system_core.cms_module_update.clearing_cache')); ?>");

    $(document).ready(function(){
        CHAMELEON.UPDATE_MANAGER.init();
    });

</script>

<?php
if (0 === \count($updatesByBundle)) {
    ?>
    <div id="updatemanager">
        <h1 style="margin-top: 0;"><?=$translator->trans('chameleon_system_core.cms_module_update.headline'); ?></h1>
        <div id="infoContainer">
            <div id="infoContainerText">
                <?= $translator->trans('chameleon_system_core.cms_module_update.update_info_no_updates'); ?>
            </div>
        </div>
        <a class="btn btn-warning" id="btnGoBack" href="<?=PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript(array('pagedef' => 'main')); ?>"><?=TGlobal::OutHTML($translator->trans('chameleon_system_core.action.return_to_main_menu')); ?></a>
    </div>
<?php
    return;
}
?>

    <div id="updatemanager">
        <h1 style="margin-top: 0;"><?=$translator->trans('chameleon_system_core.cms_module_update.headline'); ?></h1>
        <div id="infoContainer">
            <div id="infoContainerText">
                <?= $translator->trans('chameleon_system_core.cms_module_update.update_info'); ?>
            </div>
            <div id="updateCountInfo">
                <div>
                    <div><?= $translator->trans('chameleon_system_core.cms_module_update.updates_total'); ?> </div>
                    <div class="update-count count-total">0</div>
                </div>
                <div>
                    <div><?= $translator->trans('chameleon_system_core.cms_module_update.updates_pending'); ?> </div>
                    <div class="update-count count-pending">0</div>
                </div>
                <div>
                    <div><?= $translator->trans('chameleon_system_core.cms_module_update.updates_processed'); ?> </div>
                    <div class="update-count count-processed">0</div>
                </div>
                <div>
                    <div><?= $translator->trans('chameleon_system_core.cms_module_update.updates_executed'); ?> </div>
                    <div class="update-count count-executed green">0</div>
                </div>
                <div>
                    <div><?= $translator->trans('chameleon_system_core.cms_module_update.updates_skipped'); ?> </div>
                    <div class="update-count count-skipped orange">0</div>
                </div>
            </div>

            <div id="updateProgressBarContainer">
                <div id="updateProgressBarInner"><?= $translator->trans('chameleon_system_core.cms_module_update.progress_bar_initial'); ?></div>
                <div id="updateProgressBar" class="orange" style="width: 0%;"></div>
            </div>
            <?php if (count($blacklistedUpdates) > 0) {
    ?>
                <div class="alert alert-danger" role="alert">
                    <h3><?= $translator->trans('chameleon_system_core.cms_module_update.updates_blacklisted'); ?></h3>
                    <ul>
                        <?php
                        foreach ($blacklistedUpdates as $blackListedBundle => $blackListedUpdates) {
                            echo "<li>
                                      <strong>{$blackListedBundle}</strong>
                                      <ul>";
                            foreach ($blackListedUpdates as $blacklistedBuildNumber) {
                                echo "<li>{$blacklistedBuildNumber}</li>";
                            }
                            echo '</ul></li>';
                        } ?>
                    </ul>
                </div>
            <?php
} ?>

            <a class="btn btn-warning" id="btnGoBack" href="<?=PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript(array('pagedef' => 'main')); ?>"><?=TGlobal::OutHTML($translator->trans('chameleon_system_core.action.return_to_main_menu')); ?></a>
            <a class="btn btn-success" href="#" id="btnRunUpdates" data-loading-text="<?=TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.action_update')); ?>"><?=TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.action_update')); ?></a>
            <div id="ajaxTimeoutContainer">
                <label for="ajaxTimeoutSelect"><?=TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.select_ajax_timeout')); ?>: </label>
                <select id="ajaxTimeoutSelect" class="form-control form-control-sm">
                    <option value="120"><?=TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.ajax_timeout', array('%minutes%' => 120))); ?></option>
                </select>
            </div>

            <?php include __DIR__.'/globalOutput.inc.php'; ?>

            <div class="clear"></div>
        </div>
        <div id="updateManagerOutput" class="hide"></div>
    </div>

    <ul class="hide">
        <?php

        foreach ($updatesByBundle as $updates) {
            foreach ($updates as $update) {
                echo "<li>{$update->fileName}</li>\n";
            }
        }

        ?>
    </ul>

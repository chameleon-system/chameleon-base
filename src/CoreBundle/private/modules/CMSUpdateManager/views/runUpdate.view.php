<?php

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\UpdateCounterMigrationBundle\Exception\InvalidMigrationCounterException;

/** @var $data array<string, mixed> */

/**
 * @var TCMSUpdateManager $oUpdateManager
 */
$translator = ServiceLocator::get('translator');
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
    $(document).ready(function () {
        CHAMELEON.UPDATE_MANAGER.setUpdateFiles(<?php echo json_encode($updatesByBundle); ?>);
        <?php include __DIR__.'/updateManagerConfig.inc.php'; ?>
        CHAMELEON.UPDATE_MANAGER.addPostUpdateCommand('ajaxProxyClearCache', "<?php echo TGlobal::OutJS($translator->trans('chameleon_system_core.cms_module_update.clearing_cache')); ?>");
        CHAMELEON.UPDATE_MANAGER.init();
    });
</script>

<?php
if (0 === \count($updatesByBundle)) {
    ?>
    <div id="updatemanager">

        <div class="card">
            <div class="card-header">
                <h1><?php echo TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.headline')); ?></h1>
            </div>
            <div class="card-body" id="infoContainer">
                <div class="alert alert-info">
                    <?php echo TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.update_info_no_updates')); ?>
                </div>
                <?php if (null !== $data['redirectUrl']) { ?>
                <a class="btn btn-primary" href="<?php echo $data['redirectUrl']; ?>">
                    <i class="fas fa-arrow-alt-circle-right"></i>
                    <?php echo TGlobal::OutHTML($translator->trans('chameleon_system_core.action.redirect_to_login_url')); ?>
                </a>
                <?php } ?>
                <a class="btn btn-warning" id="btnGoBack" href="<?php echo PATH_CMS_CONTROLLER; ?>">
                    <i class="fas fa-home"></i>
                    <?php echo TGlobal::OutHTML($translator->trans('chameleon_system_core.action.return_to_main_menu')); ?>
                </a>
            </div>
        </div>
    <?php
    return;
}
?>
<div id="updatemanager">
    <div class="card">
        <div class="card-header">
            <h1><?php echo TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.headline')); ?></h1>
        </div>
        <div class="card-body">
            <div id="infoContainerText">
                <?php echo TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.update_info')); ?>
            </div>
            <div id="updateCountInfo">
                <div class="row">
                    <div class="col-sm-2">
                        <div class="callout callout-info">
                            <small class="text-muted"><?php echo TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.updates_total')); ?></small><br>
                            <strong class="h4 update-count count-total">0</strong>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="callout callout-info">
                            <small class="text-muted"><?php echo TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.updates_pending')); ?></small><br>
                            <strong class="h4 update-count count-pending">0</strong>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="callout callout-info">
                            <small class="text-muted"><?php echo TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.updates_processed')); ?></small><br>
                            <strong class="h4 update-count count-processed">0</strong>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="callout callout-success">
                            <small class="text-muted"><?php echo TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.updates_executed')); ?></small><br>
                            <strong class="h4 update-count count-executed">0</strong>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="callout callout-warning">
                            <small class="text-muted"><?php echo TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.updates_skipped')); ?></small><br>
                            <strong class="h4 update-count count-skipped">0</strong>
                        </div>
                    </div>

                </div>
            </div>

            <div class="progress mb-3 position-relative" id="updateProgressBarContainer">
                <div id="updateProgressBar" class="progress-bar progress-bar-animated" role="progressbar" style="width: 1%;" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100"></div>
                <h4 id="updateProgressBarText" class="justify-content-center d-flex position-absolute w-100"><?php echo TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.progress_bar_initial')); ?> (0%)</h4>
            </div>
            <?php if (count($blacklistedUpdates) > 0) {
                ?>
                <div class="alert alert-danger" role="alert">
                    <h3><?php echo TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.updates_blacklisted')); ?></h3>
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

            <a class="btn btn-warning" id="btnGoBack" href="<?php echo PATH_CMS_CONTROLLER; ?>">
                <i class="fas fa-home"></i>
                <?php echo TGlobal::OutHTML($translator->trans('chameleon_system_core.action.return_to_main_menu')); ?>
            </a>
            <?php if (null !== $data['redirectUrl']) { ?>
                <a class="btn btn-primary" href="<?php echo $data['redirectUrl']; ?>">
                    <i class="fas fa-arrow-alt-circle-right"></i>
                    <?php echo TGlobal::OutHTML($translator->trans('chameleon_system_core.action.redirect_to_login_url')); ?>
                </a>
            <?php } ?>
            <a class="btn btn-success" href="#" id="btnRunUpdates"
               data-loading-text="<?php echo TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.action_update')); ?>">
                <i class="far fa-play-circle"></i>
                <?php echo TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.action_update')); ?>
            </a>
            <?php include __DIR__.'/globalOutput.inc.php'; ?>

            <div class="clear"></div>
        </div>
        <div id="updateManagerOutput" class="d-none"></div>
    </div>
</div>
<ul class="d-none">
    <?php

                foreach ($updatesByBundle as $updates) {
                    foreach ($updates as $update) {
                        echo "<li>{$update->fileName}</li>\n";
                    }
                }

?>
</ul>

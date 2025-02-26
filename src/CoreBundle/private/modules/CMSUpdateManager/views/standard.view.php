<?php
/** @var $data array<string, mixed> */
$translator = ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
?>
<div id="updatemanager">
<form id="updateForm">
    <input type="hidden" name="pagedef" value="CMSUpdateManager"/>
    <input type="hidden" name="module_fnc[contentmodule]" id="module_fnc" value=""/>

    <div class="card">
        <div class="card-header">
            <h1><?php echo TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.headline')); ?></h1>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <?php echo TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.intro_text')); ?>
            </div>
            <div class="mt-2">
                <a class="btn btn-secondary" onclick="document.getElementById('module_fnc').value='RunUpdates';document.getElementById('updateForm').submit();">
                    <i class="far fa-eye"></i>
                    <?php echo TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.show_all')); ?>
                </a>
                <?php if (null !== $data['redirectUrl']) { ?>
                    <br />
                    <br />
                    <a class="btn btn-primary" href="<?php echo $data['redirectUrl']; ?>">
                        <i class="fas fa-arrow-alt-circle-right"></i>
                        <?php echo TGlobal::OutHTML($translator->trans('chameleon_system_core.action.redirect_to_login_url')); ?>
                    </a>
                <?php } ?>
            <br />
            <br />
            <a class="btn btn-warning" id="btnGoBack" href="<?php echo PATH_CMS_CONTROLLER; ?>">
                <i class="fas fa-home"></i>
                <?php echo TGlobal::OutHTML($translator->trans('chameleon_system_core.action.return_to_main_menu')); ?>
            </a>
        </div>
    </div>
</form>
</div>

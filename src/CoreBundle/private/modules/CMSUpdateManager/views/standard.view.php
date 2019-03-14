<?php
$translator = \ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
?>
<div id="updatemanager">
<form id="updateForm">
    <input type="hidden" name="pagedef" value="CMSUpdateManager"/>
    <input type="hidden" name="module_fnc[contentmodule]" id="module_fnc" value=""/>

    <div class="card">
        <div class="card-header">
            <h1><?= TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.headline')); ?></h1>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <?= TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.intro_text')); ?>
            </div>
            <div class="mt-2">
                <a class="btn btn-secondary" onclick="document.getElementById('module_fnc').value='RunUpdates';document.getElementById('updateForm').submit();">
                    <i class="far fa-eye"></i>
                    <?= TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.show_all')); ?>
                </a>
            <?php
            if (_DEVELOPMENT_MODE) {
                ?>
                <br /><br />
                <a class="btn btn-secondary" onclick="document.getElementById('module_fnc').value='RunUpdateSingle';document.getElementById('updateForm').submit();">
                    <i class="far fa-play-circle"></i>
                    <?= TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.select_single_update')); ?>
                </a>
                <?php
            }
            ?>

            <br />
            <br />
            <a class="btn btn-warning" id="btnGoBack" href="<?= PATH_CMS_CONTROLLER; ?>">
                <i class="fas fa-home"></i>
                <?= TGlobal::OutHTML($translator->trans('chameleon_system_core.action.return_to_main_menu')); ?>
            </a>
        </div>
    </div>
</form>
</div>

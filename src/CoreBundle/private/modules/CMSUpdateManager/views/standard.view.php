<?php
$translator = \ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
?>
<div id="updatemanager">
<form id="updateForm">
    <input type="hidden" name="pagedef" value="CMSUpdateManager"/>
    <input type="hidden" name="module_fnc[contentmodule]" id="module_fnc" value=""/>

    <div class="card">
        <div class="card-header">
            <h1><?= $translator->trans('chameleon_system_core.cms_module_update.headline'); ?></h1>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <?= $translator->trans('chameleon_system_core.cms_module_update.intro_text'); ?>
            </div>
            <div class="mt-2">
            <?php
            echo TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.cms_module_update.show_all'), "javascript:document.getElementById('module_fnc').value='RunUpdates';document.getElementById('updateForm').submit();", 'far fa-eye');
            if (_DEVELOPMENT_MODE) {
                echo '<br /><br />';
                echo TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.cms_module_update.select_single_update'), "javascript:document.getElementById('module_fnc').value='RunUpdateSingle';document.getElementById('updateForm').submit();", 'far fa-play-circle');
            }
            ?>
        </div>
    </div>
</form>
</div>

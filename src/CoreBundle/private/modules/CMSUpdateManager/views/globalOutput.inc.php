<?php
$translator = \ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
?>

<div id="globalOutputContainer" class="mt-5">
    <div id="updateFatalErrorContainer" class="card text-white bg-danger mb-3 d-none">
        <div class="card-header"><h4><?= TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.fatal_error_message')); ?></h4></div>
        <div class="card-body">
            <div id="updateFatalError"></div>
        </div>
    </div>

    <div id="updateErrorContainer" class="card text-white bg-warning mb-3 d-none">
        <div class="card-header"><h4><?= TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.error_messages')); ?></h4></div>
        <div class="card-body">
            <h5 class="card-title"><?= TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.error_count')); ?> <span id="count-errors">0</span></h5>
            <ul id="updateErrorList" class="list-group">

            </ul>
        </div>
    </div>

    <div id="updateInfoContainer" class="card text-white bg-info mb-3 d-none">
        <div class="card-header"><h4><?= TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.info_messages')); ?></h4></div>
        <div class="card-body">
            <h5 class="card-title"><?= TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_update.info_count')); ?> <span id="count-info">0</span></h5>
            <ul id="info-list" class="list-group">

            </ul>
        </div>
    </div>
</div>

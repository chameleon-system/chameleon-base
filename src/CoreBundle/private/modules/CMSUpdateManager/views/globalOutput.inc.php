<?php
$translator = \ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
?>

<div id="globalOutputContainer">
    <div id="updateFatalErrorContainer" class="updateMessageContainer hide">
                    <h4><?= $translator->trans('chameleon_system_core.cms_module_update.fatal_error_message'); ?></h4>
                    <div id="updateFatalError"></div>
                </div>
    <div id="updateErrorContainer" class="updateMessageContainer hide">
        <h4><?= $translator->trans('chameleon_system_core.cms_module_update.error_messages'); ?></h4>
        <ul id="updateErrorList" class="updateMessageList">
        <li>
            <?= $translator->trans('chameleon_system_core.cms_module_update.error_count'); ?> <span id="count-errors">0</span>
            <ul id="error-list"></ul>
        </li>
    </ul>
    </div>
    <div id="updateInfoContainer" class=" updateMessageContainer hide">
        <h4><?= $translator->trans('chameleon_system_core.cms_module_update.info_messages'); ?></h4>
        <ul id="updateInfoList" class="updateMessageList">
            <li>
                <?= $translator->trans('chameleon_system_core.cms_module_update.info_count'); ?> <span id="count-info">0</span>
                <ul id="info-list"></ul>
            </li>
        </ul>
    </div>
</div>

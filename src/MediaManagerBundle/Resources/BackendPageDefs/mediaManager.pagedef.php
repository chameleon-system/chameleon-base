<?php

use ChameleonSystem\CoreBundle\ServiceLocator;

$urlGenerator = ServiceLocator::get('chameleon_system_core.media_manager.url_generator');
$layoutTemplate = 'default';
if ($urlGenerator->openStandaloneMediaManagerInNewWindow()) {
    $layoutTemplate = 'popup_iframe';
}

$moduleList = [
    'contentmodule' => [
        'model' => 'chameleon_system_media_manager.backend_module.media_manager',
        'moduleType' => '@ChameleonSystemMediaManagerBundle',
        'view' => 'full',
    ],
];
addDefaultPageTitle($moduleList);
addDefaultHeader($moduleList);
addDefaultSidebar($moduleList);

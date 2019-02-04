<?php

use ChameleonSystem\CoreBundle\ServiceLocator;

$urlGenerator = ServiceLocator::get('chameleon_system_core.media_manager.url_generator');
$layoutTemplate = 'popup_iframe';

$moduleList = array(
    'contentmodule' => array(
        'model' => 'chameleon_system_media_manager.backend_module.media_manager',
        'moduleType' => '@ChameleonSystemMediaManagerBundle',
        'view' => 'full',
    ),
);

addDefaultPageTitle($moduleList);

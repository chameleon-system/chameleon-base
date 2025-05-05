<?php

$layoutTemplate = 'mediaManager';
$moduleList = [
    'content' => [
        'model' => 'chameleon_system_media_manager.backend_module.media_manager_legacy_list',
        'view' => 'standard',
    ],
];

addDefaultPageTitle($moduleList);

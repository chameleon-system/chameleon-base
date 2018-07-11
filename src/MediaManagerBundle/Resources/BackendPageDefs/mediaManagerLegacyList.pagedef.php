<?php

$layoutTemplate = 'mediaManager';

$moduleList = array(
    'pagetitle' => array('model' => 'MTHeader', 'view' => 'title'),
    'content' => array(
        'model' => 'chameleon_system_media_manager.backend_module.media_manager_legacy_list',
        'view' => 'standard',
    ),
);

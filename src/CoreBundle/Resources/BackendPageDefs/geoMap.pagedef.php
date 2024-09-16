<?php

$layoutTemplate = 'popup_iframe';

$moduleList = [
    'contentmodule' => [
        'model' => 'chameleon_system_core.module.map_coordinates',
        'moduleType' => '@CoreBundle',
        'view' => 'standard',
    ],
];

addDefaultPageTitle($moduleList);

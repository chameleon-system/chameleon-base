<?php

$layoutTemplate = 'frame';

$moduleList = [
    'contentmodule' => [
        'model' => 'chameleon_system_core.module.navigation_tree',
        'moduleType' => '@CoreBundle',
        'view' => 'standard',
    ],
];

addDefaultPageTitle($moduleList);

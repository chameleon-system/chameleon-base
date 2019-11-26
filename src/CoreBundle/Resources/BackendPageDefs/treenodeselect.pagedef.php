<?php

$layoutTemplate = 'popup_iframe';

$moduleList = [
    'contentmodule' => [
        'model' => 'chameleon_system_core.module.navigation_tree_single_select',
        'moduleType' => '@CoreBundle',
        'view' => 'standard',
    ],
];

addDefaultPageTitle($moduleList);

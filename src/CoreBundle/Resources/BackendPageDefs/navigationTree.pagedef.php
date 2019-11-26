<?php

$layoutTemplate = 'default';

$moduleList = [
    'contentmodule' => [
        'model' => 'chameleon_system_core.module.navigation_tree',
        'moduleType' => '@CoreBundle',
        'view' => 'standard',
    ],
];

addDefaultPageTitle($moduleList);
addDefaultHeader($moduleList);
addDefaultBreadcrumb($moduleList);
addDefaultSidebar($moduleList);

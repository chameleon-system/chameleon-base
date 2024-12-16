<?php

$layoutTemplate = 'default';
$moduleList = [
    'contentmodule' => [
        'model' => 'chameleon_system_cms_dashboard.backend_module.dashboard',
        'moduleType' => '@ChameleonSystemCmsDashboardBundle',
        'view' => 'dashboard',
    ],
];
addDefaultPageTitle($moduleList);
addDefaultHeader($moduleList);
addDefaultBreadcrumb($moduleList);
addDefaultSidebar($moduleList);
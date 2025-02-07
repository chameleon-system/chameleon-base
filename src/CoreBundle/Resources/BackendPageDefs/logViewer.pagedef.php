<?php

declare(strict_types=1);

$layoutTemplate = 'default';
$moduleList = [
    'contentmodule' => [
        'model' => 'chameleon_system_core.bridge_chameleon_backend_module.log_viewer_backend_module',
        'view' => 'logViewer',
        'moduleType' => '@ChameleonSystemCoreBundle',
        '_suppressHistory' => true,
    ],
];

addDefaultPageTitle($moduleList);
addDefaultHeader($moduleList);
addDefaultBreadcrumb($moduleList);
addDefaultSidebar($moduleList);

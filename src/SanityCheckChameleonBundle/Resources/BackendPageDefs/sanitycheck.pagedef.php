<?php

$layoutTemplate = 'default';
$moduleList = [
    'contentmodule' => [
        'model' => 'chameleon_system_sanity_check_chameleon.module.cms_sanity_check',
        'view' => 'standard',
        'moduleType' => '@ChameleonSystemSanityCheckChameleonBundle',
    ],
];

addDefaultPageTitle($moduleList);
addDefaultHeader($moduleList);
addDefaultBreadcrumb($moduleList);
addDefaultSidebar($moduleList);

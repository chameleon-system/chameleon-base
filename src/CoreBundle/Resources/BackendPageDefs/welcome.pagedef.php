<?php

$layoutTemplate = 'default';
$moduleList = [
    'contentmodule' => [
        'model' => 'chameleon_system_core.module.static_view_module',
        'view' => 'standard',
        'targetView' => 'StaticView/[{language}]/cms_welcome.html.twig',
    ],
];

addDefaultPageTitle($moduleList);
addDefaultHeader($moduleList);
addDefaultBreadcrumb($moduleList);
addDefaultSidebar($moduleList);

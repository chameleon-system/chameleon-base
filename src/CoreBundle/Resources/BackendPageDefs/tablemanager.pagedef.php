<?php

$layoutTemplate = 'default';
$moduleList = [
    'contentmodule' => [
        'model' => 'MTTableManager',
        'view' => 'standard',
    ],
];

addDefaultPageTitle($moduleList);
addDefaultHeader($moduleList);
addDefaultBreadcrumb($moduleList);
addDefaultSidebar($moduleList);

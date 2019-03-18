<?php

$layoutTemplate = 'default';
$moduleList = [
    'contentmodule' => ['model' => 'CMSUpdateManager', 'moduleType' => 'Core', 'view' => 'standard'],
];

addDefaultPageTitle($moduleList);
addDefaultHeader($moduleList);
addDefaultBreadcrumb($moduleList);
addDefaultSidebar($moduleList);

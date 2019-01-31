<?php

$layoutTemplate = 'default';
$moduleList = [
    'contentmodule' => [
        'model' => 'CMSDocumentManager',
        'view' => 'full',
    ],
];

addDefaultPageTitle($moduleList);
addDefaultHeader($moduleList);
addDefaultBreadcrumb($moduleList);
addDefaultSidebar($moduleList);

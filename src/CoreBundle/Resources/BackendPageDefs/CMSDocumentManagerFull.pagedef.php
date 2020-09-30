<?php

$layoutTemplate = 'default';
$moduleList = [
    'contentmodule' => [
        'model' => 'CMSDocumentManager',
        'view' => 'full',
    ],
];

$cmsRightAllowList = ['cms_data_pool_upload'];

addDefaultPageTitle($moduleList);
addDefaultHeader($moduleList);
addDefaultBreadcrumb($moduleList);
addDefaultSidebar($moduleList);

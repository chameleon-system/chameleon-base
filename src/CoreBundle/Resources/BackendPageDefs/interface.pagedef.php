<?php

$layoutTemplate = 'default';
$moduleList = ['contentmodule' => ['model' => 'CMSInterface', 'view' => 'standard', '_suppressHistory' => true]];

addDefaultPageTitle($moduleList);
addDefaultHeader($moduleList);
addDefaultBreadcrumb($moduleList);
addDefaultSidebar($moduleList);

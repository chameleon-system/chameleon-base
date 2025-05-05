<?php

$layoutTemplate = 'default';
$moduleList = ['contentmodule' => ['model' => 'CMSUserRightsOverview', 'view' => 'standard']];

addDefaultPageTitle($moduleList);
addDefaultHeader($moduleList);
addDefaultBreadcrumb($moduleList);
addDefaultSidebar($moduleList);

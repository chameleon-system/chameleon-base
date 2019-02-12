<?php

$layoutTemplate = 'default';
$moduleList = array('contentmodule' => array('model' => 'CMSUserRightsOverview', 'view' => 'standard'));

addDefaultPageTitle($moduleList);
addDefaultHeader($moduleList);
addDefaultBreadcrumb($moduleList);
addDefaultSidebar($moduleList);

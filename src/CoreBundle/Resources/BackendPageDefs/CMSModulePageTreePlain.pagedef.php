<?php

$layoutTemplate = 'default';
$moduleList = array('contentmodule' => array('model' => 'CMSModulePageTree', 'view' => 'standard'));

addDefaultPageTitle($moduleList);
addDefaultHeader($moduleList);
addDefaultBreadcrumb($moduleList);
addDefaultSidebar($moduleList);

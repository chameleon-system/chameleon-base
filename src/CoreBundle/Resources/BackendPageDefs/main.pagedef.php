<?php

$layoutTemplate = 'default';
$moduleList = array('contentmodule' => array('model' => 'MTMenuManager', 'view' => 'standard'));

addDefaultPageTitle($moduleList);
addDefaultHeader($moduleList);
addDefaultBreadcrumb($moduleList);
addDefaultSidebar($moduleList);

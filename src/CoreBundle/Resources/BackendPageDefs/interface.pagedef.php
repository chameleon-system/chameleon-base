<?php

$layoutTemplate = 'default';
$moduleList = array('contentmodule' => array('model' => 'CMSInterface', 'view' => 'standard', '_suppressHistory' => true));

addDefaultPageTitle($moduleList);
addDefaultHeader($moduleList);
addDefaultBreadcrumb($moduleList);
addDefaultSidebar($moduleList);

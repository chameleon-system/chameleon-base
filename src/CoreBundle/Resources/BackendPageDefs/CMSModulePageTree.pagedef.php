<?php
/**
 * @deprecated since 6.3.7 - use navigationTree.pagedef.php instead
 */
$layoutTemplate = 'default';
$moduleList = array('contentmodule' => array('model' => 'CMSModulePageTree', 'view' => 'standard'));

addDefaultPageTitle($moduleList);
addDefaultHeader($moduleList);
addDefaultBreadcrumb($moduleList);
addDefaultSidebar($moduleList);

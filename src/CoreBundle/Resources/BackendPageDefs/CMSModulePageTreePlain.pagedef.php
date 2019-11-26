<?php
/**
 * @deprecated since 6.3.7 - use navigationTreePlain.pagedef.php instead
 */
$layoutTemplate = 'frame';
$moduleList = array('contentmodule' => array('model' => 'CMSModulePageTree', 'view' => 'standard'));

addDefaultPageTitle($moduleList);

<?php
/**
 * @deprecated since 6.2.0 - Chameleon has a new media manager
 */
$layoutTemplate = 'popup_iframe';
$moduleList = array('contentmodule' => array('model' => 'CMSMediaLocalImport', 'view' => 'standard', '_suppressHistory' => true));

addDefaultPageTitle($moduleList);

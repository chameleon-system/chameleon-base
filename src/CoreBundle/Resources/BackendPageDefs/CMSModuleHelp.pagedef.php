<?php

// main layout
$layoutTemplate = 'popup_iframe';

// modules...
$moduleList = array('pagetitle' => array('model' => 'MTHeader', 'view' => 'title'), 'contentmodule' => array('model' => 'CMSModuleHelp', 'view' => 'standard', '_suppressHistory' => true));

// this line needs to be included... do not touch
if (!is_array($moduleList)) {
    $layoutTemplate = '';
}

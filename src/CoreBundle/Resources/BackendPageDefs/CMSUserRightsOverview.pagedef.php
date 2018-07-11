<?php

// main layout
$layoutTemplate = 'popup_iframe';

// modules...
$moduleList = array('pagetitle' => array('model' => 'MTHeader', 'view' => 'title'), 'headerimage' => array('model' => 'MTHeader', 'view' => 'standard'), 'contentmodule' => array('model' => 'CMSUserRightsOverview', 'view' => 'standard'));

// this line needs to be included... do not touch
if (!is_array($moduleList)) {
    $layoutTemplate = '';
}

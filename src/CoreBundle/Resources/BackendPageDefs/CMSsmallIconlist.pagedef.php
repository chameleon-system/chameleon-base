<?php

// main layout
$layoutTemplate = 'popup_iframe';

// modules...
$moduleList = array('pagetitle' => array('model' => 'MTHeader', 'view' => 'title'), 'contentmodule' => array('model' => 'CMSiconList', 'view' => 'standard', 'iconPath' => '/images/icons/'));

// this line needs to be included... do not touch
if (!is_array($moduleList)) {
    $layoutTemplate = '';
}

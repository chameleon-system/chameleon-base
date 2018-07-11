<?php

// main layout
$layoutTemplate = 'popup_window_iframe';

// modules...
$moduleList = array('pagetitle' => array('model' => 'MTHeader', 'view' => 'title'), 'headerimage' => array('model' => 'MTHeader', 'moduleType' => 'Core', 'view' => 'standard'), 'contentmodule' => array('model' => 'CMSNewsletterSubscriberImport', 'moduleType' => 'Core', 'view' => 'standard', '_suppressHistory' => true));

// this line needs to be included... do not touch
if (!is_array($moduleList)) {
    $layoutTemplate = '';
}

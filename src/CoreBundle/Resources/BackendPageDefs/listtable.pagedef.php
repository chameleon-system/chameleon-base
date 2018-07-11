<?php

// main layout
$layoutTemplate = 'tablemanager';

// modules...
$moduleList = array('pagetitle' => array('model' => 'MTHeader', 'view' => 'title'), 'headerimage' => array('model' => 'MTHeader', 'view' => 'standard'), 'tablemanager' => array('model' => 'MTTableManager', 'view' => 'standard'));

// this line needs to be included... do not touch
if (!is_array($moduleList)) {
    $layoutTemplate = '';
}

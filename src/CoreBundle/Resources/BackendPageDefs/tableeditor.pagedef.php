<?php

// main layout
$layoutTemplate = 'default';

// modules...
$moduleList = array('pagetitle' => array('model' => 'MTHeader', 'view' => 'title'), 'headerimage' => array('model' => 'MTHeader', 'view' => 'standard'), 'contentmodule' => array('model' => 'MTTableEditor', 'view' => 'standard'), 'breadcrumb' => array('model' => 'MTHeader', 'view' => 'breadcrumb'));

// this line needs to be included... do not touch
if (!is_array($moduleList)) {
    $layoutTemplate = '';
}

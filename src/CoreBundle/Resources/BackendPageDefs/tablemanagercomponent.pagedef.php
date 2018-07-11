<?php

// main layout
$layoutTemplate = 'frame';

// modules...
$moduleList = array('pagetitle' => array('model' => 'MTHeader', 'view' => 'title'), 'contentmodule' => array('model' => 'MTTableManagerComponent', 'view' => 'standard'));

// this line needs to be included... do not touch
if (!is_array($moduleList)) {
    $layoutTemplate = '';
}

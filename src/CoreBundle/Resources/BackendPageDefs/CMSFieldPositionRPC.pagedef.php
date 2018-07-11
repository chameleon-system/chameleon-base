<?php

// main layout
$layoutTemplate = 'frame';

// modules...
$moduleList = array('contentmodule' => array('model' => 'CMSFieldPositionRPC', 'view' => 'standard'));

// this line needs to be included... do not touch
if (!is_array($moduleList)) {
    $layoutTemplate = '';
}

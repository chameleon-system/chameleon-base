<?php

// main layout
$layoutTemplate = 'JSON';

// modules...
$moduleList = array('module' => array('model' => 'CMSFieldMLTRPC', 'view' => 'standard'));

// this line needs to be included... do not touch
if (!is_array($moduleList)) {
    $layoutTemplate = '';
}

<?php

// main layout
$layoutTemplate = 'empty';

// modules...
$moduleList = array('main' => array('model' => 'MTPassThrough', 'view' => 'standard'));

// this line needs to be included... do not touch
if (!is_array($moduleList)) {
    $layoutTemplate = '';
}

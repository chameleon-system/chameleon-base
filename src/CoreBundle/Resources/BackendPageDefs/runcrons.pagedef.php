<?php

// main layout
$layoutTemplate = 'runcrons';

// modules...
$moduleList = array('main' => array('model' => 'CMSRunCrons', 'view' => 'standard'));

// this line needs to be included... do not touch
if (!is_array($moduleList)) {
    $layoutTemplate = '';
}

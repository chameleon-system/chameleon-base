<?php

// main layout
$layoutTemplate = 'frame';

// modules...
$moduleList = array('contentmodule' => array('model' => 'MTBlogImportCore', 'moduleType' => 'Core', 'view' => 'standard', '_suppressHistory' => true));

// this line needs to be included... do not touch
if (!is_array($moduleList)) {
    $layoutTemplate = '';
}

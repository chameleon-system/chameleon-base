<?php

// main layout
$layoutTemplate = 'wysiwygImageChooser';

// modules...
$moduleList = array('pagetitle' => array('model' => 'MTHeader', 'view' => 'title'), 'module' => array('model' => 'CMSModuleWYSIWYGImage', 'view' => 'standard'));

// this line needs to be included... do not touch
if (!is_array($moduleList)) {
    $layoutTemplate = '';
}

<?php

// main layout
$layoutTemplate = 'ajaxTreePlain';

// modules...
$moduleList = array('pagetitle' => array('model' => 'MTHeader', 'view' => 'title'), 'module' => array('model' => 'CMSModulePageTree', 'view' => 'standard'));

// this line needs to be included... do not touch
if (!is_array($moduleList)) {
    $layoutTemplate = '';
}

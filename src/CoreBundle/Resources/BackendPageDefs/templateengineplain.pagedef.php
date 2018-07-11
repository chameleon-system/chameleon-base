<?php

// main layout
$layoutTemplate = 'templateengineplain';

// modules...
$moduleList = array('pagetitle' => array('model' => 'MTHeader', 'view' => 'title'), 'templateengine' => array('model' => 'CMSTemplateEngine', 'view' => 'main'));

// this line needs to be included... do not touch
if (!is_array($moduleList)) {
    $layoutTemplate = '';
}

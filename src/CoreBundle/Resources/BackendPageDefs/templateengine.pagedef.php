<?php

// main layout
$layoutTemplate = 'templateengine';

// modules...
$moduleList = array('pagetitle' => array('model' => 'MTHeader', 'view' => 'title'), 'headerimage' => array('model' => 'MTHeader', 'view' => 'standard'), 'templateengine' => array('model' => 'CMSTemplateEngine', 'view' => 'main'));

// this line needs to be included... do not touch
if (!is_array($moduleList)) {
    $layoutTemplate = '';
}

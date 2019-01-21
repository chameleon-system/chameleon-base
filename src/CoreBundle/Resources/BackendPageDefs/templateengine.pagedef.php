<?php

// main layout
$layoutTemplate = 'templateengine';

// modules...
$moduleList = array('pagetitle' => array('model' => 'MTHeader', 'view' => 'title'), 'headerimage' => array('model' => 'MTHeader', 'view' => 'standard'), 'templateengine' => array('model' => 'CMSTemplateEngine', 'view' => 'main'), 'breadcrumb' => array('model' => 'MTHeader', 'view' => 'breadcrumb'));

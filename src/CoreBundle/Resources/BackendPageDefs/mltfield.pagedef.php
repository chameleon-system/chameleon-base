<?php

// main layout
$layoutTemplate = 'frame';

// modules...
$moduleList = array(
    'pagetitle' => array(
        'model' => 'MTHeader',
        'view' => 'title',
    ),
    'contentmodule' => array(
        'model' => 'MTTableManager',
        'view' => 'mltField',
        '_suppressHistory' => true,
        'listClass' => 'TCMSListManagerMLT',
    ),
);

// this line needs to be included... do not touch
if (!is_array($moduleList)) {
    $layoutTemplate = '';
}

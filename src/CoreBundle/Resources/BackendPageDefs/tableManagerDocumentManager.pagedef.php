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
        'view' => 'wysiwygImageChooser',
        '_suppressHistory' => true,
        'listClass' => 'TCMSListManagerDocumentManager',
    ),
);

// this line needs to be included... do not touch
if (!is_array($moduleList)) {
    $layoutTemplate = '';
}

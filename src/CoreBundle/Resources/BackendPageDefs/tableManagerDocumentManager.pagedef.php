<?php

$layoutTemplate = 'frame';
$moduleList = [
    'contentmodule' => [
        'model' => 'MTTableManager',
        'view' => 'wysiwygImageChooser',
        '_suppressHistory' => true,
        'listClass' => 'TCMSListManagerDocumentManager',
    ],
];

addDefaultPageTitle($moduleList);

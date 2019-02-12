<?php

$layoutTemplate = 'frame';
$moduleList = [
    'contentmodule' => [
        'model' => 'MTTableManager',
        'view' => 'wysiwygImageChooser',
        '_suppressHistory' => true,
        'listClass' => 'TCMSListManagerWYSIWYGImage',
    ],
];

addDefaultPageTitle($moduleList);

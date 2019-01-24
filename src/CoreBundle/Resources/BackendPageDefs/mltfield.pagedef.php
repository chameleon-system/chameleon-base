<?php

$layoutTemplate = 'frame';
$moduleList = [
    'contentmodule' => [
        'model' => 'MTTableManager',
        'view' => 'mltField',
        '_suppressHistory' => true,
        'listClass' => 'TCMSListManagerMLT',
    ],
];

addDefaultPageTitle($moduleList);

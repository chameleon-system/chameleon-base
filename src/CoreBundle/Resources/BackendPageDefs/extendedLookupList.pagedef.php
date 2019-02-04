<?php

$layoutTemplate = 'frame';
$moduleList = [
    'contentmodule' => [
        'model' => 'MTTableManager',
        'view' => 'iframe',
        'listClass' => 'TCMSListManagerExtendedLookup',
        '_suppressHistory' => true,
    ],
];

addDefaultPageTitle($moduleList);

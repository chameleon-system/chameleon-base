<?php

$layoutTemplate = 'frame';
$moduleList = [
    'contentmodule' => [
        'model' => 'MTTableManager',
        'view' => 'iframe',
        'listClass' => 'TCMSListManagerExtendedLookupModuleInstance',
        '_suppressHistory' => true,
    ],
];

addDefaultPageTitle($moduleList);

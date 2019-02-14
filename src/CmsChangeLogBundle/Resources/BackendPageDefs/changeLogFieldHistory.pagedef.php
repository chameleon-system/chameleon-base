<?php

$layoutTemplate = 'frame';

$moduleList = [
    'contentmodule' => [
        'model' => 'MTTableManager',
        'view' => 'iframe',
        'listClass' => 'TCMSListManagerFieldHistory',
        '_suppressHistory' => true,
    ],
];

addDefaultPageTitle($moduleList);
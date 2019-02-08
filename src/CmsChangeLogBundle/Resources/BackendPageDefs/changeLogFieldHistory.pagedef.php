<?php

$layoutTemplate = 'frame';

$moduleList = [
    'pagetitle' => [
        'model' => 'MTHeader',
        'view' => 'title',
    ],
    'contentmodule' => [
        'model' => 'MTTableManager',
        'view' => 'iframe',
        'listClass' => 'TCMSListManagerFieldHistory',
        '_suppressHistory' => true,
    ],
];

addDefaultPageTitle($moduleList);
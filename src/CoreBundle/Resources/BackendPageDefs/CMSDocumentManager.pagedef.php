<?php

$layoutTemplate = 'frame';
$moduleList = [
    'contentmodule' => [
        'model' => 'CMSDocumentManager',
        'view' => 'standard',
    ],
];

addDefaultPageTitle($moduleList);

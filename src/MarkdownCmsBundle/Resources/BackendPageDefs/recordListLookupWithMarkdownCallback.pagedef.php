<?php

$layoutTemplate = 'frame';
$moduleList = [
    'contentmodule' => [
        'model' => 'MTTableManager',
        'view' => 'iframe',
        'listClass' => 'ChameleonSystem\MarkdownCmsBundle\Bridge\Chameleon\ListManager\ListManagerWithMarkdownEditorCallback',
        '_suppressHistory' => true,
    ],
];

addDefaultPageTitle($moduleList);

<?php

$oDummy = new TPkgViewRendererSnippetDummyData();
$aTabs = [
    [
        'sTitle' => 'Tab1',
        'bIsActive' => true,
        'sURL' => '#',
        'sAjaxURL' => '#ajax',
    ],
    [
        'sTitle' => 'Tab2',
        'bIsActive' => false,
        'sURL' => '#',
        'sAjaxURL' => '#ajax',
    ],
    [
        'sTitle' => 'Tab3',
        'bIsActive' => false,
        'sURL' => '#',
        'sAjaxURL' => '#ajax',
    ],
];
$oDummy->addDummyData('aTabs', $aTabs);
$oDummy->addDummyData('sContent', 'test content');

return $oDummy;

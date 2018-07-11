<?php

$oDummy = new TPkgViewRendererSnippetDummyData();
    $aTabs = array(
        array(
            'sTitle' => 'Tab1',
            'bIsActive' => true,
            'sURL' => '#',
            'sAjaxURL' => '#ajax',
        ),
        array(
            'sTitle' => 'Tab2',
            'bIsActive' => false,
            'sURL' => '#',
            'sAjaxURL' => '#ajax',
        ),
        array(
            'sTitle' => 'Tab3',
            'bIsActive' => false,
            'sURL' => '#',
            'sAjaxURL' => '#ajax',
        ),
    );
$oDummy->addDummyData('aTabs', $aTabs);
$oDummy->addDummyData('sContent', 'test content');

return $oDummy;

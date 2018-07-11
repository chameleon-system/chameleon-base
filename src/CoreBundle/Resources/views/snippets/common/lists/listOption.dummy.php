<?php

$oDummy = new TPkgViewRendererSnippetDummyData();
$aOptionList = array(
        array(
            'sValue' => '',
            'sName' => 'Alphabetisch',
            'bSelected' => true,
        ),
        array(
            'sValue' => '',
            'sName' => 'Preis',
            'bSelected' => false,
        ),
        array(
            'sValue' => '',
            'sName' => 'Erscheinungsdatum',
            'bSelected' => false,
        ),
        array(
            'sValue' => '',
            'sName' => 'Verkaufszahlen',
            'bSelected' => false,
        ),
);

$aListOption = array(
    'sName' => 'Artikel',
    'sFormActionUrl' => '#',
    'sFormHiddenFields' => '',
    'aOptionList' => $aOptionList,
);

$oDummy->addDummyDataArray($aListOption);

return $oDummy;

<?php

$oDummy = new TPkgViewRendererSnippetDummyData();
$aOptionList = [
        [
            'sValue' => '',
            'sName' => 'Alphabetisch',
            'bSelected' => true,
        ],
        [
            'sValue' => '',
            'sName' => 'Preis',
            'bSelected' => false,
        ],
        [
            'sValue' => '',
            'sName' => 'Erscheinungsdatum',
            'bSelected' => false,
        ],
        [
            'sValue' => '',
            'sName' => 'Verkaufszahlen',
            'bSelected' => false,
        ],
];

$aListOption = [
    'sName' => 'Artikel',
    'sFormActionUrl' => '#',
    'sFormHiddenFields' => '',
    'aOptionList' => $aOptionList,
];

$oDummy->addDummyDataArray($aListOption);

return $oDummy;

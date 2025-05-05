<?php

$oData = new TPkgViewRendererSnippetDummyData();

$aData = [
    'sHeadLine' => 'Meine Headline',
    'sText' => 'mein html <b>text</b>',
    'sNewsletterLink' => '#',
    'sModuleSpotName' => 'spota',
    'sMessageGeneral' => 'bei der eingabe der benutzerdaten ist ein fehler aufgetreten',
    'sMessageNewsletterList' => 'beim abonnieren einer newsletter liste ist ein fehler aufgetreten',
    'sFieldNamesPrefix' => 'newsletter_subscription',
    'aFieldSalutation' => [
        'sError' => '',
        'sValue' => '',
        'aValueList' => '',
    ],
    'aFieldFirstName' => [
        'sError',
        'sValue',
    ],
    'aFieldLastName' => [
        'sError',
        'sValue',
    ],
    'aFieldEmail' => [
        'sError',
        'sValue',
    ],
    'aGroupList' => [
        [
            'id' => 1,
            'sName' => 'erste gruppe',
            'sError' => '',
            'bIsChecked' => false,
        ],
        [
            'id' => 2,
            'sName' => 'zweite gruppe',
            'sError' => '',
            'bIsChecked' => false,
        ],
        [
            'id' => 3,
            'sName' => 'dritte gruppe',
            'sError' => '',
            'bIsChecked' => true,
        ],
        [
            'id' => 4,
            'sName' => 'vierte gruppe',
            'sError' => 'le mow',
            'bIsChecked' => false,
        ],
    ],
];

$oData->addDummyDataArray($aData);

return $oData;

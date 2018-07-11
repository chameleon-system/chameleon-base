<?php

$oData = new TPkgViewRendererSnippetDummyData();

$aData = array(
    'sHeadLine' => 'Meine Headline',
    'sText' => 'mein html <b>text</b>',
    'sNewsletterLink' => '#',
    'sModuleSpotName' => 'spota',
    'sMessageGeneral' => 'bei der eingabe der benutzerdaten ist ein fehler aufgetreten',
    'sMessageNewsletterList' => 'beim abonnieren einer newsletter liste ist ein fehler aufgetreten',
    'sFieldNamesPrefix' => 'newsletter_subscription',
    'aFieldSalutation' => array(
        'sError' => '',
        'sValue' => '',
        'aValueList' => '',
    ),
    'aFieldFirstName' => array(
        'sError',
        'sValue',
    ),
    'aFieldLastName' => array(
        'sError',
        'sValue',
    ),
    'aFieldEmail' => array(
        'sError',
        'sValue',
    ),
    'aGroupList' => array(
        array(
            'id' => 1,
            'sName' => 'erste gruppe',
            'sError' => '',
            'bIsChecked' => false,
        ),
        array(
            'id' => 2,
            'sName' => 'zweite gruppe',
            'sError' => '',
            'bIsChecked' => false,
        ),
        array(
            'id' => 3,
            'sName' => 'dritte gruppe',
            'sError' => '',
            'bIsChecked' => true,
        ),
        array(
            'id' => 4,
            'sName' => 'vierte gruppe',
            'sError' => 'le mow',
            'bIsChecked' => false,
        ),
    ),
);

$oData->addDummyDataArray($aData);

return $oData;

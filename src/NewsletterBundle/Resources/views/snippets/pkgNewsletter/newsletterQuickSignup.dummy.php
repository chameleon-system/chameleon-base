<?php

$oData = new TPkgViewRendererSnippetDummyData();

$aData = [
    'sNewsletterLink' => '#',
    'sModuleSpotName' => 'spota',
    'sFieldNamesPrefix' => 'newsletter',
];

$oData->addDummyDataArray($aData);

return $oData;

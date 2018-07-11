<?php

$oData = new TPkgViewRendererSnippetDummyData();

$aData = array(
    'sNewsletterLink' => '#',
    'sModuleSpotName' => 'spota',
    'sFieldNamesPrefix' => 'newsletter',
);

$oData->addDummyDataArray($aData);

return $oData;

<?php

$oData = new TPkgViewRendererSnippetDummyData();

$aData = [
    'sStepName' => 'foo',
    'sHeadLine' => 'hier erscheint die überschrift',
    'sText' => 'dies wäre <b>ihr</b> <u>preis</u> gewesen',
];

$oData->addDummyDataArray($aData);

return $oData;

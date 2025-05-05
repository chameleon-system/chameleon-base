<?php

$oDummy = new TPkgViewRendererSnippetDummyData();
$aListInformation = [
    'sStartItem' => '2.000',
    'sEndItem' => '2.500',
    'sMaxItems' => '201.438',
];

$oDummy->addDummyDataArray($aListInformation);

return $oDummy;

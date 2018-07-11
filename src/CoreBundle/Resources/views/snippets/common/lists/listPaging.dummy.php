<?php

$oDummyData = new TPkgViewRendererSnippetDummyData();
$aListPaging = array(
    'iActivePage' => 11,
    'iLastPage' => 83,
    'sURL' => '#page={[pageNumber]}',
);

$oDummyData->addDummyDataArray($aListPaging);

return $oDummyData;

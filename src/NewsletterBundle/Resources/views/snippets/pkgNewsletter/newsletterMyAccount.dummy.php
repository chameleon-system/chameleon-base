<?php

$oDummyData = new TPkgViewRendererSnippetDummyData();
$aIncludes = ['/common/textBlock/textBlockSmallHeadline.dummy.php'];
foreach ($aIncludes as $sInclude) {
    $oDummyData->addDummyDataFromFile($sInclude);
}
$aTextData = $oDummyData->getDummyData();

$oDummy = new TPkgViewRendererSnippetDummyData();
$oDummy->addDummyData('aTextData', $aTextData);
$oDummy->addDummyData('sNewsletterMessages', '');
$oDummy->addDummyData('bNewsletterSubscribed', true);
$oDummy->addDummyData('sActionLink', '#');

return $oDummy;

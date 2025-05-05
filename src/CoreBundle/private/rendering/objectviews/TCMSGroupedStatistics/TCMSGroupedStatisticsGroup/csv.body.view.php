<?php

$aRow = [];
$oLocal = TCMSLocal::GetActive();
echo $sRowPrefix;
$aRow[] = $sGroupTitle;

$iEmptyGroups = $iMaxGroupDepth - TCMSGroupedStatisticsGroup::$iCurrentLevel;
for ($i = 0; $i < $iEmptyGroups; ++$i) {
    $aRow[] = '';
}
//    $aNameColumns = $oGroup->GetColumnNames();
$dOldVal = 0;
foreach ($aColumnNames as $sName) {
    $dNewVal = $oGroup->GetTotalFor($sName);
    $aRow[] = $oLocal->FormatNumber($dNewVal, 2);
    $dDiff = $dNewVal - $dOldVal;
    if ($bShowDiffColumn) {
        $aRow[] = $oLocal->FormatNumber($dDiff, 2);
    }
    $dOldVal = $dNewVal;
}
echo '"'.implode('"'.$sSeparator.'"', $aRow).'"';
echo "\n";
$sRowPrefix = $sRowPrefix.'""'.$sSeparator;
if (count($aSubGroups) > 0) {
    foreach (array_keys($aSubGroups) as $sSubGroupIndex) {
        echo $aSubGroups[$sSubGroupIndex]->Render('csv.body', $aColumnNames, $iMaxGroupDepth, $bShowDiffColumn, $sRowPrefix, $sSeparator);
    }
}

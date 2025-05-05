<?php
$oLocal = TCMSLocal::GetActive();
?>
<tr>
    <th rowspan="<?php echo $oGroup->GetRowCount(); ?>"><?php echo $sGroupTitle; ?></th>
    <?php
    $iEmptyGroups = $iMaxGroupDepth - TCMSGroupedStatisticsGroup::$iCurrentLevel;
for ($i = 0; $i < $iEmptyGroups; ++$i) {
    // echo '<th>ss&nbsp;</th>';
    echo '<th>Gesamt</th>';
}
//    $aNameColumns = $oGroup->GetColumnNames();
$dOldVal = 0;
foreach ($aColumnNames as $sName) {
    $dNewVal = $oGroup->GetTotalFor($sName);
    echo '<td>'.$oLocal->FormatNumber($dNewVal, 2).'</td>';
    $dDiff = $dNewVal - $dOldVal;
    if ($bShowDiffColumn) {
        echo '<td>'.$oLocal->FormatNumber($dDiff, 2).'</td>';
    }
    $dOldVal = $dNewVal;
}
?>
</tr>
<?php
if (count($aSubGroups) > 0) {
    foreach (array_keys($aSubGroups) as $sSubGroupIndex) {
        echo $aSubGroups[$sSubGroupIndex]->Render('html.body', $aColumnNames, $iMaxGroupDepth, $bShowDiffColumn);
    }
}

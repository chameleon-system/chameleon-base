<?php
$oLocal = TCMSLocal::GetActive();
$iBarHeight = 200; // in pixel
$iTotalBarWidth = 20; // in pixel
$iSubBarWidth = 10; // in pixel
$iGridCount = 10; // number of lines to show max
$iGridDivider = 5; // number the grid lines are always divisible by
$sMainBarColor = '#185EA8'; // color for the first bar (total value)
$aColors = ['#5E98FF', '#807D9A', '#92C1F0', '#A2ACB3', '#7ADEFF']; // colors for the bars
?>
<div class="groupcontainer-barchart">
    <?php
    $dOldVal = 0;
$dMaxVal = $oGroup->GetMaxValue();
$DivideThrough = $dMaxVal / $iGridCount;
$DivideThrough = round($DivideThrough) + ($iGridDivider - round($DivideThrough) % $iGridDivider);
if (0 == $DivideThrough) {
    $dTotalWithoutRemain = $dMaxVal;
} else {
    $dRemain = $dMaxVal % $DivideThrough;
    $dTotalWithoutRemain = $dMaxVal - $dRemain;
}

$dGridHeight = $DivideThrough;
echo '<div class="bar-gridline" style="top:'.($iBarHeight + $iGridDivider - 1).'px;">0</div>';
while ($dGridHeight <= $dTotalWithoutRemain) {
    $topvalue = $iBarHeight - ($iBarHeight / $dMaxVal * $dGridHeight) + $iGridDivider;
    echo '<div class="bar-gridline" style="top:'.$topvalue.'px;">'.$dGridHeight.'</div>';
    $dGridHeight = $dGridHeight + $DivideThrough;
}

foreach ($aColumnNames as $sName) {
    echo '<div class="barchart-item">';
    echo '<div class="columnHeader"><strong>'.$sName.'</strong></div>';
    echo '<div class="columnBars" style="height:'.$iBarHeight.'px;">';
    $dNewVal = $oGroup->GetTotalFor($sName);
    $aColorSubcategory = [];
    echo '<div class="bar-wrapper" style="height:'.$iBarHeight.'px;"><div class="bar" style="background-color:'.$sMainBarColor.';width:'.$iTotalBarWidth.'px;height:'.($iBarHeight / $dMaxVal * $dNewVal).'px"></div></div>';
    if (count($aSubGroups) > 0) {
        $iColorCount = 0;
        $aSubHeight = [];
        $aOldSubHeight = [];
        $aBarHTML = [];
        foreach (array_keys($aSubGroups) as $sSubGroupIndex) {
            $sSubGroupColumn = $aSubGroups[$sSubGroupIndex]->sSubGroupColumn;
            if (!array_key_exists($sSubGroupColumn, $aSubHeight)) {
                $aSubHeight[$sSubGroupColumn] = null;
            }
            if (!array_key_exists($sSubGroupColumn, $aOldSubHeight)) {
                $aOldSubHeight[$sSubGroupColumn] = 0;
            }
            if (!array_key_exists($sSubGroupColumn, $aBarHTML)) {
                $aBarHTML[$sSubGroupColumn] = '';
            }
            if (!array_key_exists($iColorCount, $aColors)) {
                $iColorCount = 0;
            }
            $iValue = $aSubGroups[$sSubGroupIndex]->GetTotalFor($sName);
            if (!is_null($aSubHeight[$sSubGroupColumn])) {
                $aOldSubHeight[$sSubGroupColumn] = $aSubHeight[$sSubGroupColumn];
            }
            $aSubHeight[$sSubGroupColumn] = ($iBarHeight / $dMaxVal * $iValue);
            $aBarHTML[$sSubGroupColumn] .= '<div class="bar '.$sSubGroupColumn.'" style="bottom:'.$aOldSubHeight[$sSubGroupColumn].'px;background-color:'.$aColors[$iColorCount].';width:'.$iSubBarWidth.'px;height:'.$aSubHeight[$sSubGroupColumn].'px"></div>';
            $aColorSubcategory[$sSubGroupColumn][$aColors[$iColorCount]] = $aSubGroups[$sSubGroupIndex]->GetGroupName();
            ++$iColorCount;
        }
        $iSubBarCount = 1;
        foreach ($aBarHTML as $sHTML) {
            if (1 == $iSubBarCount) {
                $iMargin = $iTotalBarWidth;
            } else {
                $iMargin = $iSubBarWidth;
            }
            if (!empty($sHTML)) {
                echo '<div class="bar-wrapper" style="height:'.$iBarHeight.'px;margin-left:'.$iMargin.'px">'.$sHTML.'</div>';
            }
            ++$iSubBarCount;
        }
    }
    echo '</div><div class="cleardiv"></div>'.$oLocal->FormatNumber($dNewVal, 2).'</div>';

    $dOldVal = $dNewVal;
}
?>
    <div class="cleardiv">&nbsp;</div>
    <br/>
    <span class="legend-square"
          style="background-color:<?php echo $sMainBarColor; ?>"></span> <?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_shop.report.total')); ?><br/>
    <?php
if (count($aColorSubcategory) > 0) {
    foreach ($aColorSubcategory as $aRealColorSubcategory) {
        echo '<span class="legend-divider">&nbsp;</span>';
        foreach ($aRealColorSubcategory as $sColor => $sName) {
            echo '<span class="legend-square" style="background-color:'.$sColor.'"></span> '.$sName.'<br />';
        }
    }
}
?>
    <br/>
</div>
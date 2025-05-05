<div class="TCMSGroupedStatistics">
    <div class="html-barchart">
        <?php
        foreach ($aBlocks as $oBlock) {
            // TCMSGroupedStatisticsGroup::$iCurrentLevel = 0;
            echo '<div class="barchart-groupname">'.$oBlock->GetGroupName().'</div><br /><div class="barchart-body">'.$oBlock->Render('barchart.body', $aNameColumns, $iMaxGroupCount, $bShowDiffColumn).'</div><div class="cleardiv"></div><br />';
        }
        ?>
    </div>
</div>
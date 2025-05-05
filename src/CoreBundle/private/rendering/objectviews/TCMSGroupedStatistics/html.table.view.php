<div class="TCMSGroupedStatistics">
    <div class="html-table">
        <table border="1" class="statsTable">
            <tr>
                <?php
                $iTotalWidth = 0;
                for ($i = 0; $i < $iMaxGroupCount; ++$i) {
                    ++$iTotalWidth;
                    echo '<th class="colHeader">&nbsp;</th>';
                }
                foreach ($aNameColumns as $sName) {
                    ++$iTotalWidth;
                    echo '<th class="colHeader">'.$sName.'</th>';
                    if ($bShowDiffColumn) {
                        echo '<th class="colHeader">&Delta;</th>';
                        ++$iTotalWidth;
                    }
                }
                ?>
            </tr>
            <?php
            foreach ($aBlocks as $oBlock) {
                // TCMSGroupedStatisticsGroup::$iCurrentLevel = 0;
                echo $oBlock->Render('html.body', $aNameColumns, $iMaxGroupCount, $bShowDiffColumn);
                echo '<tr><td colspan="'.$iTotalWidth.'">&nbsp;</td></tr>';
            }
                ?>

        </table>
    </div>
</div>
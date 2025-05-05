<tr>
    <?php
    $iMaxSubGroups = $oGroup->GetSubGroupDepth() + 1;
    for ($i = 0; $i < $iMaxSubGroups; ++$i) {
        echo '<th>&nbsp;</th>';
    }
    //    $aNameColumns = $oGroup->GetColumnNames();
    foreach ($aColumnNames as $sName) {
        echo '<td>'.$sName.'</td>';
    }
    ?>
</tr>

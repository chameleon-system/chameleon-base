<?php
/**
 * @deprecated since 6.3.0 - not used anymore.
 */
/** @var $oHTMLTable THTMLTable */
/** @var $oRecordList TCMSRecordList */
/** @var $oColumns TIterator */

/** @var $sListIdentKey string */
/** @var $iCurrentPage int */
/** @var $sSearchTerm string */

/** @var $aCallTimeVars array */
$bShowActionCheckbox = (count($aActions) > 0);
$oRecordList->GoToStart();
$iRowCount = 1;
$sRowStyle = '';
while ($oRow = $oRecordList->Next()) {
    if (0 == ($iRowCount % 2)) {
        $sRowStyle = 'even';
    } else {
        $sRowStyle = 'odd';
    }
    echo "<tr class=\"{$sRowStyle} rownum{$iRowCount} ".$oHTMLTable->GetRowCSSForItem($oRow).'">';
    if ($bShowActionCheckbox) {
        ?>
    <th class="actionColumn"><input type="checkbox" name="aSelectedFiles[]" value="<?php echo TGlobal::OutHTML($oRow->id); ?>"/>
    </th><?php
    }
    $oColumns->GoToStart();
    while ($oColumn = $oColumns->Next()) {
        /** @var $oColumn THTMLTableColumn */
        echo '<td class="'.TGlobal::OutHTML($oColumn->sColumnAlias).' '.$oColumn->GetColumnFormatCSSClass().'">'.$oColumn->GetFieldValue($oRow).'</td>';
    }
    echo '</tr>';
    ++$iRowCount;
}

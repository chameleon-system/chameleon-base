<?php
/**
 * @deprecated since 6.3.0 - not used anymore.
 */
/* @var $oHTMLTable THTMLTable */
/* @var $oRecordList TCMSRecordList */
/* @var $oColumns TIterator */

/* @var $sListIdentKey string */
/* @var $iCurrentPage int */
/* @var $sSearchTerm string */

/* @var $aCallTimeVars array */
$oColumns->GoToStart();
$bShowSortCount = ($oHTMLTable->GetNumberOfOrderedColumns() > 1);
echo '<tr>';
if (count($aActions) > 0) {
    echo '<th class="actionColumn">&nbsp;</th>';
}
while ($oColumn = $oColumns->Next()) {
    /** @var $oColumn THTMLTableColumn */
    $sActiveOrderBy = $oHTMLTable->GetCurrentOrderByForColumn($oColumn);
    echo '<th class="'.TGlobal::OutHTML($oColumn->sColumnAlias).' '.$oColumn->GetColumnFormatCSSClass().'">';

    if ($oColumn->bAllowSort) {
        $sChangeOrderLink = '';
        $sOrderCount = '';
        if ($bShowSortCount && !empty($sActiveOrderBy)) {
            $iOrderCount = $oHTMLTable->GetOrderByPositionForColumn($oColumn);
            if ($iOrderCount) {
                $sOrderCount = ' ('.$iOrderCount.')';
            }
        }
        $sOrderCSS = 'orderbyNone';
        if ('ASC' == $sActiveOrderBy) {
            $sChangeOrderLink = $oHTMLTable->GetOrderByURL($oColumn, 'DESC');
            $sOrderCSS = 'orderbyASC';
        } elseif ('DESC' == $sActiveOrderBy) {
            $sChangeOrderLink = $oHTMLTable->GetOrderByURL($oColumn, null);
            $sOrderCSS = 'orderbyDESC';
        } else {
            $sChangeOrderLink = $oHTMLTable->GetOrderByURL($oColumn, 'ASC');
            $sOrderCSS = 'orderbyNone';
        }

        echo "<a href=\"{$sChangeOrderLink}\" class=\"{$sOrderCSS}\">".TGlobal::OutHTML($oColumn->sTitle)."{$sOrderCount}</a>";
    } else {
        echo TGlobal::OutHTML($oColumn->sTitle);
    }

    echo '</th>';
}
echo '</tr>';

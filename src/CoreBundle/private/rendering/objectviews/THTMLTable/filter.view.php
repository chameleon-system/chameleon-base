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
echo '<tr class="filter">';
if (count($aActions) > 0) {
    echo '<th class="actionColumn">&nbsp;</th>';
}
while ($oColumn = $oColumns->Next()) {
    /** @var $oColumn THTMLTableColumn */
    echo '<th class="'.TGlobal::OutHTML($oColumn->sColumnAlias).' '.$oColumn->GetColumnFormatCSSClass().'">';

    if ($oColumn->bAllowFilter) {
        echo $oColumn->Render('filter', 'Core', $aCallTimeVars);
    } else {
        echo '&nbsp;';
    }

    echo '</th>';
}
echo '</tr>';

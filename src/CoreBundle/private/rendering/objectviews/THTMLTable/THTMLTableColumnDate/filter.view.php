<?php

/** @var $oOwningTable THTMLTable */
/** @var $oColumn THTMLTableColumn */
/** @var $aCallTimeVars array */
/** @var $searchFilter string */
$sFilterPopupId = TGlobal::OutHTML("filter{$oOwningTable->sListIdentKey}{$oColumn->sColumnAlias}");

$sStartVal = '';
$sEndVal = '';
if (is_array($searchFilter)) {
    if (array_key_exists(THTMLTableColumnDate::FILTER_FROM, $searchFilter)) {
        $sStartVal = $searchFilter[THTMLTableColumnDate::FILTER_FROM];
    }
    if (array_key_exists(THTMLTableColumnDate::FILTER_TO, $searchFilter)) {
        $sEndVal = $searchFilter[THTMLTableColumnDate::FILTER_TO];
    }
}

?>
<div class="filterbox">
    <div style="display:none;" class="filterPopUp" id="<?=$sFilterPopupId; ?>">
        <form name="" accept-charset="utf-8" method="post" action="<?=$oOwningTable->GetFilterURL($oColumn); ?>">
            <input type="text"
                   name="<?=TGlobal::OutHTML($oOwningTable->sListIdentKey); ?>[<?=THTMLTable::URL_PARAM_SEARCH; ?>][<?=TGlobal::OutHTML($oColumn->sColumnAlias); ?>][<?=THTMLTableColumnDate::FILTER_FROM; ?>]"
                   value="<?=TGlobal::OutHTML($sStartVal); ?>"/>
            <input type="text"
                   name="<?=TGlobal::OutHTML($oOwningTable->sListIdentKey); ?>[<?=THTMLTable::URL_PARAM_SEARCH; ?>][<?=TGlobal::OutHTML($oColumn->sColumnAlias); ?>][<?=THTMLTableColumnDate::FILTER_TO; ?>]"
                   value="<?=TGlobal::OutHTML($sEndVal); ?>"/>
            <input type="submit" value="<?=TGlobal::OutHTML(TGlobal::Translate('Filter')); ?>"/>
        </form>
    </div>
    <span class="set"><a href="#" onclick="$('#<?=$sFilterPopupId; ?>').show()"><img
        src="/chameleon/blackbox/images/icons/magnifier.png" alt="<?=TGlobal::OutHTML(TGlobal::Translate('Filter setzen')); ?>" border="0"/></a></span>
    <?php
    if (!empty($sStartVal) || !empty($sEndVal)) {
        $sFilterString = '';
        if (!empty($sStartVal) && !empty($sEndVal)) {
            $sFilterString = $sStartVal.' - '.$sEndVal;
        } elseif (!empty($sStartVal)) {
            $sFilterString = '>='.$sStartVal;
        } elseif (!empty($sEndVal)) {
            $sFilterString = '<='.$sEndVal;
        } ?>
        <span class="term"><?=TGlobal::OutHTML($sFilterString); ?></span><span class="clear"><a
            href="<?=$oOwningTable->GetFilterURL($oColumn); ?>"><img src="/chameleon/blackbox/images/icons/bin_closed.png"
                                                                   alt="<?=TGlobal::OutHTML(TGlobal::Translate('Filter zurücksetzen')); ?>"
                                                                   border="0"/></a></span>
        <?php
    }
    ?>
</div>
<?php
/**
 * @deprecated since 6.3.0 - not used anymore.
 */
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
    <div style="display:none;" class="filterPopUp" id="<?php echo $sFilterPopupId; ?>">
        <form name="" accept-charset="utf-8" method="post" action="<?php echo $oOwningTable->GetFilterURL($oColumn); ?>">
            <input type="text"
                   name="<?php echo TGlobal::OutHTML($oOwningTable->sListIdentKey); ?>[<?php echo THTMLTable::URL_PARAM_SEARCH; ?>][<?php echo TGlobal::OutHTML($oColumn->sColumnAlias); ?>][<?php echo THTMLTableColumnDate::FILTER_FROM; ?>]"
                   value="<?php echo TGlobal::OutHTML($sStartVal); ?>"/>
            <input type="text"
                   name="<?php echo TGlobal::OutHTML($oOwningTable->sListIdentKey); ?>[<?php echo THTMLTable::URL_PARAM_SEARCH; ?>][<?php echo TGlobal::OutHTML($oColumn->sColumnAlias); ?>][<?php echo THTMLTableColumnDate::FILTER_TO; ?>]"
                   value="<?php echo TGlobal::OutHTML($sEndVal); ?>"/>
            <input type="submit" value="<?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('Filter')); ?>"/>
        </form>
    </div>
    <span class="set"><a href="#" onclick="$('#<?php echo $sFilterPopupId; ?>').show()"><img
        src="/chameleon/blackbox/images/icons/magnifier.png" alt="<?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('Filter setzen')); ?>" border="0"/></a></span>
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
        <span class="term"><?php echo TGlobal::OutHTML($sFilterString); ?></span><span class="clear"><a
            href="<?php echo $oOwningTable->GetFilterURL($oColumn); ?>"><img src="/chameleon/blackbox/images/icons/bin_closed.png"
                                                                   alt="<?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('Filter zurücksetzen')); ?>"
                                                                   border="0"/></a></span>
        <?php
    }
?>
</div>
<?php
/**
 * @deprecated since 6.3.0 - not used anymore.
 */
/** @var $oOwningTable THTMLTable */
/** @var $oColumn THTMLTableColumn */
/** @var $aCallTimeVars array */
/** @var $searchFilter string */
$sFilterPopupId = TGlobal::OutHTML("filter{$oOwningTable->sListIdentKey}{$oColumn->sColumnAlias}");
?>
<div class="filterbox">
    <div style="display:none;" class="filterPopUp" id="<?php echo $sFilterPopupId; ?>">
        <form name="" accept-charset="utf-8" method="post" action="<?php echo $oOwningTable->GetFilterURL($oColumn); ?>">
            <input type="text"
                   name="<?php echo TGlobal::OutHTML($oOwningTable->sListIdentKey); ?>[<?php echo THTMLTable::URL_PARAM_SEARCH; ?>][<?php echo TGlobal::OutHTML($oColumn->sColumnAlias); ?>]"
                   value="<?php echo TGlobal::OutHTML($searchFilter); ?>"/>
            <input type="submit" value="<?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('Filter')); ?>"/>
        </form>
    </div>
    <span class="set"><a href="#" onclick="$('#<?php echo $sFilterPopupId; ?>').show()"><img
        src="/chameleon/blackbox/images/icons/magnifier.png" alt="<?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('Filter setzen')); ?>" border="0"/></a></span>
    <?php
    if (!empty($searchFilter)) {
        ?>
        <span class="term"><?php echo TGlobal::OutHTML($searchFilter); ?></span><span class="clear"><a
            href="<?php echo $oOwningTable->GetFilterURL($oColumn); ?>"><img src="/chameleon/blackbox/images/icons/bin_closed.png"
                                                                   alt="<?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('Filter zurücksetzen')); ?>"
                                                                   border="0"/></a></span>
        <?php
    }
?>
</div>
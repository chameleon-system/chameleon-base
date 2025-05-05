<?php
/**
 * view shows the available gift cards for an item
 * the user can select one.
 */

/** @var $oPkgShopServiceType TdbPkgShopServiceType */

/** @var $oTargetBasketItem TShopBasketArticle */

/** @var $oActiveSelection TdbPkgShopServiceItem */
$sIntroText = $oPkgShopServiceType->GetTextField('display_intro_text');
$sActiveCardText = '';
if ($oActiveSelection) {
    $sActiveCardText = $oActiveSelection->GetUserParameter('cardtext');
}
?>
<div class="TdbPkgShopServiceType <?php echo TGlobal::OutHTML($oPkgShopServiceType->fieldSystemName); ?>">
    <?php echo $sIntroText; ?>
    <label><input <?php if ($oActiveSelection && $oActiveSelection->IsUsedAsDefault()) {
        echo 'checked="checked"';
    }?>
        type="radio" name="pkg_shop_service_type[<?php echo TGlobal::OutHTML($oPkgShopServiceType->id); ?>][default]"
        value="1"/> <?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_shop_service_item_giftcard.form.apply_to_all')); ?></label><br/>
    <label><input <?php if (!$oActiveSelection || false == $oActiveSelection->IsUsedAsDefault()) {
        echo 'checked="checked"';
    }?>
        type="radio" name="pkg_shop_service_type[<?php echo TGlobal::OutHTML($oPkgShopServiceType->id); ?>][default]"
        value="0"/> <?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_shop_service_item_giftcard.form.apply_to_product')); ?></label>
    <br/><br/>

    <textarea rows="10" cols="50"
              name="pkg_shop_service_type[<?php echo TGlobal::OutHTML($oPkgShopServiceType->id); ?>][data][cardtext]"><?php echo TGlobal::OutHTML($sActiveCardText); ?></textarea>
    <ul class="gift-wrapping">
        <li>
            <input <?php if (!$oActiveSelection) {
                echo 'checked="checked"';
            }?> type="radio"
                                                                             name="pkg_shop_service_item_id[<?php echo TGlobal::OutHTML($oPkgShopServiceType->id); ?>]"
                                                                             value="clear"/> <?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_shop_service_item_giftcard.form.apply_to_none')); ?>
        </li>
        <?php
                    $oCardList = $oPkgShopServiceType->GetFieldPkgShopServiceItemList();
while ($oCard = $oCardList->Next()) {
    $sSelected = '';
    if ($oActiveSelection && $oActiveSelection->id == $oCard->id) {
        $sSelected = 'checked="checked"';
    }
    $oProduct = $oCard->GetFieldShopArticle();
    echo '<li>';
    echo '<input '.$sSelected.' type="radio" name="pkg_shop_service_item_id['.TGlobal::OutHTML($oCard->fieldPkgShopServiceTypeId).']" value="'.TGlobal::OutHTML($oCard->id).'" /> ';
    if ($oProduct) {
        echo $oProduct->Render('vAsServiceItem', 'Customer');
    } else {
        echo 'ERROR: please define a product for the pkg_shop_service_item';
    }
    echo '</li>';
}
?>
    </ul>
</div>
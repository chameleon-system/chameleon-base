<?php
/**
 * view shows the available gift wrappings for an item
 * the user can select one.
 */

/** @var $oPkgShopServiceType TdbPkgShopServiceType */

/** @var $oTargetBasketItem TShopBasketArticle */

/** @var $oActiveSelection TdbPkgShopServiceItem */
$sIntroText = $oPkgShopServiceType->GetTextField('display_intro_text');
?>
<div class="TdbPkgShopServiceType <?php echo TGlobal::OutHTML($oPkgShopServiceType->fieldSystemName); ?>">
    <?php echo $sIntroText; ?>
    <label><input <?php if ($oActiveSelection && $oActiveSelection->IsUsedAsDefault()) {
        echo 'checked="checked"';
    }?>
        type="radio" name="pkg_shop_service_type[<?php echo TGlobal::OutHTML($oPkgShopServiceType->id); ?>][default]"
        value="1"/> <?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_shop_service_item_wrapping.form.apply_to_all')); ?></label><br/>
    <label><input <?php if (!$oActiveSelection || false == $oActiveSelection->IsUsedAsDefault()) {
        echo 'checked="checked"';
    }?>
        type="radio" name="pkg_shop_service_type[<?php echo TGlobal::OutHTML($oPkgShopServiceType->id); ?>][default]"
        value="0"/> <?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_shop_service_item_wrapping.form.apply_to_product')); ?></label>
    <br/><br/>
    <ul class="card">
        <li>
            <input <?php if (!$oActiveSelection) {
                echo 'checked="checked"';
            }?> type="radio"
                                                                             name="pkg_shop_service_item_id[<?php echo TGlobal::OutHTML($oPkgShopServiceType->id); ?>]"
                                                                             value="clear"/> <?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_shop_service_item_wrapping.form.apply_to_none')); ?>
        </li>
        <?php
                    // Verpackung
                    $oWrappingList = $oPkgShopServiceType->GetFieldPkgShopServiceItemList();
while ($oWrapping = $oWrappingList->Next()) {
    $sSelected = '';
    if ($oActiveSelection && $oActiveSelection->id == $oWrapping->id) {
        $sSelected = 'checked="checked"';
    }
    $oProduct = $oWrapping->GetFieldShopArticle();
    echo '<li>';
    echo '<input '.$sSelected.' type="radio" name="pkg_shop_service_item_id['.TGlobal::OutHTML($oPkgShopServiceType->id).']" value="'.TGlobal::OutHTML($oWrapping->id).'" /> ';
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
<?php
/** @var $oField TCMSFieldShopVariantDetails */
/* @var $oParent TdbShopArticle */
/* @var $oVariantSet TdbShopVariantSet */
/* @var $aActivatedIds array */
?>
<div class="TCMSFieldShopVariantDetails">
    <div class="vEditField">
        <input type="hidden" name="<?php echo TGlobal::OutHTML($oField->name); ?>[x]" value="x"/>
        <table class="table table-striped table-bordered table-sm">
            <tr>
                <th><?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_shop.variants.variant_type')); ?></th>
                <th><?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_shop.variants.variant_value')); ?></th>
                <th><?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_shop.variants.action_new_variant_value')); ?></th>
            </tr>
            <?php
            $oVariantTypes = $oVariantSet->GetFieldShopVariantTypeList();
while ($oType = $oVariantTypes->Next()) {
    /** @var $oType TdbShopVariantType */
    $oValueList = $oType->GetFieldShopVariantTypeValueList(); ?>
                <tr>
                    <th><?php echo TGlobal::OutHTML($oType->fieldName); ?> <a
                        href="<?php echo $oField->GetEditLink($oType->table, $oType->id); ?>" class="btn btn-sm btn-info" style="float: right"><?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.link.edit')); ?></a>
                    </th>
                    <td>
                        <?php
            if ('RadioButton' == $oType->fieldValueSelectType) {
                while ($oValue = $oValueList->Next()) {
                    $sSelected = '';
                    if (in_array($oValue->id, $aActivatedIds)) {
                        $sSelected = 'checked="checked"';
                    }

                    $radioInputId = $oField->name.'_'.$oType->id;

                    echo '<div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="'.TGlobal::OutHTML($radioInputId).'" name="'.TGlobal::OutHTML($oField->name).'['.TGlobal::OutHTML($oType->id).']" value="'.TGlobal::OutHTML($oValue->id).'" '.$sSelected.'">
                                        <label class="form-check-label" for="'.TGlobal::OutHTML($radioInputId).'">'.TGlobal::OutHTML($oValue->GetName()).'</label>
                                      </div>';
                }
            } else {
                echo '<select name="'.TGlobal::OutHTML($oField->name).'['.TGlobal::OutHTML($oType->id).']" class="form-control form-control-sm">';
                while ($oValue = $oValueList->Next()) {
                    $sSelected = '';
                    if (in_array($oValue->id, $aActivatedIds)) {
                        $sSelected = 'selected="selected"';
                    }
                    echo '<option value="'.TGlobal::OutHTML($oValue->id).'" '.$sSelected.' />'.TGlobal::OutHTML($oValue->GetName()).'</option>';
                }
                echo '</select>';
            } ?>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm"
                               name="<?php echo TGlobal::OutHTML($oField->name); ?>_new[<?php echo TGlobal::OutHTML($oType->id); ?>]"
                               value=""/>
                    </td>
                </tr>
                <?php
}
?>

        </table>
    </div>
</div>
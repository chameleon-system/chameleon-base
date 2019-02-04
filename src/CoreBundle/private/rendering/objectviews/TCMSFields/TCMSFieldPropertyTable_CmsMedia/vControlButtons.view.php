<?php
/** @var $oField TCMSFieldPropertyTable_CmsMedia */
$oCategory = TdbCmsMediaTree::GetNewInstance();
if (false != $oCategory->Load($oField->ConfigGetDefaultCategoryId()) || $oField->ConfigShowCategorySelector()) {
    ?>
    <h4><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.field_property_media.upload_headline')); ?></h4>

    <div class="form-group">
        <label for="<?=TGlobal::OutHTML($oField->name); ?>__cms_media_tree_id"><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.field_property_media.target_folder')); ?></label>
        <?php
        if ($oField->ConfigShowCategorySelector()) {
            // show category selector
            /** @var $oTreeSelect TCMRenderMediaTreeSelectBox */
            $oTreeSelect = new TCMRenderMediaTreeSelectBox();
            $sSelectedId = null;
            if ($oCategory) {
                $sSelectedId = $oCategory->id;
            }

            echo '<select class="form-control" name="'.TGlobal::OutHTML($oField->name).'__cms_media_tree_id" id="'.TGlobal::OutHTML($oField->name).'__cms_media_tree_id" class="form-control form-control-sm">';
            echo $oTreeSelect->GetTreeOptions($sSelectedId, true);
            echo '</select>';
        } elseif ($oCategory) {
            echo '<input type="hidden" name="'.TGlobal::OutHTML($oField->name).'__cms_media_tree_id" id="'.TGlobal::OutHTML($oField->name).'__cms_media_tree_id" value="'.TGlobal::OutHTML($oCategory->id).'" />'.TGlobal::OutHTML($oCategory->GetName());
        } ?>
    </div>
    <div class="form-group">
<?php
    echo TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.field_property_media.action_upload_and_assign'), 'javascript:'.$oField->_GetOpenUploadWindowJS(), 'fas fa-file-upload'); ?>
    </div>
<?php
}

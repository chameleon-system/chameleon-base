<?php
$oCMSUser = $data['oCMSUser']; /** @var $oCMSUser TCMSUser */
?>
<form id="cmsformdel" name="cmsformdel" method="post" action="<?=PATH_CMS_CONTROLLER; ?>" accept-charset="UTF-8">
    <input type="hidden" name="tableid" value="<?=TGlobal::OutHTML($data['id']); ?>"/>
    <input type="hidden" name="pagedef" value="tableeditor"/>
    <input type="hidden" name="id" value=""/>
    <input type="hidden" name="module_fnc[contentmodule]" value=""/>
    <?php if (array_key_exists('sRestriction', $data)) {
    ?><input type="hidden" name="sRestriction"
                                                                  value="<?=TGlobal::OutHTML($data['sRestriction']); ?>"/><?php
} ?>
    <?php if (array_key_exists('sRestrictionField', $data)) {
        ?><input type="hidden" name="sRestrictionField"
                                                                       value="<?=TGlobal::OutHTML($data['sRestrictionField']); ?>"/><?php
    } ?>
    <?php if (array_key_exists('_isiniframe', $data)) {
        ?><input type="hidden" name="_isiniframe"
                                                                 value="<?=TGlobal::OutHTML($data['_isiniframe']); ?>"/><?php
    } ?>
</form>
<form id="cmsform" name="cmsform" method="post" target="_top" action="<?=PATH_CMS_CONTROLLER; ?>" accept-charset="UTF-8">
    <input type="hidden" name="tableid" value="<?=TGlobal::OutHTML($data['id']); ?>"/>
    <input type="hidden" name="pagedef" value="tableeditor"/>
    <input type="hidden" name="id" value=""/>
    <input type="hidden" name="module_fnc[contentmodule]" value=""/>
    <?php if (array_key_exists('sRestriction', $data)) {
        ?><input type="hidden" name="sRestriction"
                                                                  value="<?=TGlobal::OutHTML($data['sRestriction']); ?>"/><?php
    } ?>
    <?php if (array_key_exists('sRestrictionField', $data)) {
        ?><input type="hidden" name="sRestrictionField"
                                                                       value="<?=TGlobal::OutHTML($data['sRestrictionField']); ?>"/><?php
    } ?>
    <?php if (array_key_exists('_isiniframe', $data)) {
        ?><input type="hidden" name="_isiniframe"
                                                                 value="<?=TGlobal::OutHTML($data['_isiniframe']); ?>"/><?php
    } ?>
</form>
<div style="position: relative; top: 2px;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td>
                <div style="padding-left: 2px;">
                    <div class="btn-group">
                        <?php if ($oCMSUser->oAccessManager->HasNewPermission($data['sTableName'])) {
        ?>
                        <button type="button" class="btn btn-sm btn-primary" onclick="document.cmsform.elements['module_fnc[contentmodule]'].value='Insert';document.cmsform.submit();"><span class="btn-icon" style="background-image: url(<?=TGlobal::GetStaticURLToWebLib('/images/icons/page_new.gif'); ?>);"><?php echo TGlobal::Translate('chameleon_system_core.action.new'); ?></span></button>
                        <?php
    } ?>
                        <button type="button" class="btn btn-sm btn-primary" onclick="parent.loadMLTList('<?=$data['name']; ?>_iframe','<?=$data['name']; ?>','<?=$data['id']; ?>','<?=$data['sRestriction']; ?>','<?=$data['sRestrictionField']; ?>');"><span class="btn-icon" style="background-image: url(<?=TGlobal::GetStaticURLToWebLib('/images/icons/page_new.gif'); ?>);"><?php echo TGlobal::Translate('chameleon_system_core.field_mlt.action_select'); ?></span></button>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>
<style>
    .tblsearch {
        background-image: url(<?php echo TGlobal::GetPathTheme(); ?>/images/table_bg.gif);
        border-top: 1px solid #9A9A9A;
        width: 100%;
        padding: 5px;
        color: #841313;
        font-weight: bold;
        font-size: 13px;
        line-height: 20px;
    }
</style>
<script>
    function deleteConnection(id) {
        parent.removeMLTConnection('<?php echo str_replace('_mlt', '', $data['sRestrictionField']); ?>', '<?=$data['name']; ?>', '<?php echo $data['sRestriction']; ?>', id)
    }
</script>
<?php echo $data['sTable']; ?>

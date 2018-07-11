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
</form>
<form id="cmsform" name="cmsform" method="post" action="<?=PATH_CMS_CONTROLLER; ?>" target="_top" accept-charset="UTF-8">
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
</form>
<form id="cmsformworkonlist" name="cmsformworkonlist" method="get" action="<?=PATH_CMS_CONTROLLER; ?>"
      accept-charset="UTF-8">
    <input type="hidden" name="pagedef" value="<?=$data['pagedef']; ?>"/>
    <input type="hidden" name="id" value="<?=TGlobal::OutHTML($data['id']); ?>"/>
    <input type="hidden" name="items" value=""/>
    <input type="hidden" name="module_fnc[contentmodule]" value=""/>
    <?php if (array_key_exists('sRestriction', $data)) {
        ?><input type="hidden" name="sRestriction"
                                                                  value="<?=TGlobal::OutHTML($data['sRestriction']); ?>"/><?php
    } ?>
    <?php if (array_key_exists('sRestrictionField', $data)) {
        ?><input type="hidden" name="sRestrictionField"
                                                                       value="<?=TGlobal::OutHTML($data['sRestrictionField']); ?>"/><?php
    } ?>
</form>
<?php if ($oCMSUser->oAccessManager->HasNewPermission($data['sTableName'])) {
        ?>
<div style="position: relative; top: 2px;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td>
                <div style="padding-left: 2px;">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-primary" onclick="document.cmsform.elements['module_fnc[contentmodule]'].value='Insert';document.cmsform.submit();"><span class="btn-icon" style="background-image: url(<?=TGlobal::GetStaticURLToWebLib(); ?>/images/icons/page-new.gif);"><?php echo TGlobal::Translate('chameleon_system_core.action.new'); ?></span></button>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>
<?php
    } ?>
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

<?php echo $data['sTable']; ?>

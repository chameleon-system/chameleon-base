<form id="cmsform" name="cmsform" method="post" action="<?=PATH_CMS_CONTROLLER; ?>" accept-charset="UTF-8">
    <input type="hidden" name="tableid" value="<?=TGlobal::OutHTML($data['id']); ?>"/>
    <input type="hidden" name="pagedef" value="tableeditorcomponent"/>
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
<?php if ($data['premission_new']) {
        ?>
<div style="position: relative; top: 2px;">
    <div style="padding-left: 20px;">
        <div data-table-function-bar class="btn-group">
            <button type="button" class="btn btn-sm btn-primary" onclick="document.cmsform.elements['module_fnc[contentmodule]'].value='Insert';document.cmsform.submit();"><span class="btn-icon" style="background-image: url(<?=TGlobal::GetStaticURLToWebLib(); ?>/images/icons/page-new.gif);"><?php echo TGlobal::Translate('chameleon_system_core.action.new'); ?></span></button>
        </div>
    </div>
</div>
<?php
    } ?>
            <div style="padding-left: 10px; padding-right: 10px;">
                <div class="cmsBoxBorder">
                <?php echo $data['sTable']; ?>
                </div>
            </div>

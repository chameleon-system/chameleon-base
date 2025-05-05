<?php

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;

$oCMSUser = $data['oCMSUser']; /* @var $oCMSUser TCMSUser */
?>
<form id="cmsformdel" name="cmsformdel" method="post" action="<?php echo PATH_CMS_CONTROLLER; ?>" accept-charset="UTF-8">
    <input type="hidden" name="tableid" value="<?php echo TGlobal::OutHTML($data['id']); ?>"/>
    <input type="hidden" name="pagedef" value="tableeditor"/>
    <input type="hidden" name="id" value=""/>
    <input type="hidden" name="module_fnc[contentmodule]" value=""/>
    <?php if (array_key_exists('sRestriction', $data)) {
        ?><input type="hidden" name="sRestriction"
                                                                  value="<?php echo TGlobal::OutHTML($data['sRestriction']); ?>"/><?php
    } ?>
    <?php if (array_key_exists('sRestrictionField', $data)) {
        ?><input type="hidden" name="sRestrictionField"
                                                                       value="<?php echo TGlobal::OutHTML($data['sRestrictionField']); ?>"/><?php
    } ?>
</form>
<form id="cmsform" name="cmsform" method="post" action="<?php echo PATH_CMS_CONTROLLER; ?>" target="_top" accept-charset="UTF-8">
    <input type="hidden" name="tableid" value="<?php echo TGlobal::OutHTML($data['id']); ?>"/>
    <input type="hidden" name="pagedef" value="tableeditor"/>
    <input type="hidden" name="id" value=""/>
    <input type="hidden" name="module_fnc[contentmodule]" value=""/>
    <?php if (array_key_exists('sRestriction', $data)) {
        ?><input type="hidden" name="sRestriction"
                                                                  value="<?php echo TGlobal::OutHTML($data['sRestriction']); ?>"/><?php
    } ?>
    <?php if (array_key_exists('sRestrictionField', $data)) {
        ?><input type="hidden" name="sRestrictionField"
                                                                       value="<?php echo TGlobal::OutHTML($data['sRestrictionField']); ?>"/><?php
    } ?>
</form>
<form id="cmsformworkonlist" name="cmsformworkonlist" method="get" action="<?php echo PATH_CMS_CONTROLLER; ?>"
      accept-charset="UTF-8">
    <input type="hidden" name="pagedef" value="<?php echo $data['pagedef']; ?>"/>
    <input type="hidden" name="id" value="<?php echo TGlobal::OutHTML($data['id']); ?>"/>
    <input type="hidden" name="items" value=""/>
    <input type="hidden" name="module_fnc[contentmodule]" value=""/>
    <?php if (array_key_exists('sRestriction', $data)) {
        ?><input type="hidden" name="sRestriction"
                                                                  value="<?php echo TGlobal::OutHTML($data['sRestriction']); ?>"/><?php
    } ?>
    <?php if (array_key_exists('sRestrictionField', $data)) {
        ?><input type="hidden" name="sRestrictionField"
                                                                       value="<?php echo TGlobal::OutHTML($data['sRestrictionField']); ?>"/><?php
    } ?>
</form>
<?php
/** @var SecurityHelperAccess $securityHelper */
$securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

if ($securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_NEW, $data['sTableName'])) {
    ?>
<div style="position: relative; top: 2px;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td>
                <div style="padding-left: 2px;">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-primary" onclick="document.cmsform.elements['module_fnc[contentmodule]'].value='Insert';document.cmsform.submit();"><i class="fas fa-plus pr-2"></i><?php echo ServiceLocator::get('translator')->trans('chameleon_system_core.action.new'); ?></button>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>
<?php
}
echo $data['sTable']; ?>

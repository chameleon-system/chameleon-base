<?php
?>
<br/>
<h1><?php echo ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_newsletter_robinson_import.headline'); ?></h1>
<div class="messageContainer">
    <?php
    if (isset($data['messages'])) {
        echo $data['messages'];
    }?>
</div>
<form name="import" method="post" action="<?php echo PATH_CMS_CONTROLLER; ?>" enctype="multipart/form-data">
    <input type="hidden" name="pagedef" value="NewsletterRobinsonImport">
    <input type="hidden" name="module_fnc[contentmodule]" value="ParseFile"/>
    <input type="hidden" name="_pagedefType" value="Core">
    &nbsp;<BR>
    <?php echo ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_newsletter_robinson_import.import_file_help'); ?>
    <br/>&nbsp;<br/>
    <table cellpadding="3" cellspacing="2" border="0">
        <tr>
            <td><?php echo ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.text.portal'); ?>:&nbsp;&nbsp;&nbsp;</td>
            <td colspan="2"><select name="cms_portal_id">
                <?php
                $oPortals = $data['oPortals']; /** @var $oPortals TCMSRecord */
while ($oPortal = $oPortals->Next()) {
    echo '<option value="'.TGlobal::OutHTML($oPortal->id).'">'.TGlobal::OutHTML($oPortal->GetName())."</option>\n";
}
?>
            </select></td>
        </tr>
        <tr>
            <td><?php echo ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_newsletter_robinson_import.source'); ?>:&nbsp;&nbsp;&nbsp;</td>
            <td colspan="2"><input type="file" name="csvfile"/></td>
        </tr>
        <tr>
            <td valign="top">&nbsp;</td>
            <td colspan="2">
                <?php
echo TCMSRender::DrawButton(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_newsletter_robinson_import.action_import'), 'javascript:document.import.submit();', 'far fa-play-circle', 'import');
?>
            </td>
        </tr>
    </table>
</form>
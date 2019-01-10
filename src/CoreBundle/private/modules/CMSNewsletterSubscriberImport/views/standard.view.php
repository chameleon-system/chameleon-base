<?php
?>
<br/>
<h1><?=TGlobal::Translate('chameleon_system_core.cms_module_newsletter_import.headline'); ?></h1>
<div class="messageContainer">
    <?php
    if (isset($data['messages'])) {
        echo $data['messages'];
    }?>
</div>
<?php
$oPortalList = TdbCmsPortalList::GetList();
$oPkgNewsletterGroupList = $data['oPkgNewsletterGroupList'];
/** @var $oPkgNewsletterGroupList TdbPkgNewsletterGroupList */
?>
<form name="importCSV" method="post" action="<?=PATH_CMS_CONTROLLER; ?>" enctype="multipart/form-data">
    <input type="hidden" name="pagedef" value="NewsletterSubscriberImport">
    <input type="hidden" name="module_fnc[contentmodule]" value="ParseFile"/>
    <input type="hidden" name="_pagedefType" value="Core">
    &nbsp;<BR>
    <?=TGlobal::Translate('chameleon_system_core.cms_module_newsletter_import.import_file_help'); ?>
    <br/>
    <textarea rows="2" cols="50">
        email;name;firstname;salutation/gender;
        john.doe@gmail.com;Doe;John;Mr;
    </textarea>
    <br/>
    <?=TGlobal::Translate('chameleon_system_core.cms_module_newsletter_import.import_file_help_salutation', array('%patterns%' => 'm/w; m/f; male/female; Mr/Mrs; Herr/Frau')); ?>
    <br/>&nbsp;<br/>
    <table cellpadding="3" cellspacing="2" border="0">
        <tr>
            <td><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_newsletter_import.group')); ?>:&nbsp;&nbsp;&nbsp;</td>
            <td colspan="2"><select name="pkg_newsletter_group_id">
                <option value="noGroup"><?= TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_newsletter_import.no_group')); ?></option>
                <?php
                if ($oPkgNewsletterGroupList->Length() > 0) {
                    $oPkgNewsletterGroupList = $data['oPkgNewsletterGroupList']; /** @var $oPkgNewsletterGroupList TdbPkgNewsletterGroupList */
                    while ($oPkgNewsletterGroup = $oPkgNewsletterGroupList->Next()) {
                        echo '<option value="'.TGlobal::OutHTML($oPkgNewsletterGroup->id).'">'.TGlobal::OutHTML($oPkgNewsletterGroup->GetName())."</option>\n";
                    }
                }
                ?>
            </select></td>
        </tr>
        <tr>
            <td><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_newsletter_import.portal_help')); ?>:&nbsp;&nbsp;&nbsp;</td>
            <td colspan="2"><select name="cms_portal_id">
                <?php
                if ($oPortalList->Length() > 0) {
                    while ($oPortal = $oPortalList->Next()) {
                        echo '<option value="'.TGlobal::OutHTML($oPortal->id).'">'.TGlobal::OutHTML($oPortal->GetName())."</option>\n";
                    }
                }
                ?>
            </select></td>
        </tr>
        <tr>
            <td><?=TGlobal::Translate('chameleon_system_core.cms_module_newsletter_import.replace_subscribers'); ?>:&nbsp;&nbsp;&nbsp;</td>
            <td colspan="2"><input type="checkbox" name="replaceAllSubscriber" value="true"/></td>
        </tr>
        <tr>
            <td><?=TGlobal::Translate('chameleon_system_core.cms_module_newsletter_import.update_group_only'); ?>
                :&nbsp;&nbsp;&nbsp;</td>
            <td colspan="2"><input type="checkbox" name="notupdateSubscriber" value="true"/></td>
        </tr>
        <tr>
            <td><?=TGlobal::Translate('chameleon_system_core.cms_module_newsletter_import.source'); ?>:&nbsp;&nbsp;&nbsp;</td>
            <td colspan="2"><input type="file" name="csvfile"/></td>
        </tr>
        <tr>
            <td valign="top">&nbsp;</td>
            <td colspan="2">
                <?php
                echo TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.cms_module_newsletter_import.action_import'), 'javascript:CheckReplacement();', URL_CMS.'/images/icons/action_go.gif', 'importCSV');
                ?>
            </td>
        </tr>
    </table>
</form>
<script type="text/javascript">
    function CheckReplacement() {
        if (document.importCSV.replaceAllSubscriber.checked == true) {
            CreateModalDialogFromContainer('confirmReplaceDialog', 250, 150);
        } else {
            StartCSVImport();
        }
    }

    function StartCSVImport() {
        CHAMELEON.CORE.showProcessingDialog();
        document.importCSV.submit();
    }
</script>

<div id="confirmReplaceDialog" style="display:none;">
    <?=TGlobal::Translate('chameleon_system_core.cms_module_newsletter_import.confirm_replace'); ?>
    <br \>
    <br \>
    <?php
    echo TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.cms_module_newsletter_import.action_confirm_replace'), 'javascript:StartCSVImport();', URL_CMS.'/images/icons/action_go.gif', 'importCSVFinal');
    ?>
</div>


<?php
/** @var $oUser TdbDataExtranetUser */
/** @var $oExtranetConfig TdbDataExtranet */
/** @var $aCallTimeVars array */
$oMessageManager = TCMSMessageManager::GetInstance();

require dirname(__FILE__).'/form-basket-guest.view.php';
?>
<table>
    <tr>
        <th><?=TGlobal::OutHtml(TGlobal::Translate('chameleon_system_extranet.form.password')); ?><span class="required">*</span></th>
        <td>
            <?=TTemplateTools::InputField('aUser[password]', '', 300, '', 'password'); ?>
            <?php
            if ($oMessageManager->ConsumerHasMessages(TdbDataExtranetUser::MSG_FORM_FIELD.'-password')) {
                echo $oMessageManager->RenderMessages(TdbDataExtranetUser::MSG_FORM_FIELD.'-password');
            }
            ?>
        </td>
    </tr>
    <tr>
        <th><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_extranet.form.validate_password')); ?></th>
        <td>
            <?=TTemplateTools::InputField('aUser[password2]', '', 300, '', 'password'); ?>
            <?php
            if ($oMessageManager->ConsumerHasMessages(TdbDataExtranetUser::MSG_FORM_FIELD.'-password2')) {
                echo $oMessageManager->RenderMessages(TdbDataExtranetUser::MSG_FORM_FIELD.'-password2');
            }
            ?>
        </td>
    </tr>
</table>

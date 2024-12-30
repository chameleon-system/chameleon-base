<?php
/** @var $oUser TdbDataExtranetUser */
/** @var $oExtranetConfig TdbDataExtranet */
/** @var $aCallTimeVars array */
$oMessageManager = TCMSMessageManager::GetInstance();
?>
<table>
    <tr>
        <th><?=TGlobal::OutHtml(\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_extranet.form.mail')); ?><span class="required">*</span></th>
        <td>
            <?=TTemplateTools::InputField('aUser[name]', $oUser->fieldName, 300); ?>
            <?php
            if ($oMessageManager->ConsumerHasMessages(TdbDataExtranetUser::MSG_FORM_FIELD.'-name')) {
                echo $oMessageManager->RenderMessages(TdbDataExtranetUser::MSG_FORM_FIELD.'-name');
            }
            ?>
        </td>
    </tr>
    <tr>
        <th><?=TGlobal::OutHTML(\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_extranet.form.date_of_birth')); ?></th>
        <td>
            <?=TTemplateTools::InputField('aUser[birthdate]', $oUser->fieldBirthdate, 300); ?>
            <?php
            if ($oMessageManager->ConsumerHasMessages(TdbDataExtranetUser::MSG_FORM_FIELD.'-birthdate')) {
                echo $oMessageManager->RenderMessages(TdbDataExtranetUser::MSG_FORM_FIELD.'-birthdate');
            }
            ?>
        </td>
    </tr>
</table>

<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgExtranetMapper_ChangePassword extends AbstractViewMapper
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
        $oRequirements->NeedsSourceObject('sSpotName');
        $oRequirements->NeedsSourceObject('sTargetURL');
        $oRequirements->NeedsSourceObject('sToken');
        $oRequirements->NeedsSourceObject('sTitle', null, '');
        $oRequirements->NeedsSourceObject('sText', null, '');
        /*
         * @psalm-suppress InvalidArgument
         * @FIXME Passing `false` as a type is not correct. This should probably be the following:
         * $oRequirements->NeedsSourceObject('bPasswordChanged', 'boolean',false);
         */
        $oRequirements->NeedsSourceObject('bPasswordChanged', false);
        /*
         * @psalm-suppress InvalidArgument
         * @FIXME Passing `false` as a type is not correct. This should probably be the following:
         * $oRequirements->NeedsSourceObject('bPasswordChangeKeyValid', 'boolean', false);
         */
        $oRequirements->NeedsSourceObject('bPasswordChangeKeyValid', false);
        $oRequirements->NeedsSourceObject('oPasswordChangeUser', null);
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager): void
    {
        $aFieldUserName = [];
        $aFieldPassword = [];
        $aFieldPasswordCheck = [];

        $oGlobal = TGlobal::instance();
        $aUser = $oGlobal->GetUserData('aUser');
        if (is_array($aUser)) {
            if (isset($aUser['name'])) {
                $aFieldUserName['sValue'] = $aUser['name'];
            }
        }

        $sMessageConsumer = TdbDataExtranetUser::MSG_FORM_FIELD;
        $aFieldUserName['sError'] = $this->GetMessageForField('name', $sMessageConsumer);
        $aFieldPassword['sError'] = $this->GetMessageForField('password', $sMessageConsumer);
        $aFieldPasswordCheck['sError'] = $this->GetMessageForField('password2', $sMessageConsumer);

        $oMessageManager = TCMSMessageManager::GetInstance();
        if ($oMessageManager->ConsumerHasMessages($sMessageConsumer)) {
            $sGlobalMessages = $oMessageManager->RenderMessages($sMessageConsumer);
            $oVisitor->SetMappedValue('sGlobalMessages', $sGlobalMessages);
        }

        $aTextData = [];
        $aTextData['sTitle'] = $oVisitor->GetSourceObject('sTitle');
        if (!$oVisitor->GetSourceObject('bPasswordChanged')) {
            $aTextData['sText'] = $oVisitor->GetSourceObject('sText');
        }

        $oVisitor->SetMappedValue('sChangePasswordURLKeyParamName', TdbDataExtranet::URL_PARAMETER_CHANGE_PASSWORD);
        $oVisitor->SetMappedValue('sToken', $oVisitor->GetSourceObject('sToken'));
        $oVisitor->SetMappedValue('sTargetURL', $oVisitor->GetSourceObject('sTargetURL'));
        $oVisitor->SetMappedValue('aFieldUserName', $aFieldUserName);
        $oVisitor->SetMappedValue('aFieldNewPassword', $aFieldPassword);
        $oVisitor->SetMappedValue('aFieldNewPasswordCheck', $aFieldPasswordCheck);
        $oVisitor->SetMappedValue('sSpotName', $oVisitor->GetSourceObject('sSpotName'));
        $oVisitor->SetMappedValue('sMessageConsumer', $sMessageConsumer);
        $oVisitor->SetMappedValue('aTextData', $aTextData);
        $oVisitor->SetMappedValue('bPasswordChanged', $oVisitor->GetSourceObject('bPasswordChanged'));
        $oVisitor->SetMappedValue('bPasswordChangeKeyValid', $oVisitor->GetSourceObject('bPasswordChangeKeyValid'));
        $oVisitor->SetMappedValue('oPasswordChangeUser', $oVisitor->GetSourceObject('oPasswordChangeUser'));
    }

    /**
     * Set error message for given field from message manager.
     *
     * @param string $sFieldName
     * @param string $sCustomMSGConsumer
     *
     * @return string
     */
    protected function GetMessageForField($sFieldName, $sCustomMSGConsumer)
    {
        $sMessage = '';
        $oMsgManager = TCMSMessageManager::GetInstance();
        if ($oMsgManager->ConsumerHasMessages($sCustomMSGConsumer.'-'.$sFieldName)) {
            $sMessage = $oMsgManager->RenderMessages($sCustomMSGConsumer.'-'.$sFieldName);
        }

        return $sMessage;
    }
}

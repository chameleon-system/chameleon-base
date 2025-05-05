<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\ExtranetBundle\Interfaces\ExtranetUserProviderInterface;
use ChameleonSystem\ExtranetBundle\Util\ExtranetAuthenticationUtil;

class TPkgExtranetMapper_Login extends AbstractViewMapper
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
        $oRequirements->NeedsSourceObject('sSpotName');
        $oRequirements->NeedsSourceObject('sLoginSuccessURL', null, '');
        $oRequirements->NeedsSourceObject('sLoginFailureURL', null, '');
        $oRequirements->NeedsSourceObject('sTitle', null, '');
        $oRequirements->NeedsSourceObject('sText', null, '');
        $oRequirements->NeedsSourceObject('sRegisterLinkTitle', null, '');
        $oRequirements->NeedsSourceObject('sForgetPasswordLinkTitle', null, '');
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager): void
    {
        // no field messages on login. Need only one overall message
        $aFieldUserName = [];
        $aFieldUserName['sError'] = '';
        $aFieldUserName['sValue'] = $this->getExtranetAuthenticationUtil()->getLastLoginName();

        $aFieldPassword = [];
        $aFieldPassword['sError'] = '';
        $aFieldPassword['sValue'] = '';

        $sMessageConsumer = 'loginBox';

        $oMsgManager = TCMSMessageManager::GetInstance();
        if ($oMsgManager->ConsumerHasMessages($sMessageConsumer)) {
            $sOverallMessage = $oMsgManager->RenderMessages($sMessageConsumer);
            $oVisitor->SetMappedValue('sOverallMessage', $sOverallMessage);
        }

        $sRegisterLinkTitle = $oVisitor->GetSourceObject('sRegisterLinkTitle');
        $oExtranetConfiguration = TdbDataExtranet::GetInstance();
        if (!empty($sRegisterLinkTitle)) {
            if ($oExtranetConfiguration && $bCachingEnabled) {
                $oCacheTriggerManager->addTrigger($oExtranetConfiguration->table, $oExtranetConfiguration->id);
            }
            $aTextData['sTitle'] = $sRegisterLinkTitle;
            $aTextData['sLinkURL'] = $oExtranetConfiguration->GetFieldNodeRegisterIdPageURL();
            $oVisitor->SetMappedValue('aLinkRegister', $aTextData);
        }

        $sForgetPasswordLinkTitle = $oVisitor->GetSourceObject('sForgetPasswordLinkTitle');
        $aTextData['sTitle'] = $sForgetPasswordLinkTitle;
        $aTextData['sLinkURL'] = $oExtranetConfiguration->GetFieldForgotPasswordTreenodeIdPageURL();
        $oVisitor->SetMappedValue('aLinkForgotPassword', $aTextData);

        $aTextData = [];
        $aTextData['sTitle'] = $oVisitor->GetSourceObject('sTitle');
        $aTextData['sText'] = $oVisitor->GetSourceObject('sText');

        $oVisitor->SetMappedValue('aFieldUserName', $aFieldUserName);
        $oVisitor->SetMappedValue('aFieldPassword', $aFieldPassword);
        $oVisitor->SetMappedValue('sLoginSuccessURL', $oVisitor->GetSourceObject('sLoginSuccessURL'));
        $oVisitor->SetMappedValue('sLoginFailureURL', $oVisitor->GetSourceObject('sLoginFailureURL'));
        $oVisitor->SetMappedValue('sSpotName', $oVisitor->GetSourceObject('sSpotName'));
        $oVisitor->SetMappedValue('sMessageConsumer', $sMessageConsumer);
        $oVisitor->SetMappedValue('aTextData', $aTextData);

        $user = $this->getExtranetUserProvider()->getActiveUser();
        $bIsLoggedIn = $user->IsLoggedIn();
        $oVisitor->SetMappedValue('bIsLoggedIn', $bIsLoggedIn);

        if (true === $bIsLoggedIn) {
            $oVisitor->SetMappedValue('aTextData', ['sTitle' => $oVisitor->GetSourceObject('sTitle')]);

            $oVisitor->SetMappedValue('sUsername', $user->GetName());
            $sLogoutLink = $oExtranetConfiguration->GetLinkLogout($oVisitor->GetSourceObject('sSpotName'));
            $oVisitor->SetMappedValue('sLogoutLink', $sLogoutLink);
        }
    }

    /**
     * @return ExtranetAuthenticationUtil
     */
    private function getExtranetAuthenticationUtil()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_extranet.util.extranet_authentication');
    }

    /**
     * @return ExtranetUserProviderInterface
     */
    private function getExtranetUserProvider()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_extranet.extranet_user_provider');
    }
}

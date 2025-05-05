<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TPkgExtranetMapper_AccessDenied extends AbstractViewMapper
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
        $oRequirements->NeedsSourceObject('sRegisterLinkTitle', null, '', true);
        $oRequirements->NeedsSourceObject('sLoginLinkTitle', null, '', true);
        $oRequirements->NeedsSourceObject('redirectURL', null, '', true);
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager): void
    {
        $redirectURL = $oVisitor->GetSourceObject('redirectURL');
        if (null === $redirectURL || empty($redirectURL)) {
            $redirectURL = $this->getInputFilterUtil()->getFilteredInput('sSuccessURL');
        }

        // registration link
        $sRegisterLinkTitle = $oVisitor->GetSourceObject('sRegisterLinkTitle');
        if (null === $sRegisterLinkTitle || empty($sRegisterLinkTitle)) {
            $sRegisterLinkTitle = $this->getTranslator()->trans('chameleon_system_theme_shop_standard.order_login.action_register');
        }

        $oExtranetConfiguration = TdbDataExtranet::GetInstance();
        if ($oExtranetConfiguration && $bCachingEnabled) {
            $oCacheTriggerManager->addTrigger($oExtranetConfiguration->table, $oExtranetConfiguration->id);
        }

        $aTextData = [];
        $aTextData['sTitle'] = $sRegisterLinkTitle;
        $aTextData['sLinkURL'] = $oExtranetConfiguration->GetFieldNodeRegisterIdPageURL().'?sSuccessURL='.urlencode($redirectURL);
        $oVisitor->SetMappedValue('aLinkRegister', $aTextData);

        // login link
        $sForgetPasswordLinkTitle = $oVisitor->GetSourceObject('sLoginLinkTitle');
        if (null === $sForgetPasswordLinkTitle || empty($sForgetPasswordLinkTitle)) {
            $sForgetPasswordLinkTitle = 'Anmelden';
        }

        $aTextData = [];
        $aTextData['sTitle'] = $sForgetPasswordLinkTitle;
        $aTextData['sLinkURL'] = $oExtranetConfiguration->GetFieldNodeLoginIdPageURL().'?sSuccessURL='.urlencode($redirectURL);
        $oVisitor->SetMappedValue('aLinkLogin', $aTextData);
    }

    /**
     * @return InputFilterUtilInterface
     */
    private function getInputFilterUtil()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.input_filter');
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslator()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
    }
}

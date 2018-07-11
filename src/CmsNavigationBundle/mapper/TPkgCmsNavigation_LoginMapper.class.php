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

/**
 * mapper adds a login/register link or a my account link to a navi.
/**/
class TPkgCmsNavigation_LoginMapper extends AbstractViewMapper
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements)
    {
        $oRequirements->NeedsSourceObject('aTree', 'array', array());
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager)
    {
        $aTree = $oVisitor->GetSourceObject('aTree');

        $user = $this->getExtranetUserProvider()->getActiveUser();

        $oExtranet = &TdbDataExtranet::GetInstance();
        if ($oExtranet && $bCachingEnabled) {
            $oCacheTriggerManager->addTrigger($oExtranet->table, $oExtranet->id);
        }

        if ($user->IsLoggedIn()) {
            $oNodeLogout = new TPkgCmsNavigationNode();
            $oNodeLogout->sLink = '?'.TTools::GetArrayAsURL(array('module_fnc['.$oExtranet->fieldExtranetSpotName.']' => 'Logout'));
            $oNodeLogout->sTitle = TGlobal::Translate('chameleon_system_cms_navigation.action.logout');
            $oNodeLogout->setDisableSubmenu(true);
            $aTree[] = $oNodeLogout;

            $oNodeMyAccount = new TPkgCmsNavigationNode();
            $oNodeMyAccount->load($oExtranet->fieldNodeMyAccountCmsTreeId);
            $oNodeMyAccount->setDisableSubmenu(true);
            $aTree[] = $oNodeMyAccount;
        } else {
            $oNodeLogin = new TPkgCmsNavigationNode();
            $oNodeLogin->load($oExtranet->fieldNodeLoginId);
            $oNodeLogin->setDisableSubmenu(true);
            $aTree[] = $oNodeLogin;

            $oNodeRegister = new TPkgCmsNavigationNode();
            $oNodeRegister->load($oExtranet->fieldNodeRegisterId);
            $oNodeRegister->setDisableSubmenu(true);
            $aTree[] = $oNodeRegister;
        }
        $oVisitor->SetMappedValue('aTree', $aTree);
    }

    /**
     * @return ExtranetUserProviderInterface
     */
    private function getExtranetUserProvider()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_extranet.extranet_user_provider');
    }
}

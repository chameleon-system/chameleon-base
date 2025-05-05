<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;

class MTPkgCmsNavigationEndPoint extends MTPkgViewRendererAbstractModuleMapper
{
    /**
     * {@inheritDoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager)
    {
        $aModuleConfig = $oVisitor->GetSourceObject('aModuleConfig');
        $sNaviName = (is_array($aModuleConfig) && isset($aModuleConfig['sNaviName'])) ? ($aModuleConfig['sNaviName']) : (false);

        if (false === $sNaviName) {
            return;
        }

        $activePortal = $this->getPortalDomainService()->getActivePortal();
        $sRootNaviId = $activePortal->GetNavigationTreeId($sNaviName);
        if ($bCachingEnabled) {
            $oCacheTriggerManager->addTrigger($activePortal->table, $activePortal->id);
        }

        if (!$sRootNaviId) {
            return;
        }
        $oRootNode = new TPkgCmsNavigationNode();
        $oRootNode->load($sRootNaviId);
        $aTree = $oRootNode->getAChildren();
        $oVisitor->SetMappedValue('aTree', $aTree);
        if ($bCachingEnabled) {
            $oCacheTriggerManager->addTrigger('cms_tree_node');
            $oCacheTriggerManager->addTrigger('cms_tree');
            $oCacheTriggerManager->addTrigger('cms_tpl_page');
        }
    }

    public function _AllowCache()
    {
        return true;
    }

    public function _GetCacheParameters()
    {
        $parameter = parent::_GetCacheParameters();

        $parameter['activepageid'] = $this->getActivePageService()->getActivePage()->id;
        $parameter['extranetUserGroups'] = $this->getActiveUserExtranetGroups();
        $parameter['isLoggedIn'] = $this->activeUserIsLoggedIn();

        return $parameter;
    }

    /**
     * @return string[]
     */
    private function getActiveUserExtranetGroups()
    {
        $activeUser = $this->getActiveUser();
        if (null === $activeUser) {
            return [];
        }

        return $activeUser->GetUserGroupIds();
    }

    /**
     * @return TdbDataExtranetUser|null
     */
    private function getActiveUser()
    {
        return TdbDataExtranetUser::GetInstance();
    }

    /**
     * @return bool
     */
    private function activeUserIsLoggedIn()
    {
        $activeUser = $this->getActiveUser();
        if (null === $activeUser) {
            return false;
        }

        return $activeUser->IsLoggedIn();
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private function getPortalDomainService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }
}

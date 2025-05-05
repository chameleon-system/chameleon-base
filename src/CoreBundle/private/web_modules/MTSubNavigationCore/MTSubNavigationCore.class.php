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

class MTSubNavigationCore extends MTNavigationCore
{
    /**
     * indicates that the navigation is extended by a subnavigation class.
     */
    protected $bIsSubNavigation = true;

    protected $bAllowHTMLDivWrapping = true;

    /**
     * {@inheritdoc}
     */
    public function Execute()
    {
        parent::Execute();
        if (!is_null($this->getPortalDomainService()->getActivePortal())) {
            // we fetch the portalNavi node using the current active page..
            $activePage = $this->getActivePageService()->getActivePage();
            $activePage->getBreadcrumb()->GoToStart();
            $rootNode = $activePage->getBreadcrumb()->Current();
            $iPortalNavi = $rootNode->id;

            if (array_key_exists('sNaviClass', $this->aModuleConfig)) {
                $sNaviClass = $this->aModuleConfig['sNaviClass'];
            }
            if (empty($sNaviClass)) {
                $sNaviClass = 'TCCustomNavigation';
            }
            if (array_key_exists('sNaviClassType', $this->aModuleConfig)) {
                $sNaviClassType = $this->aModuleConfig['sNaviClassType'];
            }
            if (empty($sNaviClassType)) {
                $sNaviClassType = 'Core';
            }

            $this->LoadNavi($sNaviClass, $sNaviClassType, $iPortalNavi);

            $totalSiblings = $this->data['oNavigation']->oRootNode->CountChildren();

            $this->data['sCurrentNodeName'] = $this->data['oNavigation']->oRootNode->GetName();

            if ($totalSiblings < 1) {
                $this->data['oNavigation'] = null;
            }
        } else {
            $this->data['oNavigation'] = null;
        }

        return $this->data;
    }

    /**
     * loads the subnavigation object.
     *
     * @param string $sNaviClass
     * @param string $sNaviClassType DEPRECATED
     * @param string root node id
     *
     * @todo remove $sNaviClassType from method calls and method itself
     */
    protected function LoadNavi($sNaviClass, $sNaviClassType, $iPortalNavi)
    {
        $this->data['oNavigation'] = new $sNaviClass();
        $this->data['oNavigation']->Load($iPortalNavi, $this->global->GetUserData('pagedef'));

        foreach ($this->aModuleConfig as $sParamKey => $sParamValue) {
            if (property_exists($this->data['oNavigation'], $sParamKey)) {
                if ('true' == $sParamValue) {
                    $sParamValue = true;
                } else {
                    if ('false' == $sParamValue) {
                        $sParamValue = false;
                    }
                }

                if ('bPlaceEachRootNodeInASeparateBlock' == $sParamKey && '1' == $sParamValue) {
                    $sParamValue = true;
                }

                $this->data['oNavigation']->$sParamKey = $sParamValue;
            }
        }
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private function getPortalDomainService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }
}

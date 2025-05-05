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
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\ExtranetBundle\Interfaces\ExtranetUserProviderInterface;

/**
 * manages navigation. The Navigation musst be passed to the module via 'sNaviName'
 * in the pagedef.
 * the parameter sNaviClass controlls which class in /classes/components/Navigation will
 * be used to generate the navi.
 * /**/
class MTNavigationCoreEndPoint extends TUserModelBase
{
    protected $bAllowHTMLDivWrapping = true;

    /**
     * The recursive navigation class (TCCustomNavigation or a extension of it).
     *
     * @var TCCustomNavigation
     */
    protected $oNavigation;

    /**
     * indicates that the navigation is extended by a subnavigation class.
     */
    protected $bIsSubNavigation = false;

    /**
     * {@inheritdoc}
     */
    public function Execute()
    {
        parent::Execute();
        $activePortal = $this->getPortalDomainService()->getActivePortal();
        if (null !== $activePortal) {
            if (array_key_exists('sNaviName', $this->aModuleConfig)) {
                if (is_null($this->oNavigation)) {
                    $sNaviClass = '';

                    $iPortalNavi = $activePortal->GetNavigationTreeId($this->aModuleConfig['sNaviName']);
                    if (array_key_exists('sNaviClass', $this->aModuleConfig)) {
                        $sNaviClass = $this->aModuleConfig['sNaviClass'];
                    }
                    if (empty($sNaviClass)) {
                        $sNaviClass = 'TCCustomNavigation';
                    }
                    $this->oNavigation = new $sNaviClass();
                    $this->oNavigation->Load($iPortalNavi, $this->getActivePageService()->getActivePage()->id);

                    foreach ($this->aModuleConfig as $sParamKey => $sParamValue) {
                        if (property_exists($this->oNavigation, $sParamKey)) {
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

                            $this->oNavigation->$sParamKey = $sParamValue;
                        }
                    }
                }
            } else {
                if (false == $this->bIsSubNavigation) {
                    echo 'misconfiguration: sNaviName NOT defined in page template spot';
                }
            }
        }
        $this->data['oNavigation'] = $this->oNavigation;

        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function _AllowCache()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function _GetCacheParameters()
    {
        $aParameters = parent::_GetCacheParameters();
        $aParameters['activepage'] = $this->getActivePageService()->getActivePage()->id;
        $aParameters['isCMSMode'] = TGlobal::IsCMSMode();

        $oGlobal = TGlobal::instance();
        $aParameters['__modulechooser'] = $oGlobal->GetUserData('__modulechooser');

        $oUser = $this->getExtranetUserProvider()->getActiveUser();
        if (null === $oUser) {
            return $aParameters;
        }
        $aParameters['userLoggedIn'] = $oUser->IsLoggedIn();
        $aGroupIdList = $oUser->GetUserGroupIds();
        if (count($aGroupIdList) > 0) {
            $aParameters['aUserGroupIdList'] = md5(serialize($aGroupIdList));
        }

        return $aParameters;
    }

    /**
     * if the content that is to be cached comes from the database (as ist most often the case)
     * then this function should return an array of assoc arrays that point to the
     * tables and records that are associated with the content. one table entry has
     * two fields:
     *   - table - the name of the table
     *   - id    - the record in question. if this is empty, then any record change in that
     *             table will result in a cache clear.
     *
     * @return array
     */
    public function _GetCacheTableInfos()
    {
        $tableInfo = parent::_GetCacheTableInfos();

        $aTablesAffectingNavi = [
            'cms_tree_node', 'cms_portal', 'cms_config', 'cms_portal_domains', 'cms_tree', 'cms_tpl_page',
        ];

        foreach ($aTablesAffectingNavi as $sTable) {
            $tableInfo[] = ['table' => $sTable, 'id' => null];
        }

        return $tableInfo;
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return ServiceLocator::get('chameleon_system_core.active_page_service');
    }

    /**
     * @return ExtranetUserProviderInterface
     */
    private function getExtranetUserProvider()
    {
        return ServiceLocator::get('chameleon_system_extranet.extranet_user_provider');
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private function getPortalDomainService()
    {
        return ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }
}

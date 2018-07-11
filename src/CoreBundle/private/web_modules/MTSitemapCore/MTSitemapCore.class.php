<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;

/**
 * @deprecated since 6.2.0 - no longer used.
 */
class MTSitemapCore extends TUserCustomModelBase
{
    /**
     * The recursive navigation class (TCCustomNavigation or a extension of it).
     *
     * @var TCCustomNavigation
     */
    protected $oNavigation = null;

    protected $bAllowHTMLDivWrapping = true;

    public function &Execute()
    {
        $this->data = parent::Execute();

        $this->LoadNavi();

        return $this->data;
    }

    /**
     * loads the navigation class and renders the html (ul/li).
     */
    protected function LoadNavi()
    {
        if (is_null($this->oNavigation)) {
            $oPortal = &TCMSPortal::GetPagePortal($this->global->GetUserData('pagedef'));
            $sNaviName = $this->GetNavigationIdentifier();
            $iPortalNavi = $oPortal->GetNavigationTreeId($sNaviName);

            $this->oNavigation = $this->LoadNaviClass();
            $this->oNavigation->Load($iPortalNavi, $this->global->GetUserData('pagedef'));
            $this->data['oNavigation'] = $this->oNavigation;
        }
    }

    /**
     * set your custom navigation class here.
     *
     * @return string
     */
    protected function LoadNaviClass()
    {
        return new TCCustomNavigation();
    }

    /**
     * set the navigation name of your portal (in CMS Portal Config) to get
     * a root node for the sitemap.
     *
     * @return string
     */
    protected function GetNavigationIdentifier()
    {
        return 'Hauptnavigation';
    }

    /**
     * if this function returns true, then the result of the execute function will be cached.
     *
     * @return bool
     */
    public function _AllowCache()
    {
        return true;
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
        $this->LoadNavi();

        $activePortal = $this->getPortalDomainService()->getActivePortal();

        $tableInfo = array(array('table' => 'cms_tree_node', 'id' => ''), array('table' => 'cms_portal', 'id' => $activePortal->id), array('table' => 'cms_config', 'id' => ''));

        $aNodeIds = $this->oNavigation->GetIdListOfAllTreeNodes();
        foreach ($aNodeIds as $sNodeID) {
            $tableInfo[] = array('table' => 'cms_tree', 'id' => $sNodeID);
        }

        $aPageIds = $this->oNavigation->GetIdListOfAllConnectedPages();
        foreach ($aPageIds as $sPageID) {
            $tableInfo[] = array('table' => 'cms_tpl_page', 'id' => $sPageID);
        }

        $tableInfo[] = array('table' => 'cms_portal_domains', 'id' => '');

        return $tableInfo;
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private function getPortalDomainService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }
}

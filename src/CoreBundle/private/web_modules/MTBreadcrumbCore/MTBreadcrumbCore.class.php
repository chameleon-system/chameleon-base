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

class MTBreadcrumbCore extends TUserModelBase
{
    public function Execute()
    {
        parent::Execute();
        $activePage = $this->getActivePageService()->getActivePage();
        $this->data['oBreadcrumb'] = $activePage->getBreadcrumb();

        return $this->data;
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
     * return an assoc array of parameters that describe the state of the module.
     *
     * @return array
     */
    public function _GetCacheParameters()
    {
        $aParameters = parent::_GetCacheParameters();
        $aParameters['activepagekey'] = TCMSActivePage::CacheGetKey();
        $aParameters['activePage'] = TCMSActivePage::CacheGetKey();
        $activePageService = $this->getActivePageService();

        $aParameters['activePageId'] = $activePageService->getActivePage()->id;

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
        $tableInfo = [['table' => 'cms_tree_node', 'id' => '']];

        $oActivePage = $this->getActivePageService()->getActivePage();
        while ($oTreeNode = $oActivePage->getBreadcrumb()->Next()) {
            /* @var $oTreeNode TCMSTreeNode */
            $tableInfo[] = ['table' => 'cms_tree', 'id' => $oTreeNode->id];
            if (method_exists($oTreeNode, 'GetAllLinkedPages')) {
                $oPagesList = $oTreeNode->GetAllLinkedPages();
                /** @var $oPagesList TCMSRecordList */
                $aPageIDs = $oPagesList->GetIdList();
                if (is_array($aPageIDs) && count($aPageIDs) > 0) {
                    $tableInfo[] = ['table' => 'cms_tpl_page', 'id' => $aPageIDs];
                }
            }
        }

        return $tableInfo;
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        $aIncludes = array_merge($aIncludes, $this->getResourcesForSnippetPackage('common/navigation'));

        return $aIncludes;
    }

    /**
     * @return ActivePageServiceInterface
     */
    protected function getActivePageService()
    {
        $activePageService = ChameleonSystem\CoreBundle\ServiceLocator::get(
            'chameleon_system_core.active_page_service'
        );

        return $activePageService;
    }
}

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
use ChameleonSystem\ExtranetBundle\Interfaces\ExtranetUserProviderInterface;

/**
 * holds a page object.
 * /**/
class TCMSPage extends TCMSPageAutoParent
{
    /**
     * used to cache some of the values that this function can return via functions.
     *
     * @var array
     */
    protected $_aDataCache = [];

    /**
     * the active division.
     *
     * @var TdbCmsDivision
     */
    private $division;

    /**
     * breadcrumb to the current page.
     *
     * @var TCMSPageBreadcrumb
     */
    private $breadcrumb;

    /**
     * @return TCMSPageBreadcrumb
     */
    public function getBreadcrumb()
    {
        if (null !== $this->breadcrumb) {
            return $this->breadcrumb;
        }
        $portal = self::getPortalDomainService()->getActivePortal();
        if (null === $portal) {
            return null;
        }
        $aStopNodes = TdbCmsDivision::GetStopNodes();
        // add navi nodes as stop nodes...
        $query = "SELECT `tree_node` FROM `cms_portal_navigation` WHERE `cms_portal_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($portal->id)."'";
        $navis = MySqlLegacySupport::getInstance()->query($query);
        $naviStopNodes = [];
        while ($navi = MySqlLegacySupport::getInstance()->fetch_assoc($navis)) {
            $naviStopNodes[] = $navi['tree_node'];
        }

        $aStopNodes = array_merge($naviStopNodes, $aStopNodes);
        $this->breadcrumb = $this->GetNavigationPath($aStopNodes);

        return $this->breadcrumb;
    }

    public static function GetNewInstance($sData = null, $sLanguage = null): TdbCmsTplPage
    {
        $page = null;
        $id = null;
        if (false === is_array($sData)) {
            $id = $sData;
        }
        if (null !== $id) {
            $page = self::getPageService()->getById($id, $sLanguage);
        }
        if (null !== $page) {
            return $page;
        }

        return parent::GetNewInstance($sData, $sLanguage);
    }

    /**
     * @return TdbCmsDivision|null
     */
    public function getDivision()
    {
        if (null !== $this->division) {
            return $this->division;
        }

        $this->division = TdbCmsDivision::GetPageDivision($this);

        return $this->division;
    }

    /**
     * Returns the primary tree id that this page connects to. If no primary
     * node has been set, it will return the first secondary node id we find.
     * Returns null if the page is not assigned to any page.
     *
     * @return string|null
     */
    public function GetMainTreeId()
    {
        if (!array_key_exists('mainTreeId', $this->_aDataCache)) {
            $this->_aDataCache['mainTreeId'] = null;
            $isSetInPageData = (is_array($this->sqlData) && array_key_exists('primary_tree_id_hidden', $this->sqlData));
            if ($isSetInPageData && !empty($this->sqlData['primary_tree_id_hidden'])) {
                $this->_aDataCache['mainTreeId'] = $this->sqlData['primary_tree_id_hidden'];
            } else {
                // not set in page data, so we will take the first tree node connected to the page
                $pageNodes = $this->GetPageTreeNodes();
                if (is_array($pageNodes) && count($pageNodes) > 0) {
                    $this->_aDataCache['mainTreeId'] = current($pageNodes);
                }
            }
        }

        return $this->_aDataCache['mainTreeId'];
    }

    /**
     * return true, if this page is also the home page of the active portal.
     *
     * @return bool
     */
    public function IsHomePage()
    {
        $bIsHome = $this->GetFromInternalCache('bIsHomePage');
        if (is_null($bIsHome)) {
            $bIsHome = false;
            $activePortal = self::getPortalDomainService()->getActivePortal();
            if (null !== $activePortal) {
                $iNode = $this->GetMainTreeId();
                if ($activePortal->fieldHomeNodeId == $iNode) {
                    $bIsHome = true;
                }
            }
            $this->SetInternalCache('bIsHomePage', $bIsHome);
        }

        return $bIsHome;
    }

    /**
     * Returns the name of the current active tree node. If the current page is connected
     * to multiple tree nodes only the main tree node's name is returned.
     *
     * @return string
     */
    public function GetMainTreeName()
    {
        $sNodeName = '';
        $iNode = $this->GetMainTreeId();
        $oNode = self::getTreeService()->getById($iNode);
        if (null !== $oNode) {
            $sNodeName = $oNode->GetName();
        }

        return $sNodeName;
    }

    /**
     * return an iterator of TreeNodes. if mainTreeNode is set, then it will
     * be used as a starting point (that way the tree path of a secondary node can
     * be retrieved).
     *
     * @param array $aStopNodes
     * @param int $mainTreeNode
     *
     * @return TCMSPageBreadcrumb
     */
    public function GetNavigationPath($aStopNodes, $mainTreeNode = null)
    {
        if (is_null($mainTreeNode)) {
            $mainTreeNode = $this->GetMainTreeId();
        }
        if (!array_key_exists('oTreePath', $this->_aDataCache) || !array_key_exists($mainTreeNode, $this->_aDataCache['oTreePath'])) {
            if (!array_key_exists('oTreePath', $this->_aDataCache)) {
                $this->_aDataCache['oTreePath'] = [];
            }
            $oTreeNodes = new TCMSPageBreadcrumb();
            $recordExists = true;
            $nodeId = $mainTreeNode;
            $oTreeNode = null;
            if (null === $mainTreeNode) {
                return $oTreeNodes;
            }
            $treeService = self::getTreeService();
            do {
                $oTreeNode = $treeService->getById($nodeId);
                if (null !== $oTreeNode) {
                    $oTreeNodes->AddItem($oTreeNode);
                    $nodeId = $oTreeNode->sqlData['parent_id'];
                } else {
                    $recordExists = false;
                }
            } while ($recordExists && !in_array($nodeId, $aStopNodes) && !in_array($oTreeNode->id, $aStopNodes));
            // now add the stop node as well... if a page is assigned to it...
            if ($recordExists) {
                $oTreeNode = $treeService->getById($nodeId);
                if (null !== $oTreeNode) {
                    if (false !== $oTreeNode->GetLinkedPage()) {
                        $oTreeNodes->AddItem($oTreeNode);
                    }
                }
            }

            $oTreeNodes->GoToStart();
            // need to reverse the list
            $oTreeNodes->ReverseItemList();

            $this->_aDataCache['oTreePath'][$mainTreeNode] = $oTreeNodes;
        }

        return $this->_aDataCache['oTreePath'][$mainTreeNode];
    }

    /**
     * returns an iterator holding all breadcrumbs connected to the page.
     *
     * @param array $aStopNodes
     *
     * @return TIterator
     */
    public function GetAllNavigationPaths($aStopNodes)
    {
        if (!array_key_exists('oBreadCrumbs', $this->_aDataCache)) {
            $this->_aDataCache['oBreadCrumbs'] = new TIterator();
            $mainTreeId = $this->GetMainTreeId();
            if (null === $mainTreeId) {
                return $this->_aDataCache['oBreadCrumbs'];
            }
            $allTreeIds = $this->GetPageTreeNodes();
            $oBreadcrumb = $this->GetNavigationPath($aStopNodes, $mainTreeId);
            $oBreadcrumb->bIsPrimary = true;
            $this->_aDataCache['oBreadCrumbs']->AddItem($oBreadcrumb);
            // now add all secondary nodes
            foreach ($allTreeIds as $treeID) {
                if ($treeID != $mainTreeId) {
                    $oBreadcrumb = $this->GetNavigationPath($aStopNodes, $treeID);
                    $this->_aDataCache['oBreadCrumbs']->AddItem($oBreadcrumb);
                }
            }
        }

        return $this->_aDataCache['oBreadCrumbs'];
    }

    /**
     * returns an array of all ACTIVE tree node IDs connected to the current page.
     *
     * @param bool $showOnlyActiveNodes - if true the hidden nodes will be filtered
     *
     * @return array
     */
    public function GetPageTreeNodes($showOnlyActiveNodes = true)
    {
        if (is_null($this->id)) {
            return [];
        }

        if (!array_key_exists('pageTreeNodes', $this->_aDataCache)) {
            $this->_aDataCache['pageTreeNodes'] = [];

            $parameters = [
                'pageId' => $this->id,
            ];
            $query = "SELECT `cms_tree`.`id`
                        FROM `cms_tree_node`
                  INNER JOIN `cms_tree` ON `cms_tree_node`.`cms_tree_id` = `cms_tree`.`id`
                       WHERE `cms_tree_node`.`contid` = :pageId
                         AND `cms_tree_node`.`tbl` = 'cms_tpl_page'";

            if ($showOnlyActiveNodes) {
                $parameters['currentDateTime'] = date('Y-m-d H:i:s');
                $query .= " AND (`cms_tree_node`.`start_date` <=  :currentDateTime AND (`cms_tree_node`.`end_date` >= :currentDateTime OR `cms_tree_node`.`end_date` = '0000-00-00 00:00:00'))";
                $query .= " AND (`cms_tree`.`hidden` = '0')";
            }

            $treeIdRows = $this->getDatabaseConnection()->fetchAllAssociative($query, $parameters);
            $this->_aDataCache['pageTreeNodes'] = array_map(function (array $idRow) {return $idRow['id']; }, $treeIdRows);
        }

        return $this->_aDataCache['pageTreeNodes'];
    }

    /**
     * return record list of connected tree nodes.
     *
     * @param bool $bShowOnlyActiveNodes - if true the hidden nodes will be filtered
     *
     * @return TdbCmsTreeList
     */
    public function GetTreeNodesObjects($bShowOnlyActiveNodes = true)
    {
        $sCurrentDateTime = date('Y-m-d H:i:s');
        $query = "SELECT DISTINCT `cms_tree`.*
                  FROM `cms_tree`
            INNER JOIN `cms_tree_node` ON (`cms_tree`.`id` = `cms_tree_node`.`cms_tree_id` AND `cms_tree_node`.`tbl` = 'cms_tpl_page')
                 WHERE `cms_tree_node`.`contid` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'
                 ";
        if ($bShowOnlyActiveNodes) {
            $query .= "  AND `cms_tree`.`hidden` = '0'
                     AND (`cms_tree_node`.`start_date` <= '".MySqlLegacySupport::getInstance()->real_escape_string($sCurrentDateTime)."' AND (`cms_tree_node`.`end_date` >= '".MySqlLegacySupport::getInstance()->real_escape_string($sCurrentDateTime)."' OR `cms_tree_node`.`end_date` = '0000-00-00 00:00:00'))
                  ";
        }
        $query .= ' ORDER BY `cms_tree`.`parent_id`, `cms_tree`.`entry_sort`';
        $oTreeNodes = TdbCmsTreeList::GetList($query);

        return $oTreeNodes;
    }

    /**
     * returns true if the page is marked as restricted.
     *
     * @return bool
     */
    public function IsRestricted()
    {
        $isRestricted = (array_key_exists('extranet_page', $this->sqlData) && 1 == $this->sqlData['extranet_page']);

        return $isRestricted;
    }

    /**
     * returns the portal for this page.
     *
     * @return TdbCmsPortal
     */
    public function GetPortal()
    {
        static $portalCache = [];
        $activePortal = self::getPortalDomainService()->getActivePortal();
        if ($activePortal && $this->fieldCmsPortalId === $activePortal->id) {
            return $activePortal;
        }

        // most pages use the same domain - so cache-lookup the data
        if (false === isset($portalCache[$this->fieldCmsPortalId])) {
            $portalCache[$this->fieldCmsPortalId] = $this->GetFieldCmsPortal();
        }

        return $portalCache[$this->fieldCmsPortalId];
    }

    /**
     * returns smart url for this page.
     *
     * @param bool $forceAbsolute
     * @param array $aAdditionalParameter
     * @param string $sLanguageIsoName
     *
     * @return string
     *
     * @deprecated since 6.1.0 - use chameleon_system_core.page_service::getLinkToPageObject*() instead
     */
    public function GetURL($forceAbsolute = false, $aAdditionalParameter = [], $sLanguageIsoName = '')
    {
        if (empty($sLanguageIsoName)) {
            $language = null;
        } else {
            $language = self::getLanguageService()->getLanguageFromIsoCode($sLanguageIsoName);
        }

        if ($forceAbsolute) {
            return self::getPageService()->getLinkToPageObjectAbsolute($this, $aAdditionalParameter, $language);
        } else {
            return self::getPageService()->getLinkToPageObjectRelative($this, $aAdditionalParameter, $language);
        }
    }

    /**
     * returns the language id for the current page.
     *
     * @return string|null
     */
    public function GetLanguageID()
    {
        if ('' !== $this->fieldCmsLanguageId) {
            return $this->fieldCmsLanguageId;
        }

        // load language from connected division based on main tree node of the page
        $division = $this->getDivision();
        if (null !== $division && false !== $division && '' !== $division->fieldCmsLanguageId) {
            return $division->fieldCmsLanguageId;
        }

        return null;
    }

    /**
     * returns the link to the current page including all current parameters.
     *
     * @var array
     * @var array
     * @var string
     *
     * @return string
     */
    public function GetRealURL($aAdditionalParameters = [], $aExcludeParameters = [], $sLanguageIsoName = '')
    {
        $language = null;
        if (!empty($sLanguageIsoName)) {
            $language = self::getLanguageService()->getLanguageFromIsoCode($sLanguageIsoName);
        }
        $sLink = self::getPageService()->getLinkToPageObjectRelative($this, [], $language);

        // now add all parameters
        if (count($aAdditionalParameters) > 0) {
            $aExcludeParameters = array_merge($aExcludeParameters, array_keys($aAdditionalParameters));
        }
        $aExcludeParameters[] = 'pagedef';
        $sParameters = $this->OutputDataAsURL($aExcludeParameters);
        if (!empty($sParameters)) {
            $sParameters = '?'.$sParameters;
        }

        foreach ($aAdditionalParameters as $key => $value) {
            if (!empty($sParameters)) {
                $sParameters .= '&amp;';
            } else {
                $sParameters .= '?';
            }
            $sParameters .= urlencode($key).'='.urlencode($value);
        }
        $sLink = $sLink.$sParameters;

        return $sLink;
    }

    /**
     * returns all POST and GET parameters as url.
     *
     * @param array $excludeArray
     *
     * @return string
     */
    private function OutputDataAsURL($excludeArray = [])
    {
        $aData = TGlobal::instance()->GetUserData();
        if (false === \is_array($aData)) {
            return '';
        }

        foreach ($excludeArray as $key) {
            if ('' === $key) {
                continue;
            }
            if (isset($aData[$key])) {
                unset($aData[$key]);
                continue;
            }
            // if the key contains [ and ], then we need to regex
            $iOpen = strpos($key, '[');
            if (false !== $iOpen) {
                $iClose = strpos($key, ']', $iOpen);
                if (false !== $iClose) {
                    if (preg_match("/^(.*?)(\[.*\])+/", $key, $aMatch)) {
                        if (3 == count($aMatch)) {
                            $aArrayKeys = explode('][', substr($aMatch[2], 1, -1));
                            $sPathString = $aMatch[1].'-';
                            foreach ($aArrayKeys as $Value) {
                                $sPathString .= $Value.'-';
                            }
                            $aData = TTools::DeleteArrayKeyByPath($aData, $sPathString);
                        }
                    }
                }
            }
        }

        return TTools::GetArrayAsURL($aData);
    }

    /**
     * checks if current extranet user is allowed to see this page.
     *
     * @return bool
     */
    public function AllowAccessByCurrentUser()
    {
        $allowAccess = false;

        if (!isset($this->sqlData) || !is_array($this->sqlData)) {
            $allowAccess = true;
        } elseif (isset($this->sqlData) && is_array($this->sqlData) && (!array_key_exists('extranet_page', $this->sqlData) || '0' == $this->sqlData['extranet_page'])) {
            $allowAccess = true;
        } elseif (TTools::CMSEditRequest()) {
            $allowAccess = true;
        } else {
            $extranetUser = $this->getExtranetUserProvider()->getActiveUser();
            if ($extranetUser->IsLoggedIn()) {
                $allowAccess = $extranetUser->AllowPageAccess($this->id);
            }
        }

        return $allowAccess;
    }

    /**
     * return a cache key for the page
     * - empty per default because this is only a stub so extensions of the page
     * can add custom parameters to cache key
     * IMPORTANT: for we return the cache key as string, merge the key with the
     * one from parent in your extensions.
     *
     * @return string
     */
    public static function CacheGetKey()
    {
        return '';
    }

    /**
     * @return ExtranetUserProviderInterface
     */
    private function getExtranetUserProvider()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_extranet.extranet_user_provider');
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private static function getPortalDomainService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }
}

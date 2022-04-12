<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Util\UrlNormalization\UrlNormalizationUtil;
use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * important note!
 * this db object belongs to cms_tree NOT cms_tree_node table.
 *
/**/
class TCMSTreeNode extends TCMSRecord implements ICmsLinkableObject
{
    /**
     * Hard-coded ID of the root tree node.
     * This is a legacy value, please do not take it as an example on how to do things right.
     */
    public const TREE_ROOT_ID = '99';

    /**
     * class internal cache array.
     *
     * @var array
     */
    private $_cache = array();

    public function __construct($id = null, $table = 'cms_tree')
    {
        parent::__construct($table, $id);
    }

    /**
     * @deprecated Named constructors are deprecated and will be removed with PHP8. When calling from a parent, please use `parent::__construct` instead.
     * @see self::__construct
     */
    public function TCMSTreeNode()
    {
        $this->callConstructorAndLogDeprecation(func_get_args());
    }


    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        switch ($name) {
            case 'oChildren': return $this->GetChildren();
            case 'bChildrenIncludeHiddenChildren': return false;
            default: return null;
        }
    }

    /**
     * @param bool $includeHidden - also count hidden menuitems
     *
     * @return int
     */
    public function CountChildren($includeHidden = false)
    {
        $oChildren = &$this->GetChildren($includeHidden);

        return $oChildren->Length();
    }

    /**
     * returns the children of the node.
     *
     * @param bool   $includeHidden - also include hidden menuitems in the list
     * @param string $languageId
     *
     * @return TdbCmsTreeList
     */
    public function &GetChildren($includeHidden = false, $languageId = null)
    {
        $children = self::getTreeService()->getChildren($this->id, $includeHidden, $languageId);

        return $children;
    }

    /**
     * adds an ORDER BY (field: sort_order) to the query, if the sort_order field
     * is marked as translated, it will sort by the appropriate language field.
     *
     * @param string $query
     *
     * @return string
     */
    protected function AddEntrySortOrder($query)
    {
        $sEntrySortField = 'entry_sort';
        $oPortal = $this->getPortalDomainService()->getActivePortal();
        if (TdbCmsTree::CMSFieldIsTranslated('entry_sort')) {
            $sLanguagePrefix = TGlobal::GetLanguagePrefix();
            $sLanguageId = self::getLanguageService()->getActiveLanguageId();
        }
        if (!empty($sLanguagePrefix) && !empty($sLanguageId) && ($sLanguageId != $oPortal->fieldCmsLanguageId || array_key_exists($sEntrySortField.'__'.$sLanguagePrefix, $this->sqlData))) {
            $sEntrySortField .= '__'.$sLanguagePrefix;
        }
        $query .= ' ORDER BY `'.MySqlLegacySupport::getInstance()->real_escape_string($this->table).'`.`'.MySqlLegacySupport::getInstance()->real_escape_string($sEntrySortField).'`';

        return $query;
    }

    /**
     * returns an array of all TCMSTreeNodes to the current node (including stopnode if given
     * and current node.
     *
     * @param array $aStopNodes
     *
     * @return TdbCmsTree[]
     */
    public function GetPath($aStopNodes = null)
    {
        static $aPathCache = array();
        $aKey = $this->GetCacheIdentKeyParameters(array('method' => 'GetPath', 'aStopNodes' => $aStopNodes, 'language' => $this->GetLanguage()));
        $sKey = TCacheManager::GetKey($aKey);
        if (!array_key_exists($sKey, $aPathCache)) {
            $aPath = array();
            if (!is_array($aStopNodes)) {
                $aStopNodes = array($aStopNodes);
            }
            if (!empty($this->sqlData['parent_id']) && !in_array($this->sqlData['id'], $aStopNodes)) {
                $oParentNode = self::getTreeService()->getById($this->sqlData['parent_id'], $this->GetLanguage());
                if (null !== $oParentNode) {
                    $aParentPath = $oParentNode->GetPath($aStopNodes);
                    foreach ($aParentPath as $parentElement) {
                        $aPath[] = $parentElement;
                    }
                }
            }
            $aPath[] = $this;
            $aPathCache[$sKey] = $aPath;
        } else {
            $aPath = $aPathCache[$sKey];
        }

        return $aPath;
    }

    /**
     * returns breadcrumb iterator object to the node.
     *
     * @param bool $bDropFirstItem - set to true if you don't want to include the first item in the list (this is often the navigation entry point)
     *
     * @return TCMSPageBreadcrumb
     */
    public function GetBreadcrumb($bDropFirstItem = false)
    {
        $oBreadcrumb = new TCMSPageBreadcrumb();
        /** @var $oBreadcrumb TCMSPageBreadcrumb */
        // get all navigation start ids. we stop traversing at that point

        $oCmsPortalNavigationList = TdbCmsPortalNavigationList::GetList();
        /** @var $oCmsPortalNavigationList TdbCmsPortalNavigationList */
        $aNavigationIds = $oCmsPortalNavigationList->GetIdList('tree_node');
        $aPath = $this->GetPath($aNavigationIds);
        if ($bDropFirstItem) {
            if (count($aPath) > 0) {
                unset($aPath[0]);
            }
        }
        foreach (array_keys($aPath) as $key) {
            $oBreadcrumb->AddItem($aPath[$key]);
        }

        return $oBreadcrumb;
    }

    /**
     * returns the path to the current node relative to the Navigation to which it belongs
     * as a text string separated by $sSeparator.
     *
     * @param string $sSeparator                  - string used to separate items
     * @param bool   $bDropFirstItem              - set to true if you don't want to include the first item in the list (this is often the navigation entry point)
     * @param bool   $bDisableLowerCaseConversion
     *
     * @return string
     */
    public function GetTextPathToNode($sSeparator = '/', $bDropFirstItem = true, $bDisableLowerCaseConversion = false)
    {
        $sPath = '';
        $oBreadCrumb = $this->GetBreadcrumb($bDropFirstItem);
        while ($oNode = $oBreadCrumb->Next()) {
            /** @var $oNode TCMSTreeNode */
            if (!empty($sPath)) {
                $sPath .= $sSeparator;
            }
            $sPath .= $oNode->sqlData['urlname'];
        }
        if (CHAMELEON_SEO_URL_REWRITE_TO_LOWERCASE && !$bDisableLowerCaseConversion) {
            mb_strtolower($sPath);
        }

        return $sPath;
    }

    /**
     * returns true if the current node is active.
     *
     * @return bool
     */
    public function IsActive()
    {
        if ('0' == $this->sqlData['hidden']) {
            $activePortal = $this->getPortalDomainService()->getActivePortal();
            if ($activePortal->fieldShowNotTanslated) {
                return true;
            } else {
                $oURLData = &TCMSSmartURLData::GetActive();
                $sLanguagePrefix = $oURLData->sLanguageIdentifier;
                if (!empty($sLanguagePrefix)) {
                    if (empty($this->sqlData['name__'.$sLanguagePrefix])) {
                        return false;
                    } else {
                        return true;
                    }
                } else {
                    return true;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * Get all attributes for the <a> tag of the node.
     *
     * @return array
     */
    public function GetLinkAttributes()
    {
        $aKey = $this->GetCacheIdentKeyParameters(array('method' => 'GetLinkAttributes'));
        $sKey = TCacheManagerRuntimeCache::GetKey($aKey);
        $aLinkAttributes = TCacheManagerRuntimeCache::GetContents($sKey);
        if (false === $aLinkAttributes) {
            $aLinkAttributes = array();

            $aLinkAttributes['href'] = $this->getLink();

            $sTarget = $this->GetTarget();
            if ('_self' !== $sTarget) {
                $aLinkAttributes['target'] = $sTarget;
            }

            if ($this->GetSEONoFollowForActivePage()) {
                $aLinkAttributes['rel'] = 'nofollow';
            }

            if (array_key_exists('html_access_key', $this->sqlData) && '' != $this->sqlData['html_access_key']) {
                $aLinkAttributes['accesskey'] = $this->sqlData['html_access_key'];
            }

            if (empty($aLinkAttributes['href'])) {
                $aLinkAttributes['rel'] = 'nofollow';
            }
            $aLinkAttributes['title'] = str_replace('&#92;n', ' ', TGlobal::OutHTML($this->GetName()));
            TCacheManagerRuntimeCache::SetContent($sKey, $aLinkAttributes);
        }

        return $aLinkAttributes;
    }

    /**
     * return an array identifying the object - descendant classes may require attributes beyond id.
     *
     * @return array
     */
    protected function GetCacheIdentKeyParameters($aAdditionalKeys = array())
    {
        $aKey = $aAdditionalKeys;
        $aKey['class'] = __CLASS__;
        $aKey['id'] = $this->id;

        return $aKey;
    }

    /**
     * return all attributes for the <a> tag of the node as String.
     *
     * @return string
     */
    public function GetLinkAttributesAsString()
    {
        $aAtr = $this->GetLinkAttributes();
        $aRet = array();
        foreach ($aAtr as $sKey => $sVal) {
            $aRet[] = $sKey.'="'.$sVal.'"';
        }

        return implode(' ', $aRet);
    }

    /**
     * returns true if search engines should be prevented from following this link from the active page.
     *
     * @return bool
     */
    public function GetSEONoFollowForActivePage()
    {
        $invertList = self::getTreeService()->getInvertedNoFollowRulePageIds($this->id);
        $activePageId = $this->getActivePageService()->getActivePage()->id;
        $setNoFollow = '1' === $this->sqlData['seo_nofollow'];

        if (in_array($activePageId, $invertList)) {
            $setNoFollow = !$setNoFollow;
        }

        return $setNoFollow;
    }

    /**
     * returns true if the given node is below the node given by parentNode
     * function my be called statically.
     *
     * @param string $parentNodeId
     * @param string $nodeId
     *
     * @return bool
     */
    public static function IsBelowNode($parentNodeId, $nodeId)
    {
        $treeService = self::getTreeService();
        $node = $treeService->getById($nodeId);
        $parent = $treeService->getById($parentNodeId);

        if (null === $node || null === $parent) {
            return false;
        }

        return $parent->fieldLft <= $node->fieldLft && $parent->fieldRgt >= $node->fieldRgt;
    }

    /**
     * returns the portal to which this node belongs.
     *
     * @return TdbCmsPortal
     */
    public function GetNodePortal()
    {
        $query = '
            select cms_portal.*
              FROM cms_tree
        INNER JOIN cms_portal ON cms_tree.id = cms_portal.main_node_tree
             WHERE cms_tree.lft < :lft AND cms_tree.rgt > :rgt
          ORDER BY cms_tree.lft DESC
             LIMIT 1
        ';
        $portal = $this->getDatabaseConnection()->fetchAssoc($query, array('lft' => $this->sqlData['lft'], 'rgt' => $this->sqlData['rgt']));
        if (false === $portal) {
            return null;
        }

        return TdbCmsPortal::GetNewInstance($portal);
    }

    /**
     * returns url to the node IF a page is connected to the node.
     *
     * @param bool                $absolute           set to true to include the domain in the link
     * @param string|null         $anchor
     * @param array               $optionalParameters
     * @param TdbCmsPortal|null   $portal
     * @param TdbCmsLanguage|null $language
     *
     * @return string
     */
    public function getLink($absolute = false, $anchor = null, $optionalParameters = array(), TdbCmsPortal $portal = null, TdbCmsLanguage $language = null)
    {
        $treeNodeService = self::getTreeService();
        try {
            if ($absolute) {
                $url = $treeNodeService->getLinkToPageForTreeAbsolute($this, $optionalParameters, $language);
            } else {
                $url = $treeNodeService->getLinkToPageForTreeRelative($this, $optionalParameters, $language);
            }
        } catch (RouteNotFoundException $e) {
            return '';
        }
        if (null !== $anchor) {
            $url .= '#'.urlencode($anchor);
        }

        return $url;
    }

    /**
     * overwrite this method to replace variables like [{VAR_NAME}].
     *
     * @param string $sURL
     *
     * @return string
     */
    public function replacePlaceHolderInURL($sURL)
    {
        return $sURL;
    }

    /**
     * returns requested target to node.
     *
     * @return string
     */
    public function GetTarget()
    {
        $sTarget = '_self';
        if (1 == $this->sqlData['linkTarget']) {
            $sTarget = '_blank';
        }

        return $sTarget;
    }

    /**
     * return linked page.
     *
     * @param bool $bPreventFilter - set this to true in CMS backend mode to get the page object without filtering the time and active state
     *
     * @return TdbCmsTplPage|false
     */
    public function GetLinkedPageObject($bPreventFilter = false)
    {
        $linkedPage = self::getPageService()->getByTreeId($this->id, $this->GetLanguage());

        return null === $linkedPage ? false : $linkedPage; // would like to return null, but assure BC instead.
    }

    /**
     * returns the page id linked to the node.
     *
     * @param bool $bPreventFilter - set this to true in CMS backend mode to get the page id without filtering the time and active state
     *
     * @return string|bool - linked pageid or false
     */
    public function GetLinkedPage($bPreventFilter = false)
    {
        $oLinkedPage = $this->GetLinkedPageObject($bPreventFilter);
        if (false !== $oLinkedPage) {
            return $oLinkedPage->id;
        }

        return false;
    }

    /**
     * return a RecordList of TdbCmsTplPageList of all pages connected to the node. The
     * TdbCmsTplPageList objects will include 4 special values:
     *   node_active - set to 1 if the node is active, to 0 when it is not
     *   node_start_date - the start date at which the node becomes active
     *   node_end_date - the date when the node is no longer active
     *   is_primary_page - set to 1 if a page is connected as the primary page, 0 when it is not.
     *
     * @return TdbCmsTplPageList
     */
    public function &GetAllLinkedPages()
    {
        $query = "SELECT `cms_tpl_page`.*,
                      `cms_tree_node`.`active` AS node_active,
                      `cms_tree_node`.`start_date` AS node_start_date,
                      `cms_tree_node`.`end_date` AS node_end_date,
                      IF(`cms_tpl_page`.`primary_tree_id_hidden` = `cms_tree_node`.`cms_tree_id`,1,0) AS is_primary_page
                 FROM `cms_tree_node`
           INNER JOIN `cms_tpl_page` ON `cms_tree_node`.`contid` = `cms_tpl_page`.`id`
                WHERE `cms_tree_node`.`cms_tree_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'
                  AND `cms_tree_node`.`tbl` = 'cms_tpl_page'
             ORDER BY is_primary_page DESC, node_start_date ASC
              ";

        $oCmsTplPageList = TdbCmsTplPageList::GetList($query);
        /** @var $oCmsTplPageList TdbCmsTplPageList */
        return $oCmsTplPageList;
    }

    /**
     * checks if the current web user is allowed to see this tree node.
     *
     * @return bool
     */
    public function AllowAccessByCurrentUser()
    {
        $showNode = false;

        $oLinkedPage = $this->GetLinkedPageObject();
        if (false === $oLinkedPage) {
            $showNode = true;
        } else {
            if (isset($this->sqlData['show_extranet_page']) && '1' === $this->sqlData['show_extranet_page']) {
                $showNode = true;
            } else {
                $showNode = $oLinkedPage->AllowAccessByCurrentUser();
            }
        }

        return $showNode;
    }

    /**
     * fetches the module specific sub navigation if connected through module instance in tree node.
     *
     * @return string
     */
    public function GetModuleSubNavi()
    {
        $returnMenu = '';

        if (!array_key_exists('moduleNavigation', $this->_cache) || empty($this->_cache['moduleNavigation'])) {
            // load a module instance
            if (!empty($this->sqlData['cms_tpl_module_instance_id'])) {
                $oModuleInstance = new TCMSTPLModuleInstance();
                /** @var $oModuleInstance TCMSTPLModuleInstance */
                $oModuleInstance->Load($this->sqlData['cms_tpl_module_instance_id']);
                $oModuleInstance->Init('tmpSpot', 'standard');
                $this->_cache['moduleNavigation'] = $oModuleInstance->RenderNavigation();
                $returnMenu = $this->_cache['moduleNavigation'];
            }
        } else {
            $returnMenu = $this->_cache['moduleNavigation'];
        }

        return $returnMenu;
    }

    /**
     * returns the parent node of the tree node.
     *
     * @return TdbCmsTree|null
     */
    public function &GetParentNode()
    {
        $oParentNode = self::getTreeService()->getById($this->sqlData['parent_id']);

        return $oParentNode;
    }

    /**
     * returns the previous node of the current on the same treelevel or null.
     *
     * @param bool $bIncludeHidden - also check hidden nodes
     *
     * @return TCMSTreeNode|null
     */
    public function &GetPreviousNode($bIncludeHidden = false)
    {
        $oParent = &$this->GetParentNode();

        $oTempNode = null;
        if (!is_null($oParent)) {
            $oNodeList = &$oParent->GetChildren($bIncludeHidden);
            $oNodeList->GoToStart();
            while ($oNode = &$oNodeList->Next()) {
                if ($oNode->id == $this->id) {
                    break;
                }
                $oTempNode = $oNode;
            }
        }

        return $oTempNode;
    }

    /**
     * returns the next node of the current on the same treelevel or null.
     *
     * @param bool $bIncludeHidden - also check hidden nodes
     *
     * @return TCMSTreeNode
     */
    public function &GetNextNode($bIncludeHidden = false)
    {
        $oParent = &$this->GetParentNode();

        $oNode = false;
        if (!is_null($oParent)) {
            $oNodeList = &$oParent->GetChildren($bIncludeHidden);
            $oNodeList->GoToStart();
            $bNextLoopIsRequiredNode = false;
            while ($oNode = &$oNodeList->Next()) {
                if ($bNextLoopIsRequiredNode) {
                    break;
                }
                if ($oNode->id == $this->id) {
                    $bNextLoopIsRequiredNode = true;
                }
            }
        }
        if (!$oNode) {
            $oNode = null;
        }

        return $oNode;
    }

    /**
     * Get the active page tree connection for tree id.
     *
     * @return TdbCmsTreeNode
     */
    public function GetActivePageTreeConnectionForTree()
    {
        $sCurrentDateTime = date('Y-m-d H:i:s');
        $query = "SELECT * FROM `cms_tree_node`
                       WHERE `cms_tree_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'
                         AND `active` = '1'
                         AND `tbl` = 'cms_tpl_page'
                         AND `contid` != ''
                         AND `start_date` <= '".MySqlLegacySupport::getInstance()->real_escape_string($sCurrentDateTime)."'
                         AND (`end_date` >= '".MySqlLegacySupport::getInstance()->real_escape_string($sCurrentDateTime)."' OR `end_date` = '0000-00-00 00:00:00')
                    ORDER BY `start_date` DESC, `cmsident` DESC
                       LIMIT 1 ";
        $oCmsTreeNodeList = TdbCmsTreeNodeList::GetList($query);
        /** @var $oCmsTreeNodeList TdbCmsTreeNodeList */
        $oCurrentActiveTreeConnection = $oCmsTreeNodeList->Current();

        return $oCurrentActiveTreeConnection;
    }

    /**
     * Get page tree connection date information as rendered html.
     *
     * @param string $sTreeId
     * @param string $sPageId
     *
     * @return string
     */
    protected function GetPageTreeConnectionDateInformationHTML($sTreeId, $sPageId)
    {
        $oCmsTree = self::getTreeService()->getById($sTreeId);

        if (null === $oCmsTree) {
            return '';
        }

        $oCurrentActiveTreeConnection = $oCmsTree->GetActivePageTreeConnectionForTree();
        $oLocal = TCMSLocal::GetActive();
        $sPageTreeConnectionDateInformation = '';
        $sQuery = "SELECT * FROM `cms_tree_node`
                        WHERE `cms_tree_node`.`cms_tree_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sTreeId)."'
                          AND `cms_tree_node`.`tbl` = 'cms_tpl_page'
                          AND `cms_tree_node`.`contid` = '".MySqlLegacySupport::getInstance()->real_escape_string($sPageId)."'";
        $oPageTreeConnectionList = TdbCmsTreeNodeList::GetList($sQuery);
        while ($oPageTreeConnection = $oPageTreeConnectionList->Next()) {
            if ($oPageTreeConnection->id == $oCurrentActiveTreeConnection->id) {
                $sPageTreeConnectionClass = 'dateinfo_inner_active';
            } else {
                $sPageTreeConnectionClass = 'dateinfo_inner_none_active';
            }
            $sPageTreeConnectionDateInformation .= '<div class="dateinfo_inner '.$sPageTreeConnectionClass.'">'.$oLocal->FormatDate($oPageTreeConnection->fieldStartDate).' - '.$oLocal->FormatDate($oPageTreeConnection->fieldEndDate).'</div>';
        }

        return $sPageTreeConnectionDateInformation;
    }

    /**
     * returns a breadcrumb html of the tree node for use in backend (for example: TCMSFieldTreeNode).
     *
     * @return string
     */
    public function GetTreeNodePathAsBackendHTML()
    {
        $path = '<ol class="breadcrumb pl-0">
                    <li class="breadcrumb-item"><i class="fas fa-sitemap"></i></li>';
        if (is_array($this->sqlData)) { // record loaded
            $aPath = $this->GetPath();
            foreach (array_keys($aPath) as $key) {
                $path .= '<li class="breadcrumb-item">';
                $path .= TGlobal::OutHTML($aPath[$key]->fieldName);
                $path .= "</li>\n";
            }
        } else {
            $path .= '<li class="breadcrumb-item">'.TGlobal::Translate('chameleon_system_core.error.tree_path_nothing_assigned').'</li>';
        }

        $path .= '</ol>';

        return $path;
    }

    /**
     * function is called whenever a tree node path is changed (node is moved, renamed - or one of its parents is changed)
     * the method is moved into this hook, to allow easy overwrite without having to overwrite the TCMSTableEditor object.
     *
     * @param bool $bIgnoreWorkflow deprecated
     */
    public function TriggerUpdateOfPathCache($bIgnoreWorkflow = false)
    {
        $sPath = $this->GetTextPathToNode('/');
        if (!empty($sPath) && '/' != substr($sPath, 0, 1)) {
            $sPath = '/'.$sPath;
        }
        $oTableConf = $this->GetTableConf();
        /** @var $oChildEditor TCMSTableEditorManager */
        $oChildEditor = new TCMSTableEditorManager();
        $oChildEditor->Init($oTableConf->id, $this->id);
        $oChildEditor->AllowEditByAll(true);
        $oChildEditor->SaveField('pathcache', $sPath);
        $oChildEditor->AllowEditByAll(false);
        unset($oChildEditor);
        TCacheManager::PerformeTableChange($oTableConf->fieldName, $this->id);
        $oChildren = $this->GetChildren(true);
        /** @var $oChild TCMSTreeNode */
        while ($oChild = $oChildren->Next()) {
            $oChild->TriggerUpdateOfPathCache($bIgnoreWorkflow);
        }
    }

    /**
     * return true if the current (ie. active) page is conntected to this node.
     *
     * @return bool
     */
    public function IsActiveNode()
    {
        $bIsActive = false;
        $activePage = $this->getActivePageService()->getActivePage();
        if ($activePage) {
            $aNodes = $activePage->GetPageTreeNodes(false);
            if (in_array($this->id, $aNodes)) {
                $bIsActive = true;
            }
        }

        return $bIsActive;
    }

    /**
     * return true if the current node is somewhere in the active breadcrumb.
     *
     * @return bool
     */
    public function IsInBreadcrumb()
    {
        $bIsInBreadcrumb = false;
        $activePage = $this->getActivePageService()->getActivePage();
        if ($activePage) {
            $bIsInBreadcrumb = $activePage->getBreadcrumb()->NodeInBreadcrumb($this->id);
        }

        return $bIsInBreadcrumb;
    }

    /**
     * return true if the current node is somewhere in the active breadcrumb.
     *
     * @return bool
     */
    public function GetCSSClassName()
    {
        $sClassName = strtolower($this->getUrlNormalizationUtil()->normalizeUrl($this->sqlData['name']));
        $sClassName = preg_replace('[^a-z]', '', $sClassName);
        $nodeClass = 'node'.strtolower($sClassName);

        return $nodeClass;
    }

    /**
     * @return UrlNormalizationUtil
     */
    private function getUrlNormalizationUtil()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.url_normalization');
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private function getPortalDomainService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }

    /**
     * @return UrlUtil
     */
    private function getUrlUtil()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.url');
    }
}

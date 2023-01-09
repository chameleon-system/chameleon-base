<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Exception\InvalidPortalDomainException;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Service\SystemPageServiceInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use esono\pkgCmsCache\CacheInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class TCMSPortal extends TCMSRecord
{
    public $_rootNode = null;
    public $_aCache = array();

    /**
     * you should use the factory GetPagePortal to create the portal (if the page is known).
     *
     * @param int $id - portal id
     */
    public function __construct($id = null)
    {
        parent::__construct('cms_portal', $id);
    }

    /**
     * @deprecated Named constructors are deprecated and will be removed with PHP8. When calling from a parent, please use `parent::__construct` instead.
     * @see self::__construct
     */
    public function TCMSPortal()
    {
        $this->callConstructorAndLogDeprecation(func_get_args());
    }

    /**
     * returns true if the current portal is marked as the primary portal.
     *
     * @return bool
     */
    public function IsPrimaryPortal()
    {
        return $this->id === TdbCmsConfig::GetInstance()->fieldCmsPortalId;
    }

    public static function GetStopNodes($portalID = null)
    {
        static $aStopNodes;
        if (is_null($portalID)) {
            $key = 'none';
        } else {
            $key = $portalID;
        }
        if (!$aStopNodes || !array_key_exists($key, $aStopNodes)) {
            if (!isset($aStopNodes)) {
                $aStopNodes = array();
            }
            $aStopNodes[$key] = array();
            $query = 'SELECT `main_node_tree`
                    FROM `cms_portal`
                 ';
            if (!is_null($portalID)) {
                $query .= " WHERE `cms_portal`.`id` = '".MySqlLegacySupport::getInstance()->real_escape_string($portalID)."'";
            }
            $portals = MySqlLegacySupport::getInstance()->query($query);
            while ($portal = MySqlLegacySupport::getInstance()->fetch_assoc($portals)) {
                $aStopNodes[$key][] = $portal['main_node_tree'];
            }
        }

        return $aStopNodes[$key];
    }

    public function GetNaviNodeIds()
    {
        static $aNaviNodeIds;
        $key = $this->id;
        if (is_null($key)) {
            $key = 'none';
        }
        if (!$aNaviNodeIds || !array_key_exists($key, $aNaviNodeIds)) {
            if (!$aNaviNodeIds) {
                $aNaviNodeIds = array();
            }
            $aNaviNodeIds[$key] = array();
            if (!is_null($this->id)) {
                $query = "SELECT `tree_node` FROM `cms_portal_navigation` WHERE `cms_portal_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'";
                $navis = MySqlLegacySupport::getInstance()->query($query);
                while ($navi = MySqlLegacySupport::getInstance()->fetch_assoc($navis)) {
                    $aNaviNodeIds[$key][] = $navi['tree_node'];
                }
            }
        }

        return $aNaviNodeIds[$key];
    }

    /**
     * returns the link to the home page of the portal. The home link is given by
     * the first page connected to the primary division.
     *
     * @return TdbCmsTree
     */
    public function GetPortalHomeNode()
    {
        if (false === isset($this->_aCache['_portalHomeNode'])) {
            $this->_aCache['_portalHomeNode'] = self::getTreeService()->getById($this->sqlData['home_node_id']);
        }

        return $this->_aCache['_portalHomeNode'];
    }

    /**
     * get the portal or portals that match the given domain name.
     *
     * @param string $sDomainName
     *
     * @return TCMSPortal or array of TCMSPortal
     */
    public static function GetDomainPortal($sDomainName)
    {
        static $aPortalList;
        if (!$aPortalList || !isset($aPortalList[$sDomainName])) {
            if (!is_array($aPortalList)) {
                $aPortalList = array();
            }
            $aPortalList[$sDomainName] = null;
            $query = "SELECT `cms_portal_domains`.*
                    FROM `cms_portal_domains`
              INNER JOIN `cms_portal` ON `cms_portal_domains`.`cms_portal_id` = `cms_portal`.`id`
                   WHERE `cms_portal_domains`.`name` = '".MySqlLegacySupport::getInstance()->real_escape_string($sDomainName)."'
                      OR `cms_portal_domains`.`sslname` = '".MySqlLegacySupport::getInstance()->real_escape_string($sDomainName)."'
                   ORDER BY `cms_portal`.`sort_order` ASC";
            $portalDomains = MySqlLegacySupport::getInstance()->query($query);
            $aPortalIdList = array();
            while ($domain = MySqlLegacySupport::getInstance()->fetch_assoc($portalDomains)) {
                $aPortalIdList[] = new self($domain['cms_portal_id']);
            }
            if (count($aPortalIdList) > 1) {
                $aPortalList[$sDomainName] = $aPortalIdList;
            } else {
                $aPortalList[$sDomainName] = $aPortalIdList[0];
            }
        }

        return $aPortalList[$sDomainName];
    }

    /**
     * checks if the domain is owned by this portal.
     *
     * @param string $sDomainName - name of the domain
     *
     * @return bool
     */
    public function IsDomainOfThisPortal($sDomainName)
    {
        $isDomainOfPortal = false;
        $query = "SELECT * FROM `cms_portal_domains` WHERE `cms_portal_id`= '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."' AND `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($sDomainName)."'";

        if ($domain = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
            if ($this->id == $domain['cms_portal_id']) {
                $isDomainOfPortal = true;
            }
        }
        $sqlError = MySqlLegacySupport::getInstance()->error();
        if (!empty($sqlError)) {
            trigger_error('SQL Error: '.$sqlError, E_USER_WARNING);
        }

        return $isDomainOfPortal;
    }

    /**
     * return image object of the portals logo.
     *
     * @return TCMSImage
     */
    public function GetLogo()
    {
        if (!array_key_exists('_portalLogo', $this->_aCache)) {
            $this->_aCache['_portalLogo'] = new TCMSImage();
            $this->_aCache['_portalLogo']->Load($this->sqlData['images']);
        }

        return $this->_aCache['_portalLogo'];
    }

    /**
     * returns a pointer to the primary division of this portal.
     *
     * @return TdbCmsDivision
     */
    public function GetPrimaryDivision()
    {
        if (!array_key_exists('_primaryDivision', $this->_aCache)) {
            $this->_aCache['_primaryDivision'] = TdbCmsDivision::GetTreeNodeDivision($this->sqlData['home_node_id']);
        }

        return $this->_aCache['_primaryDivision'];
    }

    /**
     * Returns the portal of a page.
     *
     * @param int $pageId
     *
     * @return TdbCmsPortal
     */
    public static function GetPagePortal($pageId)
    {
        $portal = self::getPageService()->getById($pageId)->GetPortal();

        return $portal; // Do not inline because of reference return value.
    }

    /**
     * returns the id of the homepage of this portal.
     *
     * @return string|bool
     */
    public function GetPortalPageId()
    {
        $oHomeNode = new TCMSTreeNode();
        /** @var $oHomeNode TCMSTreeNode */
        $oHomeNode->LoadWithCaching($this->sqlData['home_node_id']);

        return $oHomeNode->GetLinkedPage();
    }

    /**
     * returns false if navigation could not be found
     * else returns the navigation root node for the navigation.
     *
     * @param string $sNaviName
     *
     * @return bool|string
     */
    public function GetNavigationTreeId($sNaviName)
    {
        $query = "SELECT `tree_node`
                  FROM `cms_portal_navigation`
                 WHERE `cms_portal_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'
                   AND `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($sNaviName)."'
               ";
        $id = false;
        if ($tmp = MySqlLegacySupport::getInstance()->fetch_array(MySqlLegacySupport::getInstance()->query($query))) {
            $id = $tmp['tree_node'];
        }

        return $id;
    }

    /**
     * Returns the name of the primary domain for this portal.
     *
     * @param bool   $bStripWWW  - set this to true if you need the plain domain name without www. subdomain (like for e-mails)
     * @param string $languageId
     * @param bool   $secure     If true, the SSL name of the domain will be returned. If the SSL name is not set, the default
     *                           domain name will be returned.
     *
     * @return string $sDomainHost
     *
     * @throws InvalidPortalDomainException
     *
     * @deprecated since 6.2.0 - use chameleon_system_core.portal_domain_service::getPrimaryDomain() instead (which
     * returns a TdbCmsPortalDomains object; the domain name can then be retrieved by
     * TdbCmsPortalDomains::getSecureDomainName() or TdbCmsPortalDomains::getInsecureDomainName()).
     */
    public function GetPrimaryDomain($bStripWWW = false, $languageId = '', $secure = false)
    {
        if (empty($languageId)) {
            $languageId = null;
        }
        $oPrimaryDomain = self::getPortalDomainService()->getPrimaryDomain($this->id, $languageId);
        if ($secure) {
            $sDomainHost = $oPrimaryDomain->getSecureDomainName();
        } else {
            $sDomainHost = $oPrimaryDomain->getInsecureDomainName();
        }
        $sDomainHost = strtolower($sDomainHost);
        if ($bStripWWW && 'www.' === substr($sDomainHost, 0, 4)) {
            $sDomainHost = substr($sDomainHost, 4);
        }

        return $sDomainHost;
    }

    /**
     * returns the path to the theme directory for classes (dbobjects, modules etc.).
     *
     * @deprecated used only by pkgBlog
     *
     * @todo needs to be refactored to twig snippet chain templating
     *
     * @return string
     */
    public function GetPrivateThemePath()
    {
        static $sPath;

        if (!$sPath) {
            $sPath = '';
            if (!empty($this->sqlData['pkg_cms_theme_id'])) {
                $oWebTheme = TdbPkgCmsTheme::GetNewInstance();
                /** @var $oWebTheme TdbPkgCmsTheme */
                $oWebTheme->Load($this->sqlData['pkg_cms_theme_id']);
                if (defined('PATH_WEB_THEMES_PRIVATE') && !empty($oWebTheme->fieldDirectory)) {
                    $sPath = PATH_WEB_THEMES_PRIVATE.$oWebTheme->fieldDirectory;
                }
            }
        }

        return $sPath;
    }

    /**
     * returns the path to the theme layouts.
     *
     * @return string
     */
    public function GetThemeLayoutPath()
    {
        static $sPath;

        if (!$sPath) {
            $sPath = '';
            $sThemePath = $this->GetPrivateThemePath();
            if (!empty($sThemePath)) {
                $sPath = $sThemePath.'/layoutTemplates/';
            }
        }

        return $sPath;
    }

    /**
     * returns the path to the subdirectory inside the theme directory where module views are located.
     *
     * @return string
     */
    public function GetThemeBaseModuleViewsPath()
    {
        static $sPath;

        if (!$sPath) {
            $sPath = '';
            $sThemePath = $this->GetPrivateThemePath();
            if (!empty($sThemePath)) {
                $sPath = $sThemePath.'/modules/';
            }
        }

        return $sPath;
    }

    /**
     * returns the path to the sub directory inside the theme directory where object views are located.
     *
     * @return string
     */
    public function GetThemeObjectViewsPath()
    {
        static $sPath;

        if (!$sPath) {
            $sPath = '';
            $sThemePath = $this->GetPrivateThemePath();
            if (!empty($sThemePath)) {
                $sPath = $sThemePath.'/objectviews/';
            }
        }

        return $sPath;
    }

    /**
     * return the url to a system page with the internal name given by sSystemPageName
     * the system pages are defined through the shop config in the table cms_portal_system_page
     * Note: the links will be cached to reduce load.
     *
     * @param string $sSystemPageName
     * @param array  $aParameters      - optional parameters to add to the link
     * @param bool   $bForcePortalLink - set to true if you want to include the portal domain (http://..../) part in the link.
     * @param string $sAnchorName
     *
     * @return string
     *
     * @deprecated since 6.1.0 - use chameleon_system_core.system_page_service::getLinkToSystemPage*() instead
     */
    public function GetLinkToSystemPage($sSystemPageName, $aParameters = array(), $bForcePortalLink = false, $sAnchorName = '')
    {
        if (null === $aParameters) {
            $aParameters = array();
        }
        try {
            if ($bForcePortalLink) {
                $link = $this->getSystemPageService()->getLinkToSystemPageAbsolute($sSystemPageName, $aParameters, $this);
            } else {
                $link = $this->getSystemPageService()->getLinkToSystemPageRelative($sSystemPageName, $aParameters, $this);
            }
        } catch (RouteNotFoundException $e) {
            $link = '';
        }

        $sAnchorName = trim($sAnchorName);
        if (!empty($sAnchorName)) {
            $link .= '#'.$sAnchorName;
        }

        return $link;
    }

    /**
     * always returns relative url of the requested link
     * so the protocol and domain will be cut off if original link (from GetLinkToSystemPage) contains them.
     *
     * @param string         $sSystemPageName
     * @param array          $aParameters     - optional parameters to add to the link
     * @param string         $sAnchorName     - set name to jump to within the page
     * @param TdbCmsLanguage $language
     *
     * @return string
     *
     * @deprecated since 6.1.0 - use system_page_service::getLinkToSystemPageRelative() instead (might need to enforce the
     *                          relative URL afterwards)
     */
    public function getLinkToSystemPageRelative($sSystemPageName, $aParameters = array(), $sAnchorName = '', TdbCmsLanguage $language = null)
    {
        if (null === $aParameters) {
            $aParameters = array();
        }
        try {
            $link = $this->getSystemPageService()->getLinkToSystemPageRelative($sSystemPageName, $aParameters, $this, $language);
            $link = $this->getUrlUtil()->getRelativeUrl($link);
        } catch (RouteNotFoundException $e) {
            $link = '';
        }

        $sAnchorName = trim($sAnchorName);
        if (!empty($sAnchorName)) {
            $link .= '#'.$sAnchorName;
        }

        return $link;
    }

    /**
     * Returns the page ID of the first found page connected to a system page tree node.
     *
     * @param string              $systemPageName
     * @param TdbCmsLanguage|null $language
     *
     * @return string|bool|null - returns null or false if no page was found
     */
    public function GetSystemPageId($systemPageName, TdbCmsLanguage $language = null)
    {
        $systemPage = $this->getSystemPageService()->getSystemPage($systemPageName, $this, $language);
        if (null === $systemPage) {
            return null;
        }

        $tree = self::getTreeService()->getById($systemPage->fieldCmsTreeId, null === $language ? null : $language->id);
        if (null === $tree) {
            return null;
        }

        return $tree->GetLinkedPage();
    }

    /**
     * return the link to a system page with the internal name given by sSystemPageName
     * the system pages are defined through the shop config in the table cms_portal_system_page
     * Note: the links will be cached to reduce load.
     *
     * @param string $sLinkText        - the text to display in the link - no OutHTML will be called - so make sure to escape the incoming text
     * @param string $sSystemPageName
     * @param array  $aParameters      - optional parameters to add to the link
     * @param bool   $bForcePortalLink - set to true if you want to include the portal domain (http://..../) part in the link.
     * @param int    $width
     * @param int    $height
     * @param bool   $bGetOnlyJSCode
     * @param string $sCSSClass        - additional css class to add to the link
     * @param string $sAnchorName      - set name to jump to within the page*
     *
     * @return string
     */
    public function GetLinkToSystemPageAsPopUp($sLinkText, $sSystemPageName, $aParameters = null, $bForcePortalLink = false, $width = 450, $height = 600, $bGetOnlyJSCode = false, $sCSSClass = '', $sAnchorName = '')
    {
        $sLink = $this->GetLinkToSystemPage($sSystemPageName, $aParameters, $bForcePortalLink, $sAnchorName);
        $sOnClick = "window.open('{$sLink}','popupwindow','scrollbars=yes,resizable=yes,height=".TGlobal::OutHTML($height).',width='.TGlobal::OutHTML($width).",location=no,menubar=no,status=no,toolbar=no');return false;";
        if ($bGetOnlyJSCode) {
            $sFullLink = $sOnClick;
        } else {
            $sCSSDefaultClass = 'popup';
            if ('' != $sCSSClass) {
                $sCSSDefaultClass .= ' '.$sCSSClass;
            }
            $sFullLink = "<a href=\"{$sLink}\" target=\"_blank\" onclick=\"{$sOnClick}\" class=\"{$sCSSDefaultClass}\">{$sLinkText}</a>\n";
        }

        return $sFullLink;
    }

    /**
     * return the node for the system page with the name sSystemPageName. see GetLinkToSystemPage for details.
     *
     * @param string         $sSystemPageName
     * @param TdbCmsLanguage $language
     *
     * @return string|null
     *
     * @deprecated since 6.1.0 - use system_page_service::getSystemPage()->fieldCmsTreeId instead
     */
    public function GetSystemPageNodeId($sSystemPageName, TdbCmsLanguage $language = null)
    {
        $systemPage = $this->getSystemPageService()->getSystemPage($sSystemPageName, $this, $language);
        if (null === $systemPage) {
            return null;
        }

        return $systemPage->fieldCmsTreeId;
    }

    /**
     * return an array with system page links.
     *
     * @param bool $bForcePortalLink
     *
     * @return array
     */
    protected function GetSystemPageLinkList($bForcePortalLink)
    {
        static $aSystemPages = array();
        static $aSystemPagesWithPortal = array();

        $languageIdentifier = '-';
        $language = self::getLanguageService()->getActiveLanguage();
        if (null !== $language) {
            $languageIdentifier = $language->fieldIso6391;
        }
        if ((!isset($aSystemPages[$languageIdentifier]) && !$bForcePortalLink) || (!isset($aSystemPagesWithPortal[$languageIdentifier]) && $bForcePortalLink)) {
            $cache = $this->getCache();
            $aCacheKeyParams = array('class' => __CLASS__, 'method' => 'GetSystemPageLinkList', 'name' => 'systemportalpagelinks', 'forceportal' => $bForcePortalLink, 'language' => $languageIdentifier);
            $sKey = $cache->getKey($aCacheKeyParams);
            $aTmpList = $cache->get($sKey);
            if (null === $aTmpList) {
                $aTmpList = array();
                $oPages = $this->GetProperties('cms_portal_system_page', 'TdbCmsPortalSystemPage');
                $treeService = self::getTreeService();
                while ($oSystemPage = $oPages->Next()) {
                    /** @var $oSystemPage TdbCmsPortalSystemPage */
                    $oNode = $treeService->getById($oSystemPage->fieldCmsTreeId);
                    if (null !== $oNode) {
                        try {
                            if ($bForcePortalLink) {
                                $sLink = $treeService->getLinkToPageForTreeAbsolute($oNode, array(), $language);
                            } else {
                                $sLink = $treeService->getLinkToPageForTreeRelative($oNode, array(), $language);
                            }
                            $aTmpList[$oSystemPage->fieldNameInternal] = array('link' => $sLink, 'nodeId' => $oNode->id);
                        } catch (RouteNotFoundException $e) {
                            $this->getLogger()->warning(
                                sprintf('Error while generating link for system page %s: %s', $oSystemPage->fieldNameInternal, $e->getMessage())
                            );
                        }
                    }
                }
                $aCacheClearKeys = array(array('table' => 'cms_portal_system_page', 'id' => ''), array('table' => 'cms_tree', 'id' => ''));
                $cache->set($sKey, $aTmpList, $aCacheClearKeys);
            }
            if ($bForcePortalLink) {
                $aSystemPagesWithPortal[$languageIdentifier] = $aTmpList;
            } else {
                $aSystemPages[$languageIdentifier] = $aTmpList;
            }
        }
        if ($bForcePortalLink) {
            return $aSystemPagesWithPortal[$languageIdentifier];
        } else {
            return $aSystemPages[$languageIdentifier];
        }
    }

    /**
     * return list of system page names.
     *
     * @return array
     */
    public function GetSystemPageNames()
    {
        $aSystemPages = $this->GetSystemPageLinkList(false);

        return array_keys($aSystemPages);
    }

    /**
     * returns title of the portal.
     *
     * @return string
     */
    public function GetTitle()
    {
        $sContent = '';
        if (false !== $this->sqlData) {
            $sContent = $this->GetFromInternalCache('portalTitle');
            if (is_null($sContent)) {
                $sContent = $this->sqlData['title'];
                // fallback to name field
                if (empty($sContent)) {
                    $sContent = $this->GetName();
                }

                $this->SetInternalCache('portalTitle', $sContent);
            }
        }

        return $sContent;
    }

    /**
     * returns the URL to a custom favicon if available or the default favicon url.
     *
     * @return string|null
     */
    public function GetFaviconUrl()
    {
        return (isset($this->sqlData['favicon_url']) && '' !== $this->sqlData['favicon_url']) ? $this->sqlData['favicon_url'] : null;
    }

    /**
     * return an array holding the languages supported in the front end for a field based translation.
     *
     * @return array
     */
    public function GetFieldBasedTranslationLanguageArray()
    {
        $bActivateAllPortalLanguages = $this->GetActivateAllPortalLanguages();
        $sCachePostFix = $bActivateAllPortalLanguages ? 'active' : 'all';
        $aLanguages = $this->GetFromInternalCache('aFieldBasedTranslationLanguageArray'.$sCachePostFix);
        if (is_null($aLanguages)) {
            $aLanguages = array();
            $oLanguages = $this->GetMLT('cms_language_mlt', 'TdbCmsLanguage', '', 'CMSDataObjects', 'Core');
            while ($oLang = $oLanguages->Next()) {
                if ($oLang->id != $this->sqlData['cms_language_id'] && (true === $bActivateAllPortalLanguages || true === $oLang->fieldActiveForFrontEnd)) {
                    $aLanguages[$oLang->fieldIso6391] = $oLang->fieldName;
                }
            }
            $this->SetInternalCache('aFieldBasedTranslationLanguageArray'.$sCachePostFix, $aLanguages);
        }

        return $aLanguages;
    }

    /**
     * Return a suffix for files that should have a different name for each portal.
     *
     * @return string
     */
    public function getFileSuffix()
    {
        return '_'.$this->sqlData['cmsident'];
    }

    /**
     * Get only for frontend activated languages selected in portal.
     *
     * @param string $sOrderBy
     *
     * @return TdbCmsLanguageList
     */
    public function GetActiveLanguages($sOrderBy = '')
    {
        if (true === $this->GetActivateAllPortalLanguages()) {
            $oLanguageList = $this->GetFieldCmsLanguageList($sOrderBy);
        } else {
            $oLanguageList = $this->GetFromInternalCache('oActiveLanguageList'.$sOrderBy);
            if (is_null($oLanguageList)) {
                $sQuery = "SELECT * FROM `cms_portal_cms_language_mlt`
                            INNER JOIN `cms_language` ON `cms_language`.`id` = `cms_portal_cms_language_mlt`.`target_id`
                            WHERE `cms_portal_cms_language_mlt`.`source_id` = '".$this->id."'
                            AND `cms_language`.`active_for_front_end` = '1' ";
                if ('' != $sOrderBy) {
                    $sQuery .= ' ORDER BY '.$sOrderBy;
                }
                $oLanguageList = TdbCmsLanguageList::GetList($sQuery);
                $this->SetInternalCache('oActiveLanguageList'.$sOrderBy, $oLanguageList);
            }
        }

        return $oLanguageList;
    }

    /**
     * Checks if portal has inactive languages configured.
     *
     * @return bool
     */
    public function hasInactiveLanguages()
    {
        $bHasInactiveLanguages = false;
        $oInactiveLanguages = $this->getInactiveLanguages();
        if ($oInactiveLanguages->Length() > 0) {
            $bHasInactiveLanguages = true;
        }

        return $bHasInactiveLanguages;
    }

    /**
     * Get all inactive languages configured for portal.
     *
     * @return TdbCmsLanguageList
     */
    public function getInactiveLanguages()
    {
        $oLanguageList = $this->GetFromInternalCache('oIanctiveLanguageList');
        if (is_null($oLanguageList)) {
            $sQuery = "SELECT * FROM `cms_portal_cms_language_mlt`
                            INNER JOIN `cms_language` ON `cms_language`.`id` = `cms_portal_cms_language_mlt`.`target_id`
                            WHERE `cms_portal_cms_language_mlt`.`source_id` = '".$this->id."'
                            AND `cms_language`.`active_for_front_end` = '0' ";
            $oLanguageList = TdbCmsLanguageList::GetList($sQuery);
            $this->SetInternalCache('oIanctiveLanguageList', $oLanguageList);
        }

        return $oLanguageList;
    }

    /**
     * Set session variable to de and activate front end languages.
     *
     * @param bool $bValue
     */
    public function SetActivateAllPortalLanguages($bValue = true)
    {
        $_SESSION['activateAllPortalLanguages'] = $bValue;
    }

    /**
     * Get session variable to de and activate front end languages.
     *
     * @param bool $bValue
     */
    public function GetActivateAllPortalLanguages()
    {
        $bState = false;
        if (isset($_SESSION['activateAllPortalLanguages'])) {
            $bState = $_SESSION['activateAllPortalLanguages'];
        }

        return $bState;
    }

    /**
     * @return SystemPageServiceInterface
     */
    private function getSystemPageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.system_page_service');
    }

    /**
     * @return CacheInterface
     */
    private function getCache()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_cms_cache.cache');
    }

    /**
     * @return UrlUtil
     */
    private function getUrlUtil()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.url');
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private static function getPortalDomainService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }

    private function getLogger(): LoggerInterface
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('logger');
    }
}

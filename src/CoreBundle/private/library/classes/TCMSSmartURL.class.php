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
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Service\TreeServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * maps urls to pagedefs, and pagedefs to pages.
/**/
class TCMSSmartURL
{
    private static $additionalCacheKeys = null;

    /**
     * If inactive language was set via language prefix. Show page only if inactive
     * languages was activated in cms.
     *
     * You can activate inactive languages in cms -> portals/websites
     *
     * @param Request $request
     */
    protected static function HandleTemporaryActivatedLanguages(Request $request)
    {
        $oPortal = self::getPortalDomainService()->getActivePortal();
        if (!is_object($oPortal) || $oPortal->GetActivateAllPortalLanguages()) {
            return;
        }
        $oLanguage = self::getLanguageService()->getActiveLanguage();
        if (false === $oLanguage->fieldActiveForFrontEnd) {
            $oConfig = TdbCmsConfig::GetInstance();
            $oBaseLanguage = $oConfig->GetFieldTranslationBaseLanguage();

            $url = self::getPageService()->getLinkToPortalHomePageAbsolute(array(), $oPortal, $oBaseLanguage);
            self::getRedirect()->redirect($url);
        }
    }

    /**
     * @param array $aAdditionalCacheKeys
     */
    public static function setAdditionalCacheKeys($aAdditionalCacheKeys = array())
    {
        self::$additionalCacheKeys = $aAdditionalCacheKeys;
    }

    /**
     * @param array $aAdditionalCacheKeys
     *                                    used to be called by every index.php - since we can not change every index.php for every customer, we must keep it
     *
     * @deprecated
     */
    public static function SetRealPagdef($aAdditionalCacheKeys = array())
    {
        self::setAdditionalCacheKeys($aAdditionalCacheKeys);
    }

    /**
     * converts the user request to the real pagedef behind that call. function
     * also protects pages from cross portal calls.
     *
     * @param Request $request
     *
     * @return string
     *
     * @throws NotFoundHttpException
     */
    public static function run(Request $request)
    {
        $oURLData = &TCMSSmartURLData::GetActive();
        self::HandleTemporaryActivatedLanguages($request);
        self::RedirectOnInvalidExternalArguments();
        $oGlobal = TGlobal::instance();

        $aNonSeoParameter = array_keys($oGlobal->GetRawUserData());
        $pagedef = false;
        // if we have a pagedef in post, then we are done right away.
        if ($oGlobal->UserDataExists('pagedef')) {
            $pagedef = $oGlobal->GetUserData('pagedef');
        } else {
            $aCustomURLParameters = array();
            // need to check for pagedef again since TCMSSmartURLData may have set it
            if ($oGlobal->UserDataExists('pagedef')) {
                $pagedef = $oGlobal->GetUserData('pagedef');
            } else {
                $pagedef = self::RunCustomHandlers($request, $aCustomURLParameters);
            }

            if (false === $pagedef) {
                $oURLData->bPagedefFound = false;

                throw new NotFoundHttpException();
            }

            $oGlobal->SetRewriteParameter($aCustomURLParameters);
            foreach ($aCustomURLParameters as $key => $value) {
                $request->query->set($key, $value);
            }

            $request->query->set('pagedef', $pagedef);
        }

        $aAllParameter = array_keys($oGlobal->GetRawUserData());
        $aSeoParameterList = array_diff($aAllParameter, $aNonSeoParameter);
        $oURLData->setSeoURLParameters($aSeoParameterList);

        $oURLData->SetObjectInitializationCompleted(true);

        return $pagedef;
    }

    /**
     * returns the root page for the portal. false if no root page was found.
     *
     * @param int $iPortalId
     *
     * @return int
     */
    public static function GetPortalRootPagedef($iPortalId)
    {
        $pagedef = false;
        $query = "SELECT *
                  FROM `cms_portal`
                 WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($iPortalId)."'
               ";
        if ($trow = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
            $pagedef = TCMSSmartURLHandler::GetNodePage($trow['home_node_id']);
        }

        return $pagedef;
    }

    /**
     * should return the pageid of the document not found page for the portal.
     * return false if no "not-found" page exists.
     *
     * @param int $iPortalId
     *
     * @return int
     *
     * @deprecated since 6.2.0 - throw a NotFoundHttpException to display the not-found page.
     */
    public static function GetNotFoundPagedef($iPortalId)
    {
        $iNotFoundPageId = false;
        $oURLData = &TCMSSmartURLData::GetActive();
        $oPortal = $oURLData->GetPortal();
        if (array_key_exists('page_not_found_node', $oPortal->sqlData)) {
            $oNotFoundNode = self::getTreeService()->getById($oPortal->sqlData['page_not_found_node']);
            if (null !== $oNotFoundNode) {
                $iNotFoundPageId = $oNotFoundNode->GetLinkedPage();
                if (empty($iNotFoundPageId)) {
                    $iNotFoundPageId = false;
                }
            }
        }

        return $iNotFoundPageId;
    }

    /**
     * should return the pageid of the not found page for the portal.
     * return false if no "document-not-found" page exists.
     *
     * @param int $iPortalId
     *
     * @return int
     *
     * @deprecated since 6.2.0 - throw a NotFoundHttpException to display the not-found page.
     */
    public static function GetDocumentNotFoundPagedef($iPortalId)
    {
        $iNotFoundPageId = false;
        $oURLData = &TCMSSmartURLData::GetActive();
        $oPortal = $oURLData->GetPortal();
        if (array_key_exists('document_not_found_node', $oPortal->sqlData)) {
            $oNotFoundNode = self::getTreeService()->getById($oPortal->sqlData['document_not_found_node']);
            if (null !== $oNotFoundNode) {
                $iNotFoundPageId = $oNotFoundNode->GetLinkedPage();
                if (empty($iNotFoundPageId)) {
                    $iNotFoundPageId = false;
                }
            }
        }

        return $iNotFoundPageId;
    }

    /**
     * execute all custom handlers untill a pagedef is found. if none is found, return false
     * the custom handler which found a pagedef can return custom cache triggers.
     *
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param array                                    $aCustomURLParameters
     *
     * @return int
     */
    protected static function RunCustomHandlers(Request $request, &$aCustomURLParameters)
    {
        $pagedef = false;
        $oPortal = self::getPortalDomainService()->getActivePortal();
        if (is_null($oPortal)) {
            $query = 'SELECT * FROM `cms_smart_url_handler` ORDER BY `position`';
        } else {
            $query = "SELECT `cms_smart_url_handler`.* FROM `cms_smart_url_handler`
                         LEFT JOIN `cms_smart_url_handler_cms_portal_mlt` ON `cms_smart_url_handler_cms_portal_mlt`.`source_id` = `cms_smart_url_handler`.`id`
                         LEFT JOIN `cms_portal` ON `cms_portal`.`id` = `cms_smart_url_handler_cms_portal_mlt`.`target_id`
                              WHERE (`cms_portal`.`id` = '".$oPortal->id."' OR `cms_portal`.`id` IS NULL)
                              AND `cms_smart_url_handler`.`active` = '1'
                           ORDER BY `cms_smart_url_handler`.`position`";
        }
        $thandler = MySqlLegacySupport::getInstance()->query($query);
        while (false === $pagedef && ($aHandler = MySqlLegacySupport::getInstance()->fetch_assoc($thandler))) {
            $sClassName = $aHandler['name'];

            /** @var TCMSSmartURLHandler $oNewHandler */
            $oNewHandler = new $sClassName();
            $oNewHandler->setRequest($request);
            $pagedef = $oNewHandler->GetPageDef();
            if (false !== $pagedef) {
                foreach ($oNewHandler->aCustomURLParameters as $key => $value) {
                    $aCustomURLParameters[$key] = $value;
                }
            }
        }

        return $pagedef;
    }

    /**
     * converts a page title or something else to a valid URL.
     *
     * @param string $sRealName
     *
     * @return string
     *
     * @deprecated since at least 2009 - use chameleon_system_core.util.url_normalization instead
     */
    public static function RealNameToURLName($sRealName)
    {
        return self::getUrlNormalizationUtil()->normalizeUrl($sRealName);
    }

    /**
     * returns an url path to the tree node iTreeNode for portal iPortalID
     * NOTE: we ignore the first entry in the path since it is the last stop
     *       node (so the navigation name, or the division, or the portal.
     *
     * @param int    $iTreeNode
     * @param int    $sPortalId
     * @param int    $sPageId          (optional, if we have no active page)
     * @param string $sLanguageIsoName
     *
     * @return string
     *
     * @deprecated since 6.1.0. Use chameleon_system_core.page_service::getLinkToPageRelative() or chameleon_system_core.tree_service::getLinkToPageForTreeRelative() instead.
     */
    public static function GetURL($iTreeNode, $sPortalId = null, $sPageId = null, $sLanguageIsoName = '')
    {
        if (null === $sPortalId) {
            $portal = null;
        } else {
            $portal = TdbCmsPortal::GetNewInstance($sPortalId);
        }
        if (empty($sLanguageIsoName)) {
            $language = null;
        } else {
            $language = self::getLanguageService()->getLanguageFromIsoCode($sLanguageIsoName);
        }

        try {
            if (null === $sPageId) {
                $treeService = self::getTreeService();
                $tree = $treeService->getById($iTreeNode);
                if (null === $tree) {
                    return '/';
                }

                return $treeService->getLinkToPageForTreeRelative($tree, array(), $language);
            }

            return self::getPageService()->getLinkToPageRelative($sPageId, array(), $language);
        } catch (RouteNotFoundException $e) {
            if (null !== $portal) {
                return $portal->GetFieldPageNotFoundNodePageURL();
            }

            return '/';
        }
    }

    /**
     * returns a URL path to the tree node oRootNode for portal oPortal and oPage.
     *
     * @todo we should add logging here to fetch all old links so CMS users can see missing links
     *
     * @param TdbCmsTree    $oRootNode        - the node connected to the page
     * @param TdbCmsPortal  $oPortal
     * @param TdbCmsTplPage $oPage
     * @param bool          $bForcePortal
     * @param string        $sLanguageIsoName
     *
     * @return string
     *
     * @deprecated since 6.1.0. Use chameleon_system_core.page_service::getLinkToPage*() instead.
     */
    public function GetURLFast($oRootNode, &$oPortal, &$oPage, $bForcePortal = false, $sLanguageIsoName = '')
    {
        if (!$oRootNode || !$oPortal || !$oPage) {
            return '#';
        }

        if (empty($sLanguageIsoName)) {
            $targetLanguage = null;
        } else {
            $targetLanguage = self::getLanguageService()->getLanguageFromIsoCode($sLanguageIsoName);
        }

        $pageService = self::getPageService();
        if ($bForcePortal) {
            return $pageService->getLinkToPageObjectAbsolute($oPage, array(), $targetLanguage);
        } else {
            return $pageService->getLinkToPageObjectRelative($oPage, array(), $targetLanguage);
        }
    }

    /**
     * returns true if the target page is on a different domain then the current domain (or on a different protocol) for a target language.
     *
     * @param TdbCmsTplPage  $targetPage
     * @param TdbCmsPortal   $portal
     * @param TdbCmsLanguage $targetLanguage
     * @param bool           $currentRequestIsSecure
     *
     * @return bool
     *
     * @deprecated since 6.1.0. Use chameleon_system_core.page_service or chameleon_system_core.tree_service to generate
     *                          URLs, which also take care of adding the domain.
     */
    private function requiresDomainPrefix(TdbCmsTplPage $targetPage, TdbCmsPortal $portal = null, TdbCmsLanguage $targetLanguage = null, $currentRequestIsSecure = false)
    {
        $targetUsesHttps = $currentRequestIsSecure || $targetPage->fieldUsessl;
        if ($currentRequestIsSecure !== $targetUsesHttps) {
            return true;
        }
        $portalDomainService = self::getPortalDomainService();

        if (null === $portal) {
            $portal = $portalDomainService->getActivePortal();
        }
        $activeLanguageId = self::getLanguageService()->getActiveLanguageId();
        if ((null !== $portal && $portal->id !== $targetPage->fieldCmsPortalId) || (null !== $targetLanguage && $targetLanguage->id !== $activeLanguageId)) {
            $primaryDomain = $portalDomainService->getPrimaryDomain($targetPage->fieldCmsPortalId, $targetLanguage->id);
            $primaryDomainString = $primaryDomain->fieldName;
            if ($targetUsesHttps && '' !== $primaryDomain->fieldSslname) {
                $primaryDomainString = $primaryDomain->fieldSslname;
            }

            $sourceDomain = $portalDomainService->getActiveDomain();
            $primarySourceDomainString = $sourceDomain->fieldName;
            if ($targetUsesHttps && '' !== $sourceDomain->fieldSslname) {
                $primarySourceDomainString = $sourceDomain->fieldSslname;
            }

            if (mb_strtolower($primaryDomainString) !== mb_strtolower($primarySourceDomainString)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param bool           $bForcePortal
     * @param TdbCmsTplPage  $oPage
     * @param TdbCmsPortal   $portal
     * @param TdbCmsLanguage $targetLanguage
     *
     * @return string
     *
     * @deprecated since 6.1.0. Use chameleon_system_core.page_service or chameleon_system_core.tree_service to generate
     *                          URLs, which also take care of adding the domain.
     */
    public function GetPathDomainPrefix($bForcePortal, $oPage, TdbCmsPortal $portal = null, TdbCmsLanguage $targetLanguage = null)
    {
        $request = $this->getRequestStack()->getCurrentRequest();
        if (false === $bForcePortal && ((null === $request) || (false === $this->requiresDomainPrefix($oPage, $portal, $targetLanguage, $request->isSecure())))) {
            return '';
        }

        $targetUsesHttps = $oPage->fieldUsessl || (null !== $request && $request->isSecure());

        $portalDomainService = self::getPortalDomainService();
        $primaryDomain = $portalDomainService->getPrimaryDomain($oPage->GetPortal()->id, (null !== $targetLanguage) ? $targetLanguage->id : null);
        $protocol = 'http://';
        $primaryDomainString = $primaryDomain->fieldName;
        if (true === $targetUsesHttps) {
            $protocol = 'https://';
            if ('' !== $primaryDomain->fieldSslname) {
                $primaryDomainString = $primaryDomain->fieldSslname;
            }
        }

        return $protocol.$primaryDomainString;
    }

    /**
     * @param TdbCmsPortal   $oPortal
     * @param TdbCmsLanguage $targetLanguage
     *
     * @return string
     *
     * @deprecated since 6.0.5. Use chameleon_system_core.util.url_prefix_generator::getLanguagePrefix instead
     */
    public function GetLanguagePrefixForPortal(TdbCmsPortal $oPortal, TdbCmsLanguage $targetLanguage)
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.url_prefix_generator')->getLanguagePrefix($oPortal, $targetLanguage);
    }

    /**
     * @param string         $sPageMainTreeId
     * @param TdbCmsPortal   $oPortal
     * @param TdbCmsLanguage $targetLanguage
     * @param string         $sPathSeparator
     *
     * @return string
     *
     * @deprecated since 6.1.0. Use chameleon_system_core.tree_service::getLinkToPageForTree*() instead.
     */
    protected function GetPagePathForTreeId($sPageMainTreeId, $oPortal, $targetLanguage, $sPathSeparator = '/')
    {
        $sPath = '';

        $sFieldLanguageIsoName = TGlobal::GetLanguagePrefix($targetLanguage->id);
        if ('' !== $sFieldLanguageIsoName) {
            $sFieldLanguageIsoName = '__'.$sFieldLanguageIsoName;
        }

        $oPathNode = self::getTreeService()->getById($sPageMainTreeId, TdbCmsConfig::GetInstance()->fieldTranslationBaseLanguageId);
        if (null === $oPathNode) {
            return '';
        }
        // always force tree node to base language to prevent field overloading; we load corresponding fields manually
        $aStopNodes = TCMSPortal::GetStopNodes($oPortal->id);
        $aPath = $oPathNode->GetPath($aStopNodes);

        for ($i = 2; $i < count($aPath); ++$i) {
            $sPath .= $sPathSeparator;
            if (empty($aPath[$i]->sqlData['urlname'.$sFieldLanguageIsoName])) {
                if ($oPortal->fieldShowNotTanslated) {
                    $sPath .= $aPath[$i]->sqlData['urlname'];
                } else {
                    $sPath = '';
                }
            } else {
                $sPath .= $aPath[$i]->sqlData['urlname'.$sFieldLanguageIsoName];
            }
        }

        if (!empty($sPath)) {
            if ($oPortal && !empty($oPortal->sqlData['use_slash_in_seo_urls'])) {
                $sPath .= $sPathSeparator;
            } else {
                $sPath .= '.html';
            }
        } else {
            $sPath .= $sPathSeparator;
        }

        return $sPath;
    }

    /**
     * returns the SEO url as names with " - " as spacer (for <title> tag.
     *
     * @param int $iTreeNode
     * @param int $iPortalID
     *
     * @return string
     */
    public static function GetURLName($iTreeNode, $iPortalID = null)
    {
        $stopNodes = TCMSPortal::GetStopNodes($iPortalID);
        $oRootNode = new TCMSTreeNode();
        $oRootNode->Load($iTreeNode);
        $aPath = $oRootNode->GetPath($stopNodes);
        $name = '';
        for ($i = 2; $i < count($aPath); ++$i) {
            if ($i > 2) {
                $name .= ' - ';
            }
            $name .= $aPath[$i]->sqlData['name'];
        }

        return $name;
    }

    /**
     * returns the relative URL using the direct pagedef link without SEO conversion.
     *
     * @param int $iTreeNode
     * @param int $pageID
     *
     * @return string
     *
     * @deprecated since 6.2.0 - no longer used.
     */
    public static function GetDirectURL($iTreeNode, $pageID)
    {
        return '/'.PATH_CUSTOMER_FRAMEWORK_CONTROLLER.'?pagedef='.urlencode($pageID).'&__treeNode='.urlencode($iTreeNode);
    }

    /**
     * checks for invalid arguments and redirects to the current page with all parameters except
     * the invalid argument list - this is used to filter external requests for search engines (prevent dublicate content).
     */
    protected static function RedirectOnInvalidExternalArguments()
    {
        if (defined('INVALID_GET_PARAMS') && INVALID_GET_PARAMS != '') {
            $bParamFound = false;
            $oURLData = &TCMSSmartURLData::GetActive();
            $aAllParamNames = array_keys($oURLData->aParameters);
            $aInvalidGetParams = explode('|', INVALID_GET_PARAMS);
            $aFinalParameters = array();
            foreach ($aAllParamNames as $key => $value) {
                if (in_array(strtolower($value), $aInvalidGetParams)) {
                    $bParamFound = true;
                } else {
                    $aFinalParameters[$value] = $oURLData->aParameters[$value];
                }
            }

            if ($bParamFound) {
                //$oActivePage = TCMSActivePage::GetInstance();
                //$iPageId = $oActivePage->id;
                $sUrlWithoutParams = substr($oURLData->sOriginalURL, 0, strpos($oURLData->sOriginalURL, '?'));

                if (count($aFinalParameters) > 0) {
                    $sUrlWithoutParams .= '?'.str_replace('&amp;', '&', TTools::GetArrayAsURL($aFinalParameters));
                }

                $newURL = REQUEST_PROTOCOL.'://'.$oURLData->sOriginalDomainName.$sUrlWithoutParams;
                self::getRedirect()->redirect($newURL, Response::HTTP_MOVED_PERMANENTLY);
            }
        }
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private static function getPortalDomainService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }

    /**
     * @return LanguageServiceInterface
     */
    private static function getLanguageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.language_service');
    }

    /**
     * @return TreeServiceInterface
     */
    private static function getTreeService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.tree_service');
    }

    /**
     * @return PageServiceInterface
     */
    private static function getPageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.page_service');
    }

    /**
     * @return ICmsCoreRedirect
     */
    private static function getRedirect()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.redirect');
    }

    /**
     * @return RequestStack
     */
    private function getRequestStack()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack');
    }

    /**
     * @return UrlNormalizationUtil
     */
    private static function getUrlNormalizationUtil()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.url_normalization');
    }
}

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
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * include page header data like title, and meta tags.
 */
class MTPageMetaCoreEndPoint extends TUserModelBase
{
    /**
     * you can add additional breadcrumb elements for the title tag in your extension.
     *
     * @var array
     */
    public $aAdditionalBreadcrumbNodes = [];

    /**
     * array holding all media file ids that may trigger a cache refresh.
     *
     * @var array
     */
    protected $aCacheMediaIDs = [];

    /**
     * object of the background image
     * is loaded in GetHTMLHeadIncludes.
     *
     * @var TCMSImage
     */
    protected $oBackgroundImage;

    /**
     *  set to true if you want the title in reversed order for SEO reasons
     *  e.g. reversed breadcrumb - page title - portal name.
     *
     * @var bool
     */
    protected $bOrderTitleReversed = false;

    /**
     * set your custom separator for the title tag here.
     *
     * @var string
     */
    protected $sTitleSeparator = ' - ';

    /**
     * {@inheritdoc}
     */
    public function Init()
    {
        parent::Init();

        if ('logo' == $this->aModuleConfig['view']) {
            $this->SetHTMLDivWrappingStatus(true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function Execute()
    {
        parent::Execute();

        $this->data['sTitle'] = $this->_GetTitle();
        $this->data['sURL'] = $this->_GetActivePageURL();
        $this->data['sHost'] = $this->_GetHost();
        $this->data['oPortalLogo'] = $this->_GetPortalLogo();
        $this->data['aMetaData'] = $this->_GetMetaData();
        $this->data['sCustomHeaderData'] = $this->_GetCustomHeaderData();
        $this->data['sHomeURL'] = $this->GetHomeURL();
        $this->data['sPortalName'] = $this->_GetPortalName();
        $this->data['sCustomHeaderData'] .= $this->GetFaviconMeta();
        $this->data['aMetaData'] = $this->TrimMetaKeywords($this->data['aMetaData']);
        $this->data['aLangList'] = $this->GetLanguageList();
        $this->data['sCanonical'] = $this->GetMetaCanonical();
        $this->data['language-alternatives'] = $this->getLanguageAlternatives();

        return $this->data;
    }

    /**
     * Get all languages.
     *
     * @return array
     */
    protected function GetLanguageList()
    {
        $aLanguageList = [];
        $activePortal = $this->getPortalDomainService()->getActivePortal();
        if (null === $activePortal) {
            return $aLanguageList;
        }

        $aLanguageList = $activePortal->GetFieldBasedTranslationLanguageArray();
        $oPortalLanguage = $activePortal->GetFieldCmsLanguage();
        if (null !== $oPortalLanguage) {
            $aLanguageList[$oPortalLanguage->fieldIso6391] = $oPortalLanguage->fieldName;
        }

        return $aLanguageList;
    }

    /**
     * Trim Meta Keywords if more then 15 entries.
     *
     * @param array $aMetaData
     *
     * @return array
     */
    protected function TrimMetaKeywords($aMetaData)
    {
        if (array_key_exists('name', $aMetaData) && array_key_exists('keywords', $aMetaData['name'])) {
            if (strlen($aMetaData['name']['keywords']) > 0) {
                $aMetaKeywords = explode(',', $aMetaData['name']['keywords']);
                if (count($aMetaKeywords) > 15) {
                    $sNewMetaKeywords = '';
                    for ($i = 0; $i < 15; ++$i) {
                        if (0 == $i) {
                            $sNewMetaKeywords .= $aMetaKeywords[$i];
                        } else {
                            $sNewMetaKeywords .= ','.$aMetaKeywords[$i];
                        }
                    }
                    $aMetaData['name']['keywords'] = $sNewMetaKeywords;
                }
            }
        }

        return $aMetaData;
    }

    /**
     * returns meta tags to include a favicon based on portal configuration.
     *
     * @return string
     */
    protected function GetFaviconMeta()
    {
        $sIconURL = null;
        $activePortal = $this->getPortalDomainService()->getActivePortal();
        if (null !== $activePortal) {
            $sIconURL = $activePortal->GetFaviconUrl();
        }

        $sReturnMeta = '';
        if (null !== $sIconURL) {
            $sReturnMeta = '<link rel="shortcut icon" href="'.$sIconURL."\" type=\"image/x-icon\" />\n";
        }

        return $sReturnMeta;
    }

    /**
     * returns a stylesheet block that sets a background image
     * you need to set background-repeat or whatever configuration in your custom CSS.
     *
     * @return string
     */
    protected function GetBackgroundImage()
    {
        $sBackgroundStyle = '';

        $activePage = $this->getActivePageService()->getActivePage();
        $oImage = $activePage->GetImage(0, 'background_image');
        if (is_null($oImage) || empty($oImage->id)) {
            $pageDivision = $activePage->getDivision();
            if ($pageDivision) {
                $oImage = $pageDivision->GetImage(0, 'background_image');
            }
        }
        if (is_null($oImage) || empty($oImage->id)) {
            $activePortal = $this->getPortalDomainService()->getActivePortal();
            if ($activePortal) {
                $oImage = $activePortal->GetImage(0, 'background_image');
            }
        }

        if (!is_null($oImage) && !empty($oImage->id)) {
            $this->aCacheMediaIDs[] = $oImage->id;
            $sBackgroundImageURL = $oImage->GetFullURL();
            if (!empty($sBackgroundImageURL)) {
                $this->oBackgroundImage = $oImage;
                $sBackgroundStyle = '<style type="text/css">
          * body {
            background-image: url('.TGlobal::OutHTML($sBackgroundImageURL).');
          }
          </style>';
            }
        }

        return $sBackgroundStyle;
    }

    /**
     * {@inheritdoc}
     */
    public function GetHtmlHeadIncludes()
    {
        $aHeaderData = parent::GetHtmlHeadIncludes();
        $sBackgroundStyle = $this->GetBackgroundImage();
        if (!empty($sBackgroundStyle)) {
            $aHeaderData[] = $sBackgroundStyle;
        }

        return $aHeaderData;
    }

    /**
     * returns the current hostname.
     *
     * @return string
     */
    protected function _GetHost()
    {
        return $_SERVER['HTTP_HOST'];
    }

    /**
     * checks if referrer exists in session and if not fetches and parses it
     * isn`t called by default
     * override Init() method to load it manually.
     *
     * @return bool
     */
    protected function SaveReferrerInSession()
    {
        $returnVal = false;
        if (empty($_SESSION['userReferrer'])) {
            $referrer = getenv('HTTP_REFERER');
            if (!empty($referrer)) {
                $aReferrerData = TTools::referrer_analyzer($referrer);
                if ($aReferrerData && is_array($aReferrerData)) {
                    $_SESSION['aUserReferrer'] = $aReferrerData;
                    $returnVal = true;
                }
            }
        }

        return $returnVal;
    }

    /**
     * returns the name of the current portal.
     *
     * @return string
     */
    protected function _GetPortalName()
    {
        $activePortal = $this->getPortalDomainService()->getActivePortal();
        if (null === $activePortal) {
            return null;
        }
        $this->data['oActivePortal'] = $activePortal;

        return $activePortal->GetTitle();
    }

    /**
     * returns the url to Home (e.g. /en/ if it is a language portal)
     * loads the portals home tree node as object in $this->data.
     *
     * @return string
     */
    protected function GetHomeURL()
    {
        $oPortalHomeNode = $this->_GetPortalHomeNode();
        $this->data['oPortalHomeNode'] = $oPortalHomeNode;
        /** @deprecated - use $data['sHomeURL'] instead of data['oPortalHomeNode']->GetLink(); */
        $url = '/';
        $activePortal = $this->getPortalDomainService()->getActivePortal();
        if (null !== $activePortal && '' !== $activePortal->sqlData['identifier']) {
            $url = '/'.$activePortal->sqlData['identifier'].'/';
        }

        return $url;
    }

    /**
     * load portal and portal home tree node.
     *
     * @return TdbCmsTree
     */
    protected function _GetPortalHomeNode()
    {
        $activePortal = $this->getPortalDomainService()->getActivePortal();
        $oPortalHomeNode = null;
        if (null !== $activePortal) {
            $oPortalHomeNode = $activePortal->GetPortalHomeNode();
        }

        return $oPortalHomeNode;
    }

    /**
     * returns the portals logo as object.
     *
     * @return TCMSImage - returns null if no image is set
     */
    protected function _GetPortalLogo()
    {
        $activePortal = $this->getPortalDomainService()->getActivePortal();
        $oPortalLogo = null;
        if (null !== $activePortal) {
            $oPortalLogo = $activePortal->GetLogo();
            $this->aCacheMediaIDs[] = $oPortalLogo->id;
        }

        return $oPortalLogo;
    }

    /**
     * returns the page title based on the breadcrumb navigation.
     *
     * @return string
     */
    public function _GetTitle()
    {
        // default SEO pattern of the page
        $sTitle = $this->GetSeoPatternString();

        if (strlen($sTitle) < 1) {
            $activePortal = $this->getPortalDomainService()->getActivePortal();
            $sTitle = ServiceLocator::get('translator')->trans('chameleon_system_core.page_meta_core.no_title');
            $sBreadcrumb = '';
            if (null !== $activePortal) {
                $this->data['oActivePortal'] = $activePortal;
                $sPortalName = $this->_GetPortalName();
                $activePage = $this->getActivePageService()->getActivePage();
                $oBreadcrumb = $activePage->getBreadcrumb();
                if (is_object($oBreadcrumb)) {
                    $oBreadcrumb->GoToStart();

                    if ($this->bOrderTitleReversed) {
                        $oBreadcrumb->ReverseItemList();
                    }

                    /** @var $oNode TCMSTreeNode */
                    while ($oNode = $oBreadcrumb->Next()) {
                        if ($this->bOrderTitleReversed) {
                            $sBreadcrumb .= $oNode->GetName().$this->sTitleSeparator;
                        } else {
                            $sBreadcrumb .= $this->sTitleSeparator.$oNode->GetName();
                        }
                    }

                    if ($this->bOrderTitleReversed) {
                        $oBreadcrumb->ReverseItemList();
                    }

                    $oBreadcrumb->GoToStart();
                }

                if ($this->bOrderTitleReversed) {
                    $aAdditionalBreadcrumbNodes = array_reverse($this->aAdditionalBreadcrumbNodes);
                } else {
                    $aAdditionalBreadcrumbNodes = $this->aAdditionalBreadcrumbNodes;
                }

                foreach ($aAdditionalBreadcrumbNodes as $aLink) {
                    if ($this->bOrderTitleReversed) {
                        $sBreadcrumb .= $aLink['name'].$this->sTitleSeparator;
                    } else {
                        $sBreadcrumb .= $this->sTitleSeparator.$aLink['name'];
                    }
                }

                if ($this->bOrderTitleReversed) {
                    $sTitle = $sBreadcrumb.$sPortalName;
                } else {
                    $sTitle = $sPortalName.$sBreadcrumb;
                }
            }
        }

        return $sTitle;
    }

    /**
     * returns the custom header data from portal config
     * adds a comment tag with the page id, if a valid user session was found
     * the page id may be used for faster finding a web page in CMS.
     *
     * @return string
     */
    protected function _GetCustomHeaderData()
    {
        $activePortal = $this->getPortalDomainService()->getActivePortal();
        $sCustomHeaderData = '';

        if (!is_null($activePortal)) {
            $sCustomHeaderData = $activePortal->sqlData['custom_metadata'];

            // add pageID to content if user is logged in
            /** @var SecurityHelperAccess $securityHelper */
            $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

            if (true === $securityHelper->isGranted(CmsUserRoleConstants::CMS_USER)) {
                $activePage = $this->getActivePageService()->getActivePage();
                $sCustomHeaderData .= "\n<!-- CMS page ID: ".TGlobal::OutHTML($activePage->id).'; IDENT: '.TGlobal::OutHTML($activePage->sqlData['cmsident'])."-->\n";
            }
        }

        return $sCustomHeaderData;
    }

    /**
     * returns the base url to the current page.
     *
     * @return string
     */
    protected function _GetActivePageURL()
    {
        if (null === $this->getPortalDomainService()->getActivePortal()) {
            $url = ServiceLocator::get('translator')->trans('chameleon_system_core.page_meta_core.no_url');
        } else {
            $url = $this->getActivePageService()->getLinkToActivePageRelative();
        }

        return $url;
    }

    /**
     * returns a multi-dimensional array of all meta data that may be configured
     * in portal config or overloaded by the page config for the current page.
     *
     * @return array
     */
    protected function _GetMetaData()
    {
        $activePage = $this->getActivePageService()->getActivePage();
        $activePortal = $this->getPortalDomainService()->getActivePortal();

        if (null === $activePage || null === $activePortal) {
            return [];
        }

        $aMetaData = [
            'charset' => 'UTF-8',
            'http-equiv' => [],
            'name' => [],
        ];
        $aMetaData['name']['description'] = $activePortal->sqlData['meta_description'];
        $aMetaData['name']['author'] = $activePortal->sqlData['meta_author'];
        $aMetaData['name']['publisher'] = $activePortal->sqlData['meta_publisher'];

        $aMetaData['name']['keywords'] = $activePortal->sqlData['meta_keywords'];
        $aMetaData['name']['robots'] = 'index, follow, all, noodp';

        $division = $activePage->getDivision();
        if ($division) {
            if (!empty($division->sqlData['keywords'])) {
                $aMetaData['name']['keywords'] = $division->sqlData['keywords'];
            }
        }
        // now overwrite meta with meta from the page (if set)
        if (!empty($activePage->sqlData['meta_description'])) {
            $aMetaData['name']['description'] = $activePage->sqlData['meta_description'];
        }
        if (!empty($activePage->sqlData['meta_author'])) {
            $aMetaData['name']['author'] = $activePage->sqlData['meta_author'];
        }
        if (!empty($activePage->sqlData['meta_publisher'])) {
            $aMetaData['name']['publisher'] = $activePage->sqlData['meta_publisher'];
        }
        if (!empty($activePage->sqlData['meta_keywords'])) {
            $aMetaData['name']['keywords'] = $activePage->sqlData['meta_keywords'];
        }
        if (!empty($activePage->sqlData['meta_robots'])) {
            $aMetaData['name']['robots'] = $activePage->sqlData['meta_robots'];
        }

        return $aMetaData;
    }

    /**
     * {@inheritdoc}
     */
    public function _AllowCache()
    {
        return null !== $this->getPortalDomainService()->getActivePortal();
    }

    /**
     * {@inheritdoc}
     */
    public function _GetCacheParameters()
    {
        $cacheParameters = parent::_GetCacheParameters();

        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        if (true === $securityHelper->isGranted(CmsUserRoleConstants::CMS_USER)) {
            $cacheParameters['debugOutputActive'] = true;
        }

        $oActivePage = $this->getActivePageService()->getActivePage();
        $cacheParameters['activepage'] = $oActivePage->id;
        $cacheParameters['aAdditionalBreadcrumbNodes'] = serialize($this->aAdditionalBreadcrumbNodes);

        return $cacheParameters;
    }

    /**
     * {@inheritdoc}
     */
    public function _GetCacheTableInfos()
    {
        $activePortal = $this->getPortalDomainService()->getActivePortal();
        $activePage = $this->getActivePageService()->getActivePage();
        $activeDivision = $activePage->getDivision();

        $tableInfo = [['table' => 'cms_tree', 'id' => ''],
            ['table' => 'cms_tree_node', 'id' => ''],
            ['table' => 'cms_tpl_page', 'id' => (null !== $activePage) ? ($activePage->id) : null],
            ['table' => 'cms_portal', 'id' => (null !== $activePortal) ? ($activePortal->id) : null],
            ['table' => 'cms_portal_navigation', 'id' => ''],
            ['table' => 'cms_division', 'id' => (null !== $activeDivision) ? $activeDivision->id : null],
        ];

        if (count($this->aCacheMediaIDs) > 0) {
            $tableInfo[] = ['table' => 'cms_media', 'id' => $this->aCacheMediaIDs];
        }

        return $tableInfo;
    }

    /**
     * Get SEO pattern string.
     *
     * @param bool $bDoNotReplace Do not replace pattern with values
     *
     * @return string
     */
    protected function GetSeoPatternString($bDoNotReplace = false)
    {
        $sPattern = '';

        // default SEO pattern of actual page
        $activePage = $this->getActivePageService()->getActivePage();
        if (is_object($activePage)) {
            if (!empty($activePage->sqlData['seo_pattern'])) {
                $sPattern = $activePage->sqlData['seo_pattern'];
            }

            $aReplace = [];
            $aReplace['PAGE_NAME'] = $activePage->GetName();

            $activePortal = $this->getPortalDomainService()->getActivePortal();
            if (is_object($activePortal)) {
                $aReplace['PORTAL_NAME'] = $activePortal->GetTitle();
            }

            $oSeoRenderer = new TCMSRenderSeoPattern();
            $oSeoRenderer->AddPatternReplaceValues($aReplace);

            if (!$bDoNotReplace) {
                $sPattern = $oSeoRenderer->RenderPattern($sPattern);
            }
        }

        return $sPattern;
    }

    /**
     * return the canonical URL for the page.
     *
     * @return string
     */
    protected function GetMetaCanonical()
    {
        $activePage = $this->getActivePageService()->getActivePage();
        $activePortal = $this->getPortalDomainService()->getActivePortal();

        if (null === $activePage || null === $activePortal) {
            return '';
        }

        if (true === $this->isNotFoundPage($activePage, $activePortal)) {
            return '';
        }

        return $activePage->GetRealURLPlain([], true);
    }

    private function isNotFoundPage(TCMSActivePage $activePage, TdbCmsPortal $activePortal): bool
    {
        return $activePage->fieldPrimaryTreeIdHidden === $activePortal->fieldPageNotFoundNode;
    }

    private function getLanguageAlternatives(): array
    {
        $activePortal = $this->getPortalDomainService()->getActivePortal();
        if (null === $activePortal) {
            return [];
        }

        $languageService = $this->getLanguageService();
        $activeLanguage = $languageService->getActiveLanguage();
        if (null === $activeLanguage) {
            return [];
        }

        $activeLanguages = $activePortal->GetActiveLanguages();
        if ($activeLanguages->Length() < 2) {
            return [];
        }

        $activePage = $this->getActivePageService()->getActivePage();
        if (null === $activePage || true === $this->isNotFoundPage($activePage, $activePortal)) {
            return [];
        }

        $alternatives = [];
        while (false !== ($alternativeLanguage = $activeLanguages->Next())) {
            $iso = $alternativeLanguage->fieldIso6391;
            try {
                $url = $alternativeLanguage->GetTranslatedPageURL();
                $alternatives[$iso] = $url;
            } catch (Exception $exception) {
                $logLevel = Logger::ERROR;
                if ($exception instanceof RouteNotFoundException) {
                    // This is ok: page might be disabled on purpose in that language
                    $logLevel = Logger::DEBUG;
                }

                $this->getLogger()->log(
                    $logLevel,
                    sprintf(
                        'Cannot generate alternative language URLs for page with ID "%s" and name "%s" for language "%s".',
                        $activePage->id,
                        $activePage->GetName(),
                        $iso
                    ),
                    ['exception' => $exception]
                );
            }
        }

        return $alternatives;
    }

    private function getActivePageService(): ActivePageServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.active_page_service');
    }

    private function getPortalDomainService(): PortalDomainServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }

    private function getLanguageService(): LanguageServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.language_service');
    }

    private function getLogger(): LoggerInterface
    {
        return ServiceLocator::get('logger');
    }
}

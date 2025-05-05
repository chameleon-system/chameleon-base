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
use ChameleonSystem\CoreBundle\ServiceLocator;
use Psr\Log\LoggerInterface;

class TViewPathManager implements IViewPathManager
{
    /**
     * @var PortalDomainServiceInterface
     */
    private $portalDomainService;
    /**
     * @var TPkgViewRendererSnippetDirectoryInterface
     */
    private $viewRendererSnippetDirectory;

    public function __construct(PortalDomainServiceInterface $portalDomainService, TPkgViewRendererSnippetDirectoryInterface $viewRendererSnippetDirectory)
    {
        $this->portalDomainService = $portalDomainService;
        $this->viewRendererSnippetDirectory = $viewRendererSnippetDirectory;
    }

    /**
     * @param string $sViewName
     * @param string $sModuleName
     * @param string $sType
     *
     * @return string
     */
    public function getBackendModuleViewPath($sViewName, $sModuleName = '', $sType = 'Core')
    {
        $sPath = PATH_MODULES;
        if ('Customer' === $sType) {
            $sPath = PATH_MODULES_CUSTOMER;
        }

        $sTemplatePath = $sPath.'/'.$sModuleName.'/views/'.$sViewName.'.view.php';

        return $sTemplatePath;
    }

    /**
     * Get the path to a backend module view
     * theme option only used by pkgBlog currently.
     *
     * @param string $sModuleName
     * @param string $sViewName
     *
     * @return string
     */
    public function getModuleViewPath($sModuleName, $sViewName)
    {
        $sTemplatePath = realpath(PATH_CUSTOMER_FRAMEWORK_MODULES.'/'.$sModuleName.'/views/'.$sViewName.'.view.php');

        // overwrite path with theme path if we find a portal for the current page and a theme is set
        $activePortal = $this->portalDomainService->getActivePortal();
        if (null !== $activePortal) {
            $sThemePath = $activePortal->GetThemeBaseModuleViewsPath();
            if (!empty($sThemePath)) {
                $sTemplatePath = realpath($sThemePath.'/'.$sModuleName.'/'.$sViewName.'.view.php');
            }
        }

        return $sTemplatePath;
    }

    /**
     * @param string $sViewName
     * @param string $sModuleName
     * @param string $sType
     * @param string|null $sMappedPath - if there is already a mapped path replacing PATH_CUSTOMER_FRAMEWORK
     *
     * @return string
     */
    public function getWebModuleViewPath($sViewName, $sModuleName, $sType = 'Customer', $sMappedPath = null)
    {
        if (null === $sMappedPath) {
            $sMappedPath = PATH_CUSTOMER_FRAMEWORK.'/modules/';
        }
        $baseModuleName = basename(str_replace('\\', '/', $sModuleName));
        $sModuleName = str_replace('\\', '_', $sModuleName);
        // try to get it first from customer module path
        $sTemplatePath = $sMappedPath.$sModuleName.'/views/'.$sViewName.'.view.php';
        if (false === file_exists($sTemplatePath)) {
            $sViewFileName = $sModuleName.'/'.$sViewName.'.view.php';
            $sTemplatePath = $this->getTemplateFilePathFromTheme($sViewFileName, TPkgViewRendererSnippetDirectory::PATH_MODULES);
            if (false === file_exists($sTemplatePath)) {
                // maybe the view is from a bundle
                $sMappedPath = str_replace('//', '/', $sMappedPath);
                $templatePathBundle = $sMappedPath.$baseModuleName.'/views/'.$sViewName.'.view.php';
                if (file_exists($templatePathBundle)) {
                    return $templatePathBundle;
                } else {
                    $this->getLogger()->error(sprintf('View not found in path: %s or %s', $sTemplatePath, $templatePathBundle));
                }
            }
        }
        if (null !== $sTemplatePath) {
            return $sTemplatePath;
        }
        /*
         * @deprecated only used by pkgBlog
         */
        if (!TGlobal::IsCMSMode()) {
            // overwrite path with theme path if we find a portal for the current page and a theme is set
            $activePortal = $this->portalDomainService->getActivePortal();
            if (null !== $activePortal) {
                $sThemePath = $activePortal->GetThemeBaseModuleViewsPath();
                if (!empty($sThemePath)) {
                    $sTemplatePath = $sThemePath.'/'.$sModuleName.'/'.$sViewName.'.view.php';
                }
            }
        }

        if (null !== $sTemplatePath) {
            return $sTemplatePath;
        }

        $templateModulePath = TPkgViewRendererSnippetDirectory::PATH_MODULES;
        $sTemplatePath = $templateModulePath.'/'.$sViewFileName;

        return $sTemplatePath;
    }

    /**
     * loads the theme snippet chain if available for portal and returns the first occurrence of the file found
     * in one of the snippet chain directories.
     *
     * @param string $sViewFileName
     * @param string $sThemeSubdirectory
     *
     * @return string|null returns theme path or empty string if file not found in any theme directory
     */
    protected function getTemplateFilePathFromTheme($sViewFileName, $sThemeSubdirectory)
    {
        $activePortal = $this->portalDomainService->getActivePortal();
        // overwrite path with theme path if we find a portal for the current page and a theme is set
        if (null === $activePortal) {
            return null;
        }

        $themeDirectoryChain = $this->viewRendererSnippetDirectory->getBasePaths($activePortal, $sThemeSubdirectory);
        if (0 === count($themeDirectoryChain)) {
            return null;
        }

        $themeDirectoryChain = array_reverse($themeDirectoryChain);
        foreach ($themeDirectoryChain as $themeChainPath) {
            $filename = $themeChainPath.'/'.$sViewFileName;

            if (true === file_exists($filename)) {
                return $filename;
            }
        }

        return null;
    }

    /**
     * The path in the mono repo in chameleon is different from the path in the composer files of the
     * individual packages. This is an unfortunate change and should not really concern the core.
     * Renaming bundles again is a massiv amount of work and would re-introduce bad names. So we keep
     * the mapping - In order to be able to remove the mapping `\TViewPathManager::getObjectPackageViewPath`
     * needs to be deprecated - and replaced by a method that expects the bundle name instead of a path mapping.
     */
    private function mapChameleonPrivatePackageToMonoRepoPath(string $privatePackageName): string
    {
        return match ($privatePackageName) {
            'articleparagraphbundle' => 'article-paragraph',
            'artificial-intelligence-bundle' => 'artificial-intelligence',
            'chameleon-shop-theme-sell' => 'theme-sell',
            'chameleon-standard-logging-bundle' => 'chameleon-standard-logging',
            'ckeditor-bootstrap-bundle' => 'ckeditor-bootstrap',
            'consent-management-bundle' => 'consent-management',
            'country-based-pricing-bundle' => 'country-based-pricing',
            'elastic-bundle' => 'elastic',
            'elastic-integration-bundle' => 'elastic-integration',
            'elastic-shop-integration-bundle' => 'elastic-shop-integration',
            'externaltrackermatomo' => 'external-tracker-matomo',
            'google-recaptcha-bundle' => 'google-recaptcha',
            'google-tag-manager-bundle' => 'google-tag-manager',
            'google-tag-manager-shop-bundle' => 'google-tag-manager-shop',
            'id-rewrite-bundle' => 'id-rewrite',
            'image-optimization-bundle' => 'image-optimization',
            'import-bundle' => 'import',
            'item-list-bundle' => 'item-list',
            'keep-me-logged-in-bundle' => 'keep-me-logged-in',
            'klarna-payments-bundle' => 'klarna-payments',
            'legacy-translation-bundle' => 'legacy-translation',
            'libri-bibliography-service-bundle' => 'libri-bibliography-service',
            'login-security-bundle' => 'login-security',
            'markdown-cms-bundle' => 'markdown-cms',
            'meta-data-bundle' => 'meta-data',
            'meta-tags-filter-bundle' => 'meta-tags-filter',
            'open-graph-meta-data-article-bundle' => 'open-graph-meta-data-article',
            'open-graph-meta-data-bundle' => 'open-graph-meta-data',
            'paypal-checkout-bundle' => 'paypal-checkout',
            'pkgactiveusercountry' => 'active-user-country',
            'pkgaddressvalidationdhladdressfactory' => 'address-validation-dhl-address-factory',
            'pkgarticle' => 'article',
            'pkgarticlepkgshop' => 'article-pkg-shop',
            'pkgarticlerights' => 'article-rights',
            'pkgautoglossary' => 'auto-glossary',
            'pkgautomaticmailevent' => 'automatic-mail-event',
            'pkgcmsaddressvalidation' => 'cms-address-validation',
            'pkgcmsbulksql' => 'cms-bulk-sql',
            'pkgcmscachewarmer' => 'cms-cache-warmer',
            'pkgcmscreditcheck' => 'cms-credit-check',
            'pkgcmsform' => 'cms-form',
            'pkgcmssimulation' => 'cms-simulation',
            'pkgcmstranslationnotification' => 'cms-translation-notification',
            'pkgconfigurableform' => 'configurable-form',
            'pkgcustomsearch' => 'custom-search',
            'pkgcustomsearchtrigger' => 'custom-search-trigger',
            'pkgdarksite' => 'dark-site',
            'pkgexternaltrackeradform' => 'external-tracker-adform',
            'pkgexternaltrackeraffilinet' => 'external-tracker-affilinet',
            'pkgexternaltrackerbilliger' => 'external-tracker-billiger',
            'pkgexternaltrackercriteo' => 'external-tracker-criteo',
            'pkgexternaltrackereconda' => 'external-tracker-econda',
            'pkgexternaltrackerfashionde' => 'external-tracker-fashionde',
            'pkgexternaltrackergoogleadwordsconversioncontact' => 'external-tracker-google-adwords-conversion-contact',
            'pkgexternaltrackergoogleadwordsconversiontracker' => 'external-tracker-google-adwords-conversion-tracker',
            'pkgexternaltrackergoogleadwordsremarketing' => 'external-tracker-google-adwords-remarketing',
            'pkgexternaltrackergosquared' => 'external-tracker-go-squared',
            'pkgexternaltrackerhurra' => 'external-tracker-hurra',
            'pkgexternaltrackermoebelde' => 'external-tracker-moebel-de',
            'pkgexternaltrackershoplupe' => 'external-tracker-shop-lupe',
            'pkgexternaltrackershopping24' => 'external-tracker-shopping-24',
            'pkgexternaltrackerveinteractive' => 'external-tracker-ve-interactive',
            'pkgformwizard' => 'form-wizard',
            'pkglikecount' => 'like-count',
            'pkgmandrill' => 'mandrill',
            'pkgmodulefeedbacktopic' => 'module-feedback-topic',
            'pkgnameduserobjectbasket' => 'named-user-object-basket',
            'pkgnewslettergoogleanalyticstracking' => 'newsletter-google-analytics-tracking',
            'pkgnewsletterinxmail' => 'newsletter-inxmail',
            'pkgnewslettermailchimp' => 'newsletter-mailchimp',
            'pkgquerymanager' => 'query-manager',
            'pkgremotepagetemplate' => 'remote-page-template',
            'pkgserializetool' => 'serialize-tool',
            'pkgshopaddressvalidation' => 'shop-address-validation',
            'pkgshopaffiliatewebgains' => 'shop-affiliatewebgains',
            'pkgshoparticlecountryblacklist' => 'shop-articlecountryblacklist',
            'pkgshopbrandstores' => 'shop-brandstores',
            'pkgshopbrandteaser' => 'shop-brandteaser',
            'pkgshopbuyablevoucher' => 'shop-buyablevoucher',
            'pkgshopcategorynestedset' => 'shop-categorynestedset',
            'pkgshopchanneladvisor' => 'shop-channeladvisor',
            'pkgshopchanneladvisorpkgshopcurrency' => 'shop-channeladvisorpkgshopcurrency',
            'pkgshopcreditcheck' => 'shop-creditcheck',
            'pkgshopcurrencyindividual' => 'shop-currencyindividual',
            'pkgshopcustomercomment' => 'shop-customercomment',
            'pkgshopgenericcrossselling' => 'shop-genericcrossselling',
            'pkgshopgiveaway' => 'shop-giveaway',
            'pkgshopgraduatedprices' => 'shop-graduatedprices',
            'pkgshopgrossnetmanager' => 'shop-grossnetmanager',
            'pkgshopmanufacturerdiscount' => 'shop-manufacturerdiscount',
            'pkgshopordercomment' => 'shop-ordercomment',
            'pkgshoporderoption' => 'shop-orderoption',
            'pkgshoppaymentpayone' => 'shop-payment-payone',
            'pkgshoppaymentpayonestoreduserpayment' => 'shop-payment-payone-stored-user-payment',
            'pkgshoppermanentbasket' => 'shop-permanent-basket',
            'pkgshopproductexportads2people' => 'shop-product-export-ads2people',
            'pkgshopproductexportbilliger' => 'shop-product-export-billiger',
            'pkgshopproductexportciao' => 'shop-product-export-ciao',
            'pkgshopproductexportdooyoo' => 'shop-product-export-dooyoo',
            'pkgshopproductexportgeizkragen' => 'shop-product-export-geizkragen',
            'pkgshopproductexportgoogle' => 'shop-product-export-google',
            'pkgshopproductexportidealo' => 'shop-product-export-idealo',
            'pkgshopproductexportnextag' => 'shop-product-export-nextag',
            'pkgshopproductexportpangora' => 'shop-product-export-pangora',
            'pkgshopproductexportpreissuchmaschine' => 'shop-product-export-preissuchmaschine',
            'pkgshopproductexportrakuten' => 'shop-product-export-rakuten',
            'pkgshopproductexportsoquero' => 'shop-product-export-soquero',
            'pkgshopproductexportstylelounge' => 'shop-product-export-stylelounge',
            'pkgshopproductexportwebgains' => 'shop-product-export-webgains',
            'pkgshopproductexportyatego' => 'shop-product-export-yatego',
            'pkgshopproductexportyopi' => 'shop-product-export-yopi',
            'pkgshopratingservicepkgshoparticlereview' => 'shop-rating-service-pkg-shop-article-review',
            'pkgshopserviceitem' => 'shop-service-item',
            'pkgshopshowcase' => 'shop-showcase',
            'pkgshopstoreduserpayment' => 'shop-stored-user-payment',
            'pkgshopuserrestrictedproducts' => 'shop-user-restricted-products',
            'pkgsurvey' => 'survey',
            'pkgtranslationservice' => 'translation-service',
            'pkgtrustedshops' => 'trusted-shops',
            'pkgtshoppaymenthandlerogone' => 'shop-payment-handler-ogone',
            'pkguserobjectbasket' => 'user-object-basket',
            'position-aware-discount-and-voucher-bundle' => 'position-aware-discount-and-voucher',
            'product-shipping-cost-preview-bundle' => 'product-shipping-cost-preview',
            'redis-bundle' => 'redis',
            'responsive-images-bundle' => 'responsive-images',
            'review-bundle' => 'review',
            'shop-multi-warehouse-bundle' => 'shop-multi-warehouse',
            'sitemap-bundle' => 'sitemap',
            'symfony-customization-bundle' => 'symfony-customization',
            'theme-extensions-bundle' => 'theme-extensions',
            'theme-extensions-generator-bundle' => 'theme-extensions-generator',
            'tracking-consent-bundle' => 'tracking-consent',
            'translation-tools-bundle' => 'translation-tools',
            default => $privatePackageName
        };
    }

    /**
     * @param string $sViewName
     * @param string $sSubType
     * @param string $sType
     *
     * @return string
     */
    public function getObjectPackageViewPath($sViewName, $sSubType = '', $sType = 'Core')
    {
        $sPath = TGlobal::_GetClassRootPath($sSubType, $sType);
        if (ESONO_PACKAGES === $sPath && false !== strpos($sSubType, '/')) {
            // Remove empty elements.
            $sSubType = \trim($sSubType, '/');
            $sSubType = \str_replace('//', '/', $sSubType);

            $parts = \explode('/', $sSubType);
            $parts[0] = \strtolower($parts[0]);
            $parts[0] = $this->getPackagePath($parts[0]);
            $sSubType = \implode('/', $parts);

            if (false === is_dir($sPath.'/'.$sSubType)) {
                // it must be a chameleon-system-private package.
                $sPath = realpath(rtrim(ESONO_PACKAGES, '/').'-private');
                if (false === is_dir($sPath.'/'.$sSubType)) {
                    // They were renamed if installed as mono repos -so we need to remap
                    // fetch new package name
                    $sSubType = \trim($sSubType, '/');
                    $sSubType = \str_replace('//', '/', $sSubType);

                    $parts = \explode('/', $sSubType);
                    $parts[0] = \strtolower($parts[0]);
                    $parts[0] = $this->mapChameleonPrivatePackageToMonoRepoPath($parts[0]);
                    $sSubType = \implode('/', $parts);
                    $sPath = realpath($sPath.'/monorepo/packages');
                }
            }
        }

        $sFilePath = $sSubType.'/'.$sViewName.'.view.php';
        $sTemplatePath = $sPath.'/'.$sFilePath;

        // try to get it first from customer in extensions/library/classes then try extension objectviews
        $sTemplatePathFromTheme = $this->getTemplateFilePathFromTheme($sFilePath, 'library/classes');
        if (null === $sTemplatePathFromTheme) {
            $sTemplatePathFromTheme = $this->getTemplateFilePathFromTheme($sFilePath, TPkgViewRendererSnippetDirectory::PATH_OBJECTVIEWS);
        }
        if (null !== $sTemplatePathFromTheme) {
            return $sTemplatePathFromTheme;
        }

        if ('Core' !== $sType && false == TGlobal::IsCMSMode()) {
            $activePortal = $this->portalDomainService->getActivePortal();
            if (null !== $activePortal) {
                $sThemePath = $activePortal->GetThemeObjectViewsPath();
                if (!empty($sThemePath)) {
                    $sTemplatePath = $sThemePath.'/'.$sFilePath;
                }
            }
        }

        return $sTemplatePath;
    }

    /**
     * Maps old package names to new paths. This is required as the system relied upon packages directly in
     * vendor/chameleon-system which is no longer the case since 6.2.0. Replacing this behavior is not so simple, as
     * there is a very large potential for BC breaks.
     *
     * @param string $oldPackageName
     *
     * @return string
     */
    private function getPackagePath($oldPackageName)
    {
        if (array_key_exists($oldPackageName, self::$packagePaths)) {
            return self::$packagePaths[$oldPackageName];
        }

        return $oldPackageName;
    }

    /**
     * @param string $sViewName
     * @param string $sSubType
     * @param string $sType @deprecated since 7.1.6
     *
     * @return string
     */
    public function getObjectViewPath($sViewName, $sSubType = '', $sType = 'Core')
    {
        $viewFileName = $sSubType.'/'.$sViewName.'.view.php';

        $sTemplatePathFromTheme = $this->getTemplateFilePathFromTheme($viewFileName, TPkgViewRendererSnippetDirectory::PATH_OBJECTVIEWS);
        if (null !== $sTemplatePathFromTheme) {
            return $sTemplatePathFromTheme;
        }

        /**
         * @deprecated all object views should move to a theme directory
         */
        $path = _CMS_CUSTOMER_CORE.'/objectviews';

        // overwrite path with theme path if we find a portal for the current page and a theme is set
        $activePortal = $this->portalDomainService->getActivePortal();
        if (null !== $activePortal) {
            $themePath = $activePortal->GetThemeObjectViewsPath();
            if (false === empty($themePath)) {
                $path = $themePath;
            }
        }

        $templatePath = $path.'/'.$viewFileName;

        if (file_exists($templatePath)) {
            return $templatePath;
        }

        // fallback to core
        $path = _CMS_CORE.'/rendering/objectviews/';
        $templatePath = $path.'/'.$viewFileName;

        return $templatePath;
    }

    /**
     * get path to layout file if exists in theme directory chain or in private/framework/layoutTemplates.
     *
     * @param string $sLayoutName name of layout with or without .layout.php
     *
     * @return string|null
     */
    public function getLayoutViewPath($sLayoutName)
    {
        if ('.layout.php' != substr($sLayoutName, -11)) {
            $sLayoutName .= '.layout.php';
        }
        /**
         * @deprecated all layout templates should move to a theme directory
         */
        $sLayoutPath = PATH_CUSTOMER_PAGELAYOUTS.$sLayoutName;
        if (false === file_exists($sLayoutPath)) {
            $sLayoutPath = $this->getTemplateFilePathFromTheme($sLayoutName, TPkgViewRendererSnippetDirectory::PATH_LAYOUTS);
        }

        return $sLayoutPath;
    }

    /**
     * @param string $sFullView
     *
     * @return string
     */
    public function getBackendModuleViewFromFullPath($sFullView)
    {
        return $sFullView;
    }

    /**
     * @var array
     */
    private static $packagePaths = [
        'core' => 'chameleon-base/src/CoreBundle',
        'pkgatomiclock' => 'chameleon-base/src/AtomicLockBundle',
        'pkgcmsactionplugin' => 'chameleon-base/src/CmsActionPluginBundle',
        'pkgcmscache' => 'chameleon-base/src/CmsCacheBundle',
        'pkgcmscaptcha' => 'chameleon-base/src/CmsCaptchaBundle',
        'pkgcmschangelog' => 'chameleon-base/src/CmsChangeLogBundle',
        'pkgcmsclassmanager' => 'chameleon-base/src/CmsClassManagerBundle',
        'pkgcmscorelog' => 'chameleon-base/src/CmsCoreLogBundle',
        'pkgcmscounter' => 'chameleon-base/src/CmsCounterBundle',
        'pkgcmsevent' => 'chameleon-base/src/CmsEventBundle',
        'pkgcmsfilemanager' => 'chameleon-base/src/CmsFileManagerBundle',
        'pkgcmsinterfacemanager' => 'chameleon-base/src/CmsInterfaceManagerBundle',
        'pkgcmsnavigation' => 'chameleon-base/src/CmsNavigationBundle',
        'pkgcmsresultcache' => 'chameleon-base/src/CmsResultCacheBundle',
        'pkgcmsrouting' => 'chameleon-base/src/CmsRoutingBundle',
        'pkgcmsstringutilities' => 'chameleon-base/src/CmsStringUtilitiesBundle',
        'pkgcmstextblock' => 'chameleon-base/src/CmsTextBlockBundle',
        'pkgcmstextfield' => 'chameleon-base/src/CmsTextFieldBundle',
        'pkgcomment' => 'chameleon-base/src/CommentBundle',
        'pkgcore' => 'chameleon-base/src/PkgCoreBundle',
        'pkgcorevalidatorconstraints' => 'chameleon-base/src/CoreValidatorConstraintsBundle',
        'pkgcsv2sql' => 'chameleon-base/src/Csv2SqlBundle',
        'pkgexternaltracker' => 'chameleon-base/src/ExternalTrackerBundle',
        'pkgexternaltrackergoogleanalytics' => 'chameleon-base/src/ExternalTrackerGoogleAnalyticsBundle',
        'pkgextranet' => 'chameleon-base/src/ExtranetBundle',
        'pkggenerictableexport' => 'chameleon-base/src/GenericTableExportBundle',
        'pkgmultimodule' => 'chameleon-base/src/MultiModuleBundle',
        'pkgnewsletter' => 'chameleon-base/src/NewsletterBundle',
        'pkgrevisionmanagement' => 'chameleon-base/src/RevisionManagementBundle',
        'pkgsnippetrenderer' => 'chameleon-base/src/SnippetRendererBundle',
        'pkgtrackviews' => 'chameleon-base/src/TrackViewsBundle',
        'pkgurlalias' => 'chameleon-base/src/UrlAliasBundle',
        'pkgviewrenderer' => 'chameleon-base/src/ViewRendererBundle',

        'pkgcmsnavigationpkgshop' => 'chameleon-shop/src/CmsNavigationPkgShopBundle',
        'pkgextranetregistrationguest' => 'chameleon-shop/src/ExtranetRegistrationGuestBundle',
        'pkgimagehotspot' => 'chameleon-shop/src/ImageHotspotBundle',
        'pkgsearch' => 'chameleon-shop/src/SearchBundle',
        'pkgshop' => 'chameleon-shop/src/ShopBundle',
        'pkgshopaffiliate' => 'chameleon-shop/src/ShopAffiliateBundle',
        'pkgshoparticledetailpaging' => 'chameleon-shop/src/ShopArticleDetailPagingBundle',
        'pkgshoparticlepreorder' => 'chameleon-shop/src/ShopArticlePreorderBundle',
        'pkgshoparticlereview' => 'chameleon-shop/src/ShopArticleReviewBundle',
        'pkgshopcurrency' => 'chameleon-shop/src/ShopCurrencyBundle',
        'pkgshopdhlpackstation' => 'chameleon-shop/src/ShopDhlPackstationBundle',
        'pkgshoplistfilter' => 'chameleon-shop/src/ShopListFilterBundle',
        'pkgshopnewslettersignupwithorder' => 'chameleon-shop/src/ShopNewsletterSignupWithOrderBundle',
        'pkgshoporderstatus' => 'chameleon-shop/src/ShopOrderStatusBundle',
        'pkgshoporderviaphone' => 'chameleon-shop/src/ShopOrderViaPhoneBundle',
        'pkgshoppaymentipn' => 'chameleon-shop/src/ShopPaymentIPNBundle',
        'pkgshoppaymenttransaction' => 'chameleon-shop/src/ShopPaymentTransactionBundle',
        'pkgshopprimarynavigation' => 'chameleon-shop/src/ShopPrimaryNavigationBundle',
        'pkgshopproductexport' => 'chameleon-shop/src/ShopProductExportBundle',
        'pkgshopratingservice' => 'chameleon-shop/src/ShopRatingServiceBundle',
        'pkgshopwishlist' => 'chameleon-shop/src/ShopWishlistBundle',
        'pkgtshoppaymenthandlersofortueberweisung' => 'chameleon-shop/src/ShopPaymentHandlerSofortueberweisungBundle',
    ];

    private function getLogger(): LoggerInterface
    {
        return ServiceLocator::get('logger');
    }
}

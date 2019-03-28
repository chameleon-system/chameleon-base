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

    /**
     * @param PortalDomainServiceInterface              $portalDomainService
     * @param TPkgViewRendererSnippetDirectoryInterface $viewRendererSnippetDirectory
     */
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
     * @param string      $sViewName
     * @param string      $sModuleName
     * @param string      $sType
     * @param string|null $sMappedPath - if there is already a mapped path replacing PATH_CUSTOMER_FRAMEWORK
     *
     * @return string
     */
    public function getWebModuleViewPath($sViewName, $sModuleName, $sType = 'Customer', $sMappedPath = null)
    {
        if (null === $sMappedPath) {
            $sMappedPath = PATH_CUSTOMER_FRAMEWORK.'/modules/';
        }
        $sModuleName = str_replace('\\', '_', $sModuleName);
        // try to get it first from customer module path
        $sTemplatePath = $sMappedPath.$sModuleName.'/views/'.$sViewName.'.view.php';
        if (false === file_exists($sTemplatePath)) {
            $sViewFileName = $sModuleName.'/'.$sViewName.'.view.php';
            $sTemplatePath = $this->getTemplateFilePathFromTheme($sViewFileName, TPkgViewRendererSnippetDirectory::PATH_MODULES);
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
        $sTemplatePath = null;
        if (true === TGlobal::IsCMSMode()) {
            return null;
        }

        $activePortal = $this->portalDomainService->getActivePortal();
        // overwrite path with theme path if we find a portal for the current page and a theme is set
        if (null === $activePortal) {
            return null;
        }

        $aThemeDirectoryChain = $this->viewRendererSnippetDirectory->getBasePaths($activePortal, $sThemeSubdirectory);
        if (0 === count($aThemeDirectoryChain)) {
            return null;
        }

        $aThemeDirectoryChain = array_reverse($aThemeDirectoryChain);
        foreach ($aThemeDirectoryChain as $sThemeChainPath) {
            $sFilePath = $sThemeChainPath.'/'.$sViewFileName;

            if (true === file_exists($sFilePath)) {
                $sTemplatePath = $sFilePath;
                break;
            }
        }

        return $sTemplatePath;
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
     * @param string $sType
     *
     * @return string
     */
    public function getObjectViewPath($sViewName, $sSubType = '', $sType = 'Core')
    {
        $sViewFileName = $sSubType.'/'.$sViewName.'.view.php';

        switch ($sType) {
            case 'Core':
                $sPath = _CMS_CORE.'/rendering/objectviews/';
                $sTemplatePath = $sPath.'/'.$sViewFileName;
                break;
            case 'Custom-Core':
                $sPath = _CMS_CUSTOM_CORE.'/rendering/objectviews/';
                $sTemplatePath = $sPath.'/'.$sViewFileName;
                break;
            case 'Customer':
            default:
                $sTemplatePathFromTheme = $this->getTemplateFilePathFromTheme($sViewFileName, TPkgViewRendererSnippetDirectory::PATH_OBJECTVIEWS);
                if (null !== $sTemplatePathFromTheme) {
                    $sTemplatePath = $sTemplatePathFromTheme;
                } else {
                    /**
                     * @deprecated all object views should move to a theme directory
                     */
                    $sPath = _CMS_CUSTOMER_CORE.'/objectviews';

                    // overwrite path with theme path if we find a portal for the current page and a theme is set
                    $activePortal = $this->portalDomainService->getActivePortal();
                    if (null !== $activePortal) {
                        $sThemePath = $activePortal->GetThemeObjectViewsPath();
                        if (!empty($sThemePath)) {
                            $sPath = $sThemePath;
                        }
                    }

                    $sTemplatePath = $sPath.'/'.$sViewFileName;
                }
                break;
        }

        return $sTemplatePath;
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
        'pkgshoppaymentamazon' => 'chameleon-shop/src/AmazonPaymentBundle',
        'pkgshoppaymentipn' => 'chameleon-shop/src/ShopPaymentIPNBundle',
        'pkgshoppaymenttransaction' => 'chameleon-shop/src/ShopPaymentTransactionBundle',
        'pkgshopprimarynavigation' => 'chameleon-shop/src/ShopPrimaryNavigationBundle',
        'pkgshopproductexport' => 'chameleon-shop/src/ShopProductExportBundle',
        'pkgshopratingservice' => 'chameleon-shop/src/ShopRatingServiceBundle',
        'pkgshopwishlist' => 'chameleon-shop/src/ShopWishlistBundle',
        'pkgtshoppaymenthandlersofortueberweisung' => 'chameleon-shop/src/ShopPaymentHandlerSofortueberweisungBundle',
    ];
}

<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\CoreEvents;
use ChameleonSystem\CoreBundle\Event\ResourceCollectionJavaScriptCollectedEvent;
use ChameleonSystem\CoreBundle\Interfaces\ResourceCollectorInterface;
use ChameleonSystem\CoreBundle\Service\CssMinifierServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * this class can manage resource collection creation.
/**/
class TCMSResourceCollection implements ResourceCollectorInterface
{
    /**
     * base path of server ($_SERVER['DOCUMENT_ROOT']).
     */
    private $sBasePath = null;

    /**
     * @var string|null current absolute css path
     */
    protected $sCurrentCSSPath = null;

    /**
     * @var IPkgCmsFileManager
     */
    private $cmsFileManager;

    /**
     * @var PortalDomainServiceInterface
     */
    private $portalDomainService;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var string
     */
    private $assetUrl;
    /**
     * @var string
     */
    private $assetPath;

    /**
     * @var CssMinifierServiceInterface|null
     */
    private $cssMinifierService;

    public function __construct(
        ?IPkgCmsFileManager $cmsFileManager = null,
        ?PortalDomainServiceInterface $portalDomainService = null,
        ?EventDispatcherInterface $eventDispatcher = null,
        ?CssMinifierServiceInterface $cssMinifierService = null,
        string $assetUrl = URL_OUTBOX.'static',
        string $assetPath = PATH_OUTBOX.'/static'
    ) {
        $this->cmsFileManager = $cmsFileManager ?? ServiceLocator::get('chameleon_system_core.filemanager');
        $this->portalDomainService = $portalDomainService ?? ServiceLocator::get('chameleon_system_core.portal_domain_service');
        $this->eventDispatcher = $eventDispatcher ?? ServiceLocator::get('event_dispatcher');
        $this->cssMinifierService = $cssMinifierService ?? ServiceLocator::get('chameleon_system_core.service.css_minifier');
        $this->assetUrl = rtrim($assetUrl, '/');
        $this->assetPath = rtrim($assetPath, '/');
    }

    /**
     * {@inheritdoc}
     */
    public function IsEnabled()
    {
        return ServiceLocator::getParameter('chameleon_system_core.resources.enable_external_resource_collection');
    }

    /**
     * {@inheritdoc}
     */
    public function IsAllowed()
    {
        if (false === $this->IsEnabled()) {
            return false;
        }

        $oGlobal = TGlobal::instance();
        $bIsModuleChooser = $oGlobal->GetUserData('__modulechooser');
        if (true === TGlobal::IsCMSMode()) {
            // do not enable in backend mode
            return false;
        }
        $bIsTemplateEngine = 'true' === $bIsModuleChooser && TCMSUser::CMSUserDefined();
        if (true === $bIsTemplateEngine) {
            return false;
        }

        if (true === $this->hasResourceCollectionWriteProcessRunning()) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function CollectExternalResources($sPageContent)
    {
        if (false === $this->IsAllowed()) {
            return $sPageContent;
        }
        if (false === \stripos($sPageContent, '</head>')) {
            return $sPageContent;
        }
        $filesPrefix = $this->getFilesRefreshAndDomainPrefix();

        // we only work on the <head></head> content - everything else is kept as is
        $aPageParts = \explode('</head>', $sPageContent);
        if (\count($aPageParts) < 2) {
            $aPageParts = \explode('</HEAD>', $sPageContent);
        }
        if (2 !== \count($aPageParts)) {
            return $sPageContent;
        }

        $sReworkContent = $aPageParts[0];

        // also exclude anything that is marked as exclude
        $matchString = '/<!--#CMSRESOURCEIGNORE#-->(.+?)<!--#ENDCMSRESOURCEIGNORE#-->/si';
        $sReworkContent = preg_replace_callback(
            $matchString,
            array($this, 'CollectExternalResourcesCommentsCallback'),
            $sReworkContent
        );

        // we want to ignore css/js in comments. easy if we strip comments first
        $matchString = '/<!--(.+?)-->/si';
        $sReworkContent = preg_replace_callback(
            $matchString,
            array($this, 'CollectExternalResourcesCommentsCallback'),
            $sReworkContent
        );

        // create resource collection for dynamic css
        $matchString = "/<link([^>]+?)href=[\"]([^'\"]+?).css([\?][^[:space:]]+)?[\"]([^>]*?)>(?!\\s*?<!--(.*?)#GLOBALRESOURCECOLLECTION#)/i";
        $sReworkContent = preg_replace_callback(
            $matchString,
            array($this, 'CollectExternalResourcesCSSCallback'),
            $sReworkContent
        );
        $aCSS = $this->StaticContentCollector('css');


        // create resource collection for static global css
        $matchString = "/<link([^>]+?)href=[\"]([^'\"]+?).css([\?][^[:space:]]+)?[\"]([^>]*?)>\\s*(<!--.*?#GLOBALRESOURCECOLLECTION#.?-->)/i";
        $sReworkContent = preg_replace_callback(
            $matchString,
            array($this, 'CollectExternalResourcesCSSCallback'),
            $sReworkContent
        );
        $aGlobalCSS = $this->StaticContentCollector('cssglobal');


        // repeat for js
        $matchString = "/<script([^>]+?)src=[\"]([^'\"]+?).js([\?][^[:space:]]+)?[\"]([^>]*?)><\/script>(?!\\s*?<!--(.*?)#GLOBALRESOURCECOLLECTION#)/i";
        $sReworkContent = preg_replace_callback(
            $matchString,
            array($this, 'CollectExternalResourcesJSCallback'),
            $sReworkContent
        );
        $aJS = $this->StaticContentCollector('js');
        $minify = ServiceLocator::getParameter(
            'chameleon_system_core.resources.enable_external_resource_collection_minify'
        );
        if ($minify) {
            $sMinifyStatus = 'true';
        } else {
            $sMinifyStatus = 'false';
        }


        $matchString = "/<script([^>]+?)src=[\"]([^'\"]+?).js([\?][^[:space:]]+)?[\"]([^>]*?)><\/script>\\s*(<!--.*?#GLOBALRESOURCECOLLECTION#.?-->)/i";
        $sReworkContent = preg_replace_callback(
            $matchString,
            array($this, 'CollectExternalResourcesJSCallback'),
            $sReworkContent
        );
        $aJSGlobal = $this->StaticContentCollector('jsglobal');

        $hasCollectedCss = \count($aCSS) > 0 || \count($aGlobalCSS) > 0;
        $hasCollectedJs = \count($aJS) > 0 || \count($aJSGlobal) > 0;
        if (false === $hasCollectedCss && false === $hasCollectedJs) {
            return $sPageContent;
        }

        $sFileCSSMD5 = $filesPrefix.'.css.'.md5(implode(';', $aCSS)).'.css';
        $sFileJSMD5 = $filesPrefix.'.js.'.md5(implode(';', $aJS).$sMinifyStatus).'.js';
        $sFileCSSGlobalMD5 = $filesPrefix.'.global_css.'.md5(implode(';', $aGlobalCSS)).'.css';
        $sFileJSGlobalMD5 = $filesPrefix.'.global_js.'.md5(implode(';', $aJSGlobal).$sMinifyStatus).'.js';
        $resourceFilesExist = \file_exists($sFileCSSMD5);
        $resourceFilesExist = $resourceFilesExist && \file_exists($sFileJSMD5);
        $resourceFilesExist = $resourceFilesExist && \file_exists($sFileCSSGlobalMD5);
        $resourceFilesExist = $resourceFilesExist && \file_exists($sFileJSGlobalMD5);

        if (false === $resourceFilesExist) {
            if (false === $this->setResourceCollectionWriteProcessRunning(true)) {
                return $sPageContent;
            }
        }
        try {
            if (false === $this->CreateCSSResourceCollectionFile($sFileCSSMD5, $aCSS)) {
                $sFileCSSMD5 = ''; // reset file because we don`t have includes
            }
            if (false === $this->CreateJSResourceCollectionFile($sFileJSMD5, $aJS)) {
                $sFileJSMD5 = ''; // reset file because we don`t have includes
            }
            if (false === $this->CreateCSSResourceCollectionFile($sFileCSSGlobalMD5, $aGlobalCSS)) {
                $sFileCSSGlobalMD5 = ''; // reset global file because we don`t have global includes
            }
            if (false === $this->CreateJSResourceCollectionFile($sFileJSGlobalMD5, $aJSGlobal)) {
                $sFileJSGlobalMD5 = ''; // reset global file because we don`t have global includes
            }
        } finally {
            if (false === $resourceFilesExist) {
                $this->setResourceCollectionWriteProcessRunning(false);
            }
        }

        $sPageContent = $sReworkContent.'</head>'.$aPageParts[1];

        $sPageContent = $this->AddResourceCollectionToHead(
            $sPageContent,
            $sFileCSSMD5,
            $sFileJSMD5,
            $sFileCSSGlobalMD5,
            $sFileJSGlobalMD5
        );

        return $sPageContent;
    }

    private function getFilesRefreshAndDomainPrefix(): string
    {
        $filesPrefix = ServiceLocator::getParameter('chameleon_system_core.resources.enable_external_resource_collection_refresh_prefix');

        $portal = $this->portalDomainService->getActivePortal();

        if (null !== $portal) {
            $filesPrefix .= $portal->getFileSuffix();
        }

        return $filesPrefix;
    }

    /**
     * Add resource collection files to head.
     *
     * @param string $sPageContent
     * @param string $sFileMD5
     * @param string $sFileJSMD5
     * @param string $sFileCSSGlobalMD5
     * @param string $sFileJSGlobalMD5
     *
     * @return string
     */
    protected function AddResourceCollectionToHead($sPageContent, $sFileMD5, $sFileJSMD5, $sFileCSSGlobalMD5, $sFileJSGlobalMD5)
    {
        $sCompressLinkCSS = '';
        $sCompressLinkCSSGlobal = '';
        $sCompressLinkJs = '';
        $sCompressLinkJsGlobal = '';

        if (!empty($sFileMD5)) {
            $sCompressLinkCSS = '<link href="'.TGlobal::GetStaticURL($this->assetUrl.'/css/'.$sFileMD5).'" rel="stylesheet" type="text/css" />'."\n";
        }
        if (!empty($sFileCSSGlobalMD5)) {
            $sCompressLinkCSSGlobal = '<link href="'.TGlobal::GetStaticURL($this->assetUrl.'/css/'.$sFileCSSGlobalMD5).'" rel="stylesheet" type="text/css" />'."\n";
        }
        if (!empty($sFileJSMD5)) {
            $sCompressLinkJs = '<script src="'.TGlobal::GetStaticURL($this->assetUrl.'/js/'.$sFileJSMD5).'" type="text/javascript"></script>'."\n";
        }
        if (!empty($sFileJSGlobalMD5)) {
            $sCompressLinkJsGlobal = '<script src="'.TGlobal::GetStaticURL($this->assetUrl.'/js/'.$sFileJSGlobalMD5).'" type="text/javascript"></script>'."\n";
        }

        $sPreHeadText = $sCompressLinkCSSGlobal.$sCompressLinkCSS.$sCompressLinkJsGlobal.$sCompressLinkJs;
        $bCompleteCSSAndJsTagFound = (false !== strpos($sPageContent, '<!--#CMSHEADERCODE-COMPACT-JS-AND-CSS#-->'));
        $bCSSTagFound = (false !== strpos($sPageContent, '<!--#CMSHEADERCODE-COMPACT-CSS#-->'));
        $bJSTagFound = (false !== strpos($sPageContent, '<!--#CMSHEADERCODE-COMPACT-JS#-->'));
        if (!$bCompleteCSSAndJsTagFound && !$bCSSTagFound && !$bJSTagFound) {
            $sPreHeadText = '<head>'.$sPreHeadText;
            $sPageContent = str_replace('<head>', $sPreHeadText, $sPageContent);
        } else {
            if ($bCompleteCSSAndJsTagFound) {
                $sPageContent = str_replace('<!--#CMSHEADERCODE-COMPACT-JS-AND-CSS#-->', $sPreHeadText, $sPageContent);
            }
            if ($bCSSTagFound) {
                $sPageContent = str_replace('<!--#CMSHEADERCODE-COMPACT-CSS#-->', ($sCompressLinkCSSGlobal.$sCompressLinkCSS), $sPageContent);
            }
            if ($bJSTagFound) {
                $sPageContent = str_replace('<!--#CMSHEADERCODE-COMPACT-JS#-->', ($sCompressLinkJsGlobal.$sCompressLinkJs), $sPageContent);
            }
        }
        // re-insert comments
        /** @var $aComments array */
        $aComments = $this->StaticContentCollector('comment');
        foreach ($aComments as $iIndex => $sComment) {
            $sPageContent = str_replace('<!-- [{INDEX:'.$iIndex.'}] -->', $sComment, $sPageContent);
        }

        return $sPageContent;
    }

    /**
     * Write resource collection file for css containing all included css files.
     *
     * @param string $sFileMD5
     * @param array  $aCSS
     *
     * @return bool
     */
    protected function CreateCSSResourceCollectionFile($sFileMD5, $aCSS)
    {
        $bFileCreated = false;
        if (is_array($aCSS) && count($aCSS) > 0) {
            $bFileCreated = true;
            $sCSSStaticPath = $this->assetPath.'/css/';
            if (!file_exists($sCSSStaticPath.$sFileMD5)) {
                $bTargetDirectoryIsWritable = true;
                if (!is_dir($sCSSStaticPath)) {
                    $bTargetDirectoryIsWritable = $this->cmsFileManager->mkdir($sCSSStaticPath, 0777, true);
                }

                if ($bTargetDirectoryIsWritable) {
                    $sContent = '';
                    if (_DEVELOPMENT_MODE) {
                        $sContent = '/* created: '.date('Y-m-d H:i:s')."\n";
                        $sContent .= implode("\n", $aCSS)."\n";
                        $sContent .= "*/\n";
                    }
                    $this->sBasePath = $_SERVER['DOCUMENT_ROOT'];

                    foreach ($aCSS as $iIndex => $sCSSFile) {
                        // strip URL params
                        $sCSSFile = parse_url($sCSSFile, PHP_URL_PATH);

                        // change static urls to relative urls

                        $sCSSFile = TGlobal::ResolveStaticURL($sCSSFile);
                        $aStaticURLs = TGlobal::GetStaticURLPrefix();
                        if (!is_array($aStaticURLs)) {
                            $aStaticURLs = array($aStaticURLs);
                        }
                        foreach ($aStaticURLs as $sStaticURL) {
                            if (!empty($sStaticURL)) {
                                if (false !== strpos($sCSSFile, $sStaticURL)) {
                                    $sCSSFile = substr($sCSSFile, strlen($sStaticURL));
                                }
                            }
                        }
                        $sCSSFullURL = $sCSSFile;
                        if ('http://' != substr($sCSSFile, 0, 7) && 'https://' != substr($sCSSFile, 0, 8)) {
                            $sCSSFile = realpath($this->sBasePath.'/'.$sCSSFile);
                        }
                        $aCSS[$iIndex] = $sCSSFile;
                        if (is_string($sCSSFile)) {
                            $sSubString = file_get_contents($sCSSFile);
                            $sSubString = TTools::RemoveUTF8HeaderBomFromString($sSubString); // CSS parsing crashs at UTF-8 BOM Header position

                            $sPath = $this->GetAbsoluteURLPath($sCSSFullURL);
                            $sSubString = $this->ProcessCSSRecursive($sPath, $sSubString);

                            $minify = ServiceLocator::getParameter('chameleon_system_core.resources.enable_external_resource_collection_minify');
                            if (true === $minify) {
                                $sSubString = $this->cssMinifierService->minify($sSubString);
                            }
                            if (_DEVELOPMENT_MODE) {
                                $sContent .= "/* FROM: {$aCSS[$iIndex]} */\n".$sSubString."\n";
                            } else {
                                $sContent .= $sSubString."\n";
                            }
                        }
                    }

                    $this->cmsFileManager->file_put_contents($sCSSStaticPath.$sFileMD5, $sContent);
                }
            }
        }

        return $bFileCreated;
    }

    /**
     * Write resource collection file for JS containing all included JS files.
     *
     * @param string $sFileJSMD5
     * @param array  $aJS
     *
     * @return bool
     */
    protected function CreateJSResourceCollectionFile($sFileJSMD5, $aJS)
    {
        $bFileCreated = false;
        if (is_array($aJS) && count($aJS) > 0) {
            $bFileCreated = true;
            $sJSStaticPath = $this->assetPath.'/js/';
            if (!file_exists($sJSStaticPath.$sFileJSMD5)) {
                $bTargetDirectoryIsWritable = true;
                if (!is_dir($sJSStaticPath)) {
                    $bTargetDirectoryIsWritable = $this->cmsFileManager->mkdir($sJSStaticPath, 0777, true);
                }

                if ($bTargetDirectoryIsWritable) {
                    $sContent = '';
                    if (_DEVELOPMENT_MODE) {
                        $sContent = '/* created: '.date('Y-m-d H:i:s')."\n";
                        $sContent .= implode("\n", $aJS)."\n";
                        $sContent .= "*/\n";
                    }
                    $this->sBasePath = $_SERVER['DOCUMENT_ROOT'];

                    foreach ($aJS as $iIndex => $sJSFile) {
                        // strip URL params
                        $sJSFile = parse_url($sJSFile, PHP_URL_PATH);
                        $sJSFile = TGlobal::ResolveStaticURL($sJSFile);
                        $aStaticURLs = TGlobal::GetStaticURLPrefix();
                        if (!is_array($aStaticURLs)) {
                            $aStaticURLs = array($aStaticURLs);
                        }
                        foreach ($aStaticURLs as $sStaticURL) {
                            if (!empty($sStaticURL)) {
                                if (false !== strpos($sJSFile, $sStaticURL)) {
                                    $sJSFile = substr($sJSFile, strlen($sStaticURL));
                                }
                            }
                        }

                        if ('http://' != substr($sJSFile, 0, 7) && 'https://' != substr($sJSFile, 0, 8)) {
                            $sJSFile = realpath($this->sBasePath.'/'.$sJSFile);
                        }
                        $aJS[$iIndex] = $sJSFile;
                        if (is_string($sJSFile)) {
                            $sSubString = file_get_contents($sJSFile);
                            $sSubString = TTools::RemoveUTF8HeaderBomFromString($sSubString); // JS parsing crashs at UTF-8 BOM Header position

                            if (_DEVELOPMENT_MODE) {
                                $sContent .= "/* FROM: {$aJS[$iIndex]} */\n".$sSubString."\n";
                            } else {
                                $sContent .= $sSubString."\n";
                            }
                        }
                    }
                    $sContent = $this->dispatchJSMinifyEvent($sContent);
                    $this->cmsFileManager->file_put_contents($sJSStaticPath.$sFileJSMD5, $sContent);
                }
            }
        }

        return $bFileCreated;
    }

    /**
     * @param string $jsContent
     *
     * @return string
     */
    private function dispatchJSMinifyEvent($jsContent)
    {
        $event = new ResourceCollectionJavaScriptCollectedEvent($jsContent);
        $event = $this->eventDispatcher->dispatch($event, CoreEvents::GLOBAL_RESOURCE_COLLECTION_COLLECTED_JAVASCRIPT);

        return $event->getContent();
    }

    /**
     * returns absolute URL of current CSS file (strips filename).
     *
     * @param string $sFile   full file URL
     *
     * @return string path of a file
     */
    protected function GetAbsoluteURLPath($sFile)
    {
        $sRes = '';
        if (!empty($sFile)) {
            $sRes = substr($sFile, 0, strripos($sFile, '/'));
        }

        return $sRes;
    }

    /**
     * @param array $aMatch
     *
     * @return string
     */
    protected function CollectExternalResourcesCommentsCallback($aMatch)
    {
        static $aProtectedComments = array('#CMSHEADERCODE-COMPACT-JS-AND-CSS#', '#CMSHEADERCODE-JS#', '#CMSHEADERCODE-CSS#', '#CMSHEADERCODE#', '#CMSHEADERCODE-COMPACT-JS#', '#CMSHEADERCODE-COMPACT-CSS#', '#GLOBALRESOURCECOLLECTION#');
        if (is_array($aMatch) && count($aMatch) > 1 && in_array(trim($aMatch[1]), $aProtectedComments)) {
            return $aMatch[0];
        }

        if (0 < preg_match('/<!-- \\[\\{INDEX:\\d+?\\}\\] -->/', $aMatch[0])) {
            return $aMatch[0];
        }

        $iIndex = $this->StaticContentCollector('comment', $aMatch[0]);

        return '<!-- [{INDEX:'.$iIndex.'}] -->';
    }

    /**
     * @param string      $sType
     * @param string|null $sFile
     *
     * @return array
     */
    protected function StaticContentCollector($sType, $sFile = null)
    {
        static $aContent = array();
        if (!array_key_exists($sType, $aContent)) {
            $aContent[$sType] = array();
        }
        if (!is_null($sFile)) {
            $iIndex = count($aContent[$sType]);
            $aContent[$sType][$iIndex] = $sFile;

            return $iIndex;
        } else {
            return $aContent[$sType];
        }
    }

    /**
     * @param string $sAbsoluteCSSFileURL current path of the css file
     * @param string $sContent            content of the css file
     *
     * @return string
     */
    protected function ProcessCSSRecursive($sAbsoluteCSSFileURL, $sContent)
    {
        //replace the relative url path in css file by absolute, something like [absoluter-path-CSS]/../images/foo.jpg
        $sTemp = $this->ReplaceRelativePath($sAbsoluteCSSFileURL, $sContent);

        //replace "@import css filename" via insert css content from that css file
        $sNewCss = $this->ImportCSSContent($sTemp);
        if ($sNewCss === $sTemp) {
            return $sNewCss;
        } else {
            return $this->ProcessCSSRecursive($this->sCurrentCSSPath, $sNewCss);
        }
    }

    /**
     * replace the relative url path in css file with absolute path.
     *
     * @param string $sAbsoluteCSSFileURL current path of the css file
     * @param string $sContent            content of the css file
     *
     * @return mixed|string content in which relative url is replaced with absolute url
     */
    protected function ReplaceRelativePath($sAbsoluteCSSFileURL, $sContent)
    {
        $this->sCurrentCSSPath = $sAbsoluteCSSFileURL;

        /**
         * replaces url(..)
         * from e.g. background-image.
         */
        $sRegExp = "/url\([[:space:]]*[\"|\']{0,1}(\.{0,2}\S+)[[:space:]]*[\"|\']{0,1}\)/is";
        $sNewContent = preg_replace_callback($sRegExp, array($this, 'ReplaceRealPathCallback'), $sContent);

        /**
         * replaces src=..
         * from progid:DXImageTransform.Microsoft.AlphaImageLoader(src=..).
         */
        $sRegExp = "/src=[[:space:]]*[\"|\']{0,1}(\.{0,2}\S+)[[:space:]]*[\"|\']{0,1}/is";
        $sNewContent = preg_replace_callback($sRegExp, array($this, 'ReplaceRealPathCallback'), $sNewContent);

        /**
         * replaces css @import ..
         */
        $sRegExp = "/@import [[:space:]]*\"(\.{0,2}\S+)[[:space:]]*\"/is";
        $sNewContent = preg_replace_callback($sRegExp, array($this, 'ReplaceRealPathCallback'), $sNewContent);

        return $sNewContent;
    }

    /**
     * replaces relative URLs to absolute URLs based on current CSS file URL
     * (e.g. ../images/image.jpg -> /chameleon/javascript/jQuery/pluginName/images/image.jpg).
     *
     * @param array $aMatch match array of RegExp
     *
     * @return string $aMatch[0]  absolute path
     */
    public function ReplaceRealPathCallback($aMatch)
    {
        if (isset($aMatch[1]) && true === $this->isRelativeUrl($aMatch[1])) {
            $sNewPath = TGlobal::GetStaticURL($this->sCurrentCSSPath.DIRECTORY_SEPARATOR.$aMatch[1]);
            if ('http://' == substr($sNewPath, 0, 7)) {
                $sNewPath = substr($sNewPath, 5);
            }
            if ('https://' == substr($sNewPath, 0, 8)) {
                $sNewPath = substr($sNewPath, 6);
            }
            $aMatch[0] = str_replace(trim($aMatch[1]), $sNewPath, $aMatch[0]);
        }

        return $aMatch[0];
    }

    private function isRelativeUrl(string $url): bool
    {
        if ('/' === substr($url, 0, 1)) {
            return false;
        }

        if ('http://' === substr($url, 0, 7) || 'https://' === substr($url, 0, 8)) {
            return false;
        }

        if ('data:' === substr($url, 0, 5)) {
            return false;
        }

        return true;
    }

    /**
     * Import css Content via replace the css file name by
     * css content.
     *
     * @param string $sContent
     *
     * @return string
     */
    protected function ImportCSSContent($sContent)
    {
        //@import url("print.css") print, @import "<name>" <media>;
        $sRegExp = "#@import\s*(url\(){0,1}\s*\"(\s*\S+\s*)\"(.*)\;#";
        $sNewContent = preg_replace_callback($sRegExp, array($this, 'ReplaceCSSImportCallback'), $sContent);

        return $sNewContent;
    }

    /**
     * callback for function ImportCSSContent that opens the replaced css files
     * and fetch the content from it.
     *
     * @var array $aMatch
     *
     * @return string $sCSS css content
     */
    protected function ReplaceCSSImportCallback($aMatch)
    {
        $sCSSFile = parse_url(trim($aMatch[2]), PHP_URL_PATH);

        $sCSSFile = TGlobal::ResolveStaticURL($sCSSFile);
        $aStaticURLs = TGlobal::GetStaticURLPrefix();
        if (!is_array($aStaticURLs)) {
            $aStaticURLs = array($aStaticURLs);
        }
        foreach ($aStaticURLs as $sStaticURL) {
            if (!empty($sStaticURL)) {
                if (false !== strpos($sCSSFile, $sStaticURL)) {
                    $sCSSFile = substr($sCSSFile, strlen($sStaticURL));
                }
            }
        }
        if ('http://' != substr($sCSSFile, 0, 7) && 'https://' != substr($sCSSFile, 0, 8)) {
            $sCSSFile = str_replace('/', DIRECTORY_SEPARATOR, $sCSSFile);
            $sCSSFile = realpath($this->sBasePath.DIRECTORY_SEPARATOR.$sCSSFile);
        }

        if (is_readable($sCSSFile)) {
            $aMatch[0] = file_get_contents($sCSSFile);
            $aMatch[0] = TTools::RemoveUTF8HeaderBomFromString($aMatch[0]); // CSS parsing crashs at UTF-8 BOM Header position
        }

        return $aMatch[0];
    }

    /**
     * @param array $aMatch
     *
     * @return string
     */
    protected function CollectExternalResourcesCSSCallback($aMatch)
    {
        $sReturn = $aMatch[0];

        // ignore if type = print
        $sTmp = strtolower(str_replace(' ', '', $aMatch[0]));
        $sTmp = str_replace("'", '"', $sTmp);
        if (false === strpos($sTmp, 'media="print"')) {
            $sCSSName = $aMatch[2].'.css';
            if (0 === strpos($aMatch[3], '?')) {
                $sCSSName .= $aMatch[3];
            }
            if ($this->isAllowedResource($sCSSName)) {
                $sType = 'css';
                if (6 == count($aMatch) && strstr($aMatch[5], '#GLOBALRESOURCECOLLECTION#')) {
                    $sType = 'cssglobal';
                }
                $this->StaticContentCollector($sType, $sCSSName);
                $sReturn = ''; // '<!-- '.$sReturn.' -->';
            }
        }

        return $sReturn;
    }

    /**
     * @param array $aMatch
     *
     * @return string
     */
    protected function CollectExternalResourcesJSCallback($aMatch)
    {
        $sReturn = $aMatch[0];
        $sJSName = $aMatch[2].'.js';
        if (0 === strpos($aMatch[3], '?')) {
            $sJSName .= $aMatch[3];
        }
        if ($this->isAllowedResource($sJSName)) {
            $sType = 'js';
            if (6 == count($aMatch) && strstr($aMatch[5], '#GLOBALRESOURCECOLLECTION#')) {
                $sType = 'jsglobal';
            }
            $this->StaticContentCollector($sType, $sJSName);
            $sReturn = ''; // '<!-- '.$sReturn.' -->';
        }

        return $sReturn;
    }

    /**
     * Test whether collecting resource is allowed. Resource is only allowed if
     * it is from local or static.
     *
     * @var string $sResource
     *
     * @return true if resource ist allowed
     */
    protected function isAllowedResource($sResource)
    {
        return $this->isCollectable($sResource) && ($this->isLocalResource($sResource) || $this->isStatic($sResource));
    }

    protected function isCollectable($sResource)
    {
        return false === strpos($sResource, 'nocollection=1');
    }

    /**
     * Test for Static URL.
     *
     * @var string $sResource
     *
     * @return true if resource is static url
     */
    protected function isStatic($sResource)
    {
        if ('[{CMSSTATICURL' == substr($sResource, 0, 14)) {
            return true;
        }

        $bIsStatic = false;
        $aStaticURLs = TGlobal::GetStaticURLPrefix();
        if (!is_array($aStaticURLs)) {
            $aStaticURLs = array($aStaticURLs);
        }
        foreach ($aStaticURLs as $sStaticURL) {
            if (!empty($sStaticURL)) {
                if (false !== strpos($sResource, $sStaticURL)) {
                    $bIsStatic = true;
                    break;
                }
            }
        }

        return $bIsStatic;
    }

    /**
     * Test for local resource.
     *
     * @var string $sResource
     *
     * @return bool - true if the resource is in local
     */
    protected function isLocalResource($sResource)
    {
        $bIsLocal = false;
        if (('.' === $sResource['0'] || '/' === $sResource['0']) && '/' != $sResource['1']) { // filter protocoll less urls
            $bIsLocal = true;
        } else {
            $sResource = str_replace('http://', '', $sResource);
            $sResource = str_replace('https://', '', $sResource);
            if (substr($sResource, 0, strlen($_SERVER['HTTP_HOST'])) === $_SERVER['HTTP_HOST']) {
                $bIsLocal = true;
            }
        }

        return $bIsLocal;
    }

    private function hasResourceCollectionWriteProcessRunning(): bool
    {
        return file_exists($this->assetPath.'/lock.tmp');
    }

    private function setResourceCollectionWriteProcessRunning(bool $state): bool
    {
        if (true === $state) {
            return false === file_exists($this->assetPath.'/lock.tmp') && touch($this->assetPath.'/lock.tmp');
        }

        return true === file_exists($this->assetPath.'/lock.tmp') && unlink($this->assetPath.'/lock.tmp');
    }
}

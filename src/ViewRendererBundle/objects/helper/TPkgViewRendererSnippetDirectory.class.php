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
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\ViewRenderer\Interfaces\ThemeServiceInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class TPkgViewRendererSnippetDirectory implements TPkgViewRendererSnippetDirectoryInterface
{
    /**
     * @var string
     */
    public static $CSSSNIPPET = '<link rel="stylesheet" href="{{}}" type="text/css" />';
    /**
     * @var string
     */
    public static $JSSNIPPET = '<script src="{{}}" type="text/javascript"></script>';

    const PATH_MODULES = 'webModules';
    const PATH_OBJECTVIEWS = 'objectviews';
    const PATH_LAYOUTS = 'layoutTemplates';

    /**
     * @var PortalDomainServiceInterface
     */
    private $portalDomainService;
    /**
     * @var RequestInfoServiceInterface
     */
    private $requestInfoService;
    /**
     * @var KernelInterface
     */
    private $kernel;
    /**
     * @var ThemeServiceInterface
     */
    private $themeService;

    public function __construct(
        PortalDomainServiceInterface $portalDomainService,
        RequestInfoServiceInterface $requestInfoService,
        KernelInterface $kernel,
        ThemeServiceInterface $themeService
    ) {
        $this->requestInfoService = $requestInfoService;
        $this->kernel = $kernel;
        $this->themeService = $themeService;
        $this->portalDomainService = $portalDomainService;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourcesForSnippetPackage($sSnippetPath)
    {
        return $this->doGetResourcesForSnippetPackage(ltrim($sSnippetPath, '/'));
    }

    /**
     * Function to get the resources utilizing an array, that keeps track of the already included packages
     * to break possible circular references.
     *
     * @param string $sSnippetPath
     * @param array  $aUsedPackages
     *
     * @return array
     */
    private function doGetResourcesForSnippetPackage($sSnippetPath, array $aUsedPackages = array())
    {
        static $aCachedResources = array();

        if (isset($aCachedResources[$sSnippetPath])) {
            return $aCachedResources[$sSnippetPath];
        }

        // break circular references
        if (in_array($sSnippetPath, $aUsedPackages)) {
            return array();
        }
        $aUsedPackages[] = $sSnippetPath;

        $cache = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.cache');
        $key = array(
            'class' => __CLASS__,
            'method' => 'doGetResourcesForSnippetPackage',
            'sSnippetPath' => $sSnippetPath,
        );

        $sKey = $cache->getKey($key, true);
        $aIncludes = $cache->get($sKey);
        if (null !== $aIncludes) {
            $aCachedResources[$sSnippetPath] = $aIncludes;

            return $aCachedResources[$sSnippetPath];
        }

        $aIncludes = array();

        static $locator = null;
        if (null === $locator) {
            $aConfigDirectories = $this->getBasePaths();
            $locator = new Symfony\Component\Config\FileLocator(array_reverse($aConfigDirectories));
        }
        try {
            $searchFor = '' === $sSnippetPath ? 'config.yml' : $sSnippetPath.'/config.yml';
            $sConfigFile = $locator->locate($searchFor);
            if (!is_file($sConfigFile)) {
                throw new InvalidArgumentException('File not found: '.$sConfigFile);
            }
            $aConfig = Symfony\Component\Yaml\Yaml::parse(file_get_contents($sConfigFile));

            if (isset($aConfig['include'])) {
                foreach ($aConfig['include'] as $sUsePackage) {
                    $aIncludes = array_merge($aIncludes, $this->doGetResourcesForSnippetPackage($sUsePackage, $aUsedPackages));
                }
            }

            if (isset($aConfig['css'])) {
                foreach ($aConfig['css'] as $sCssFile) {
                    $sResource = str_replace('{{}}', TGlobal::GetStaticURL($sCssFile), self::$CSSSNIPPET);
                    $aIncludes[] = $sResource;
                }
            }
            $oGlobal = TGlobal::instance();
            if (true === isset($aConfig['js']) && false === $oGlobal->isFrontendJSDisabled()) {
                foreach ($aConfig['js'] as $sJSFile) {
                    $sResource = str_replace('{{}}', TGlobal::GetStaticURL($sJSFile), self::$JSSNIPPET);
                    $aIncludes[] = $sResource;
                }
            }
        } catch (InvalidArgumentException $e) {
            // file not found. continue happily.
        }

        $cache->set($sKey, $aIncludes, array());

        $aCachedResources[$sSnippetPath] = $aIncludes;

        return $aIncludes;
    }

    /**
     * {@inheritdoc}
     */
    public function getSnippetList($aDirTree, $sActiveRelativePath)
    {
        if (true === empty($sActiveRelativePath)) {
            return $this->getAllSnippets($aDirTree);
        }
        $aPathParts = explode('/', $sActiveRelativePath);
        $aActiveDir = $aDirTree;
        foreach ($aPathParts as $sPath) {
            $aActiveDir = $aActiveDir[$sPath];
        }

        $aActiveSnippets = array();
        if (false === is_array($aActiveDir)) {
            $aActiveSnippets[$aActiveDir->sSnippetName] = $aActiveDir;
        } else {
            foreach ($aActiveDir as $sKey => $aDirContent) {
                if (false === is_array($aDirContent)) {
                    $aActiveSnippets[$sKey] = $aDirContent;
                }
            }
        }

        return $aActiveSnippets;
    }

    /**
     * {@inheritdoc}
     */
    public function getSnippetBaseDirectory()
    {
        if (true === $this->requestInfoService->isBackendMode()) {
            return CMS_SNIPPET_PATH.'-cms';
        }

        return CMS_SNIPPET_PATH;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTree($oPortal = null, $snippetPath = null)
    {
        $aTypeList = $this->getBasePaths($oPortal, $snippetPath);
        $aDirTree = array();
        foreach ($aTypeList as $sType) {
            if (false === $sType) {
                continue;
            }
            $aDirTree = $this->getConfigTreeHelper($aDirTree, $sType, '');
        }

        return $aDirTree;
    }

    /**
     * {@inheritdoc}
     */
    public function getDirTree($bWithDummyData = false, $oPortal = null)
    {
        $aTypeList = $this->getBasePaths($oPortal);
        $aDirTree = array();
        foreach ($aTypeList as $sType) {
            if (false === $sType) {
                continue;
            }
            $aDirTree = $this->getDirTreeHelper($aDirTree, $sType, '', $bWithDummyData);
        }

        return $aDirTree;
    }

    /**
     * @param array       $aTree
     * @param string      $sType
     * @param string|null $sRootPath
     *
     * @return array
     */
    private function getConfigTreeHelper(array $aTree = array(), $sType = _CMS_CORE, $sRootPath = null)
    {
        if (null === $sRootPath) {
            $sRootPath = $this->getSnippetBaseDirectory();
        }
        $sPath = empty($sRootPath) ? $sType : $sType.'/'.$sRootPath;
        if (false === is_dir($sPath)) {
            return $aTree;
        }

        $d = dir($sPath);
        while (false !== ($entry = $d->read())) {
            if ('.' === $entry || '..' === $entry || '.' === substr($entry, 0, 1)) {
                continue;
            }
            if ('config.yml' === $entry) {
                $aTree[$entry] = $sPath.'/'.$entry;
            } elseif (is_dir($sPath.'/'.$entry)) {
                if (isset($aTree[$entry])) {
                    $aTree[$entry] = $this->getConfigTreeHelper($aTree[$entry], $sType, $sRootPath.'/'.$entry);
                } else {
                    $aTree[$entry] = $this->getConfigTreeHelper(array(), $sType, $sRootPath.'/'.$entry);
                }
            }
        }
        $d->close();
        ksort($aTree);

        return $aTree;
    }

    /**
     * @param array       $aTree
     * @param string      $sType
     * @param string      $sRootPath
     * @param bool        $bWithDummyData - include the dummy data files
     *
     * @return array
     */
    private function getDirTreeHelper($aTree = array(), $sType = _CMS_CORE, $sRootPath = null, $bWithDummyData = false)
    {
        if (null === $sRootPath) {
            $sRootPath = '';
        }
        $sPath = $sType.'/'.$sRootPath;
        if (false === is_dir($sPath)) {
            return $aTree;
        }

        $sRelativePath = $sRootPath;
        if (false === $sRelativePath) {
            $sRelativePath = '';
        }
        $d = dir($sPath);
        while (false !== ($entry = $d->read())) {
            if ('.' === $entry || '..' === $entry || '.' === substr($entry, 0, 1)) {
                continue;
            }
            if ('.html.twig' === substr($entry, -10) || '.txt.twig' === substr($entry, -9)) {
                $aTree[$entry] = $this->getSnippetObject($sType, $sRelativePath, $entry, $bWithDummyData);
            } elseif (is_dir($sPath.'/'.$entry)) {
                if (isset($aTree[$entry])) {
                    $aTree[$entry] = $this->getDirTreeHelper($aTree[$entry], $sType, $sRootPath.'/'.$entry, $bWithDummyData);
                } else {
                    $aTree[$entry] = $this->getDirTreeHelper(array(), $sType, $sRootPath.'/'.$entry, $bWithDummyData);
                }
            }
        }
        $d->close();

        return $aTree;
    }

    /**
     * @param string $sType
     * @param string $sRelativePath
     * @param string $sSnippetName
     * @param bool   $bWithDummyData
     *
     * @return TPkgViewRendererSnippetGalleryItem
     *
     * @throws ErrorException
     */
    protected function getSnippetObject($sType, $sRelativePath, $sSnippetName, $bWithDummyData = false)
    {
        $oSnippet = new TPkgViewRendererSnippetGalleryItem();
        $oSnippet->sSnippetName = $sSnippetName;
        $oSnippet->sRelativePath = $sRelativePath;
        $oSnippet->sType = $sType;

        $oDummyData = new TPkgViewRendererSnippetDummyData();

        $sFileName = substr($sSnippetName, 0, -5); // cut .twig extension

        $sPathRelativeToSnippetsFolder = '/'.$sRelativePath.'/'.$sFileName.'.dummy.php';
        $sDummyFileFullPath = $oDummyData->getDummyDataFilePath($sPathRelativeToSnippetsFolder, null);

        if ('' === $sDummyFileFullPath) {
            // dummy file found nowhere try again the old style filenames without .html sub extension
            $aFileNameParts = explode('.', $sFileName);
            array_pop($aFileNameParts);
            $sFileName = implode('.', $aFileNameParts);
        }

        if ($bWithDummyData) {
            $sDummyDataFile = '/'.$sRelativePath.'/'.$sFileName.'.dummy.php';

            $oDummyData->addDummyDataFromFile($sDummyDataFile, true);
            $oSnippet->oDummyData = $oDummyData;
        }

        return $oSnippet;
    }

    /**
     * @param array|TPkgViewRendererSnippetGalleryItem $mContent
     *
     * @return array
     */
    private function getAllSnippets($mContent)
    {
        $aSnippets = array();

        if (is_array($mContent)) {
            foreach ($mContent as $mSubContent) {
                $aSnippets = array_merge($aSnippets, $this->getAllSnippets($mSubContent));
            }
        } elseif ($mContent instanceof TPkgViewRendererSnippetGalleryItem) {
            $aSnippets[$mContent->sRelativePath.'/'.$mContent->sSnippetName] = $mContent;
        }

        return $aSnippets;
    }

    /**
     * {@inheritdoc}
     */
    public function getBasePaths($oPortal = null, $sBaseDirectory = null)
    {
        if (null === $sBaseDirectory) {
            $sBaseDirectory = $this->getSnippetBaseDirectory();
        }

        if (null === $oPortal) {
            $oPortal = $this->portalDomainService->getActivePortal();
        }

        $snippetChain = [];
        $theme = $this->themeService->getTheme($oPortal);
        if (null !== $theme) {
            $snippetChain = $theme->getSnippetChainAsArray();
        }

        // TODO quite unfortunate here that there is no "this is backend" (only the fact that is has no portal and thus no snippet chain paths)

        $aBasePaths = [];
        if (false !== CHAMELEON_PATH_THEMES && count($snippetChain) > 0) {
            foreach ($snippetChain as $element) {
                $element = TCMSUserInput::FilterValue($element, 'TCMSUserInput_Raw;TCMSUserInput/filter;Core');
                $path = $this->getVerifiedPath($element, $sBaseDirectory);
                if (null !== $path) {
                    $aBasePaths[] = $path;
                }
            }
        } else {
            // NOTE this is now a very old fallback; normally everything should have a theme and a snippet chain > 0

            $aBasePaths = array(realpath(PATH_CORE_VIEWS.'/'.$sBaseDirectory));
            $aCandidates = array(_CMS_CUSTOMER_CORE, _CMS_CUSTOM_CORE);
            foreach ($aCandidates as $sCandidate) {
                if (false !== $sCandidate && true === is_dir($sCandidate.'/'.$sBaseDirectory)) {
                    $aBasePaths[] = realpath($sCandidate.'/'.$sBaseDirectory);
                }
            }
        }

        return $aBasePaths;
    }

    /**
     * @param string $element
     * @param string $baseDirectory
     *
     * @return string|null
     */
    private function getVerifiedPath($element, $baseDirectory)
    {
        if ('' === $element) {
            return null;
        }

        if ('@' === $element[0]) {
            try {
                $path = $this->kernel->locateResource($element).'/'.$baseDirectory;
            } catch (InvalidArgumentException $e) {
                return null;
            } catch (RuntimeException $e) {
                return null;
            }
        } else {
            $path = CHAMELEON_PATH_THEMES.'/'.$element.'/'.$baseDirectory;
        }

        if (false === is_dir($path)) {
            return null;
        }

        return realpath($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getBasePathsFromInstance($oPortal = null, $sBaseDirectory = null)
    {
        return $this->getBasePaths($oPortal, $sBaseDirectory);
    }
}

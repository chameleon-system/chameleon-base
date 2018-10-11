<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ViewRendererBundle\objects;

use CssMin;
use Exception;
use TdbCmsPortal;
use TPkgViewRendererSnippetResourceCollector;
use ViewRenderException;

class TPkgViewRendererLessCompiler
{
    /**
     * @var string
     */
    private $cssDir;

    public function __construct(string $cssDir)
    {
        $this->cssDir = $cssDir;
    }

    /**
     * local path to less directory - this is where the chameleon_?.css files live.
     *
     * @return string
     */
    public function getLocalPathToCompiledLess()
    {
        return $this->cssDir;
    }

    /**
     * @return string
     */
    public function getLocalPathToCachedLess()
    {
        return $this->getLocalPathToCompiledLess().'/cached';
    }

    /**
     * @param TdbCmsPortal|null $portal
     *
     * @return string
     */
    public function getCompiledCssUrl(TdbCmsPortal $portal = null)
    {
        $path = $this->getLessDirUrlPath();
        $filename = $this->getCompiledCssFilename($portal);
        $versionSuffix = '?'.ENABLE_EXTERNAL_RESOURCE_COLLECTION_REFRESH_PREFIX;

        return $path.'/'.$filename.$versionSuffix;
    }

    /**
     * @param TdbCmsPortal|null $portal
     *
     * @return string
     */
    protected function getCompiledCssFilename(TdbCmsPortal $portal = null)
    {
        $fileSuffix = (null === $portal) ? '' : $portal->getFileSuffix();

        return 'chameleon'.$fileSuffix.'.css';
    }

    /**
     * @see getCompiledCssFilename
     *
     * @return string
     *
     * @deprecated since 6.2.0 - the pattern is handled in routing.yml. To modify the pattern, replace the according routing file in the backend's routing config.
     */
    public function getCompiledCssFilenamePattern()
    {
        return '/(.*)chameleon_([0-9]{1,16}).css/';
        // when changing the pattern, be aware that clients depend on the
        // fact that the portal ID is match number 2.
    }

    /**
     * @param TdbCmsPortal $portal
     *
     * @return string[] the less files relative to the web root
     */
    protected function getLessFilesFromSnippetsAndTheme($portal)
    {
        $resourceCollector = new TPkgViewRendererSnippetResourceCollector();
        $resources = $resourceCollector->getLessResources($portal, CMS_SNIPPET_PATH);

        $theme = null;
        if (is_object($portal)) {
            $theme = $portal->GetFieldPkgCmsTheme();
        }

        if (!$theme || empty($theme->fieldLessFile)) {
            $lessFileToImport = '/assets/less/chameleon.less';
        } else {
            $lessFileToImport = $theme->fieldLessFile;
        }

        if (!file_exists(realpath(PATH_WEB.'/'.$lessFileToImport))) {
            throw new \InvalidArgumentException("In the theme, the less file '$lessFileToImport' is configured to be imported. However, the file could not be found.");
        }

        // Make sure "chameleon.less" (and thus bootstrap) is first - so that it can be overwritten by less in config.ymls
        array_unshift($resources, $lessFileToImport);

        return $resources;
    }

    /**
     * @param TdbCmsPortal $portal
     * @param bool|null    $minifyCss
     *
     * @return string
     *
     * @throws ViewRenderException
     */
    public function getGeneratedCssForPortal($portal, $minifyCss = null)
    {
        if (false === class_exists('Less_Cache') && false === class_exists('lessc')) {
            throw new ViewRenderException(
                'You need to install oyejorge/less.php or leafo/lessphp in an appropriate version. See composer.json in chameleon-system/pkgviewrenderer in the suggest section.'
            );
        }

        if (null === $minifyCss) {
            $minifyCss = (true === $this->getMinifyParameter());
        }

        $lessFiles = $this->getLessFilesFromSnippetsAndTheme($portal);

        if (true === class_exists('Less_Cache')) {
            // Workaround (#37218): Remove trailing slash from document root; it will confuse less.php's "path interpretation"
            $originalDocumentRoot = $_SERVER['DOCUMENT_ROOT'];
            $_SERVER['DOCUMENT_ROOT'] = rtrim($_SERVER['DOCUMENT_ROOT'], '/');

            $lessPortalIdentifier = $portal->getFileSuffix();

            $cachedLessDir = $this->getLocalPathToCachedLess();
            $this->createDirectoryIfNeeded($cachedLessDir);

            $options = _DEVELOPMENT_MODE ? array(
                'sourceMap' => true,
                'sourceMapWriteTo' => $this->getLocalPathToCompiledLess().'/lessSourceMap_'.$lessPortalIdentifier.'.map',
                'sourceMapURL' => $this->getLessDirUrlPath().'/lessSourceMap_'.$lessPortalIdentifier.'.map',
            ) : array();

            $options['import_dirs'] = array(PATH_WEB => '/');
            $options['cache_dir'] = $cachedLessDir;
            $options['compress'] = $minifyCss;

            $filesForLessParsing = array();
            foreach ($lessFiles as $lessFile) {
                $filesForLessParsing[PATH_WEB.$lessFile] = '/';
            }

            \Less_Cache::SetCacheDir($cachedLessDir);
            try {
                $cssFile = \Less_Cache::Get($filesForLessParsing, $options);
            } catch (Exception $exc) {
                if (false !== strpos($exc->getMessage(), 'stat failed')) {
                    // Consider this as a 'File removed! Halp!' and clear the cache and try again
                    array_map('unlink', glob($this->getLocalPathToCachedLess().'/*'));

                    $cssFile = \Less_Cache::Get($filesForLessParsing, $options);
                } else {
                    throw new ViewRenderException('Exception during less compile', 0, $exc);
                }
            }

            $absoluteCssFilepath = $this->getLocalPathToCachedLess().'/'.$cssFile;

            $_SERVER['DOCUMENT_ROOT'] = $originalDocumentRoot;

            return file_get_contents($absoluteCssFilepath);
        }

        // NOTE also remove Cssmin when this is removed?

        return $this->getCssFromLessC($lessFiles, $minifyCss);
    }

    /**
     * @deprecated since 6.2.0 - lessc (leafo) should not be used anymore; support will be removed
     *
     * @param string[] $lessFiles
     * @param bool     $minifyCss
     *
     * @return string
     */
    private function getCssFromLessC($lessFiles, $minifyCss)
    {
        $less = new \lessc();
        $less->addImportDir($_SERVER['DOCUMENT_ROOT']);
        $generatedCss = $less->compile($this->getImportStatementsForFiles($lessFiles));

        if ($minifyCss) {
            $generatedCss = CssMin::minify($generatedCss);
        }

        return $generatedCss;
    }

    /**
     * @param string[] $lessFiles
     *
     * @return array|string
     */
    private function getImportStatementsForFiles($lessFiles)
    {
        $importStatements = array();
        $pattern = '@import "{{}}";';
        foreach ($lessFiles as $file) {
            $importStatements[] = str_replace('{{}}', $file, $pattern);
        }

        $importStatements = implode("\n", $importStatements);

        return $importStatements;
    }

    /**
     * @param string       $css
     * @param TdbCmsPortal $portal
     *
     * @return bool
     */
    public function writeCssFileForPortal($css, $portal)
    {
        $lessDir = $this->getLocalPathToCompiledLess();

        try {
            $this->createDirectoryIfNeeded($lessDir);
        } catch (ViewRenderException $exception) {
            return false;
        }

        $filename = $this->getCompiledCssFilename($portal);
        $targetPath = $lessDir.'/'.$filename;

        return file_put_contents($targetPath, $css);
    }

    /**
     * returns the URL path to the less directory.
     *
     * @return string
     */
    protected function getLessDirUrlPath()
    {
        $sOutboxURL = URL_OUTBOX;

        // remove the domain an protocol
        $sOutboxURL = str_replace('http://', '', $sOutboxURL);
        $sOutboxURL = str_replace('https://', '', $sOutboxURL);
        $sOutboxURL = substr($sOutboxURL, strpos($sOutboxURL, '/'));

        if ('/' == substr($sOutboxURL, -1)) {
            $sOutboxURL = substr($sOutboxURL, 0, -1);
        }

        $sOutboxURL .= '/static/less';

        return $sOutboxURL;
    }

    /**
     * @return mixed
     */
    private function getMinifyParameter()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::getParameter(
            'chameleon_system_core.resources.enable_external_resource_collection_minify');
    }

    /**
     * @throws ViewRenderException
     */
    private function createDirectoryIfNeeded(string $dir): void
    {
        if (false === \is_dir($dir)) {
            if (false === \mkdir($dir, 0777, true) && false === \is_dir($dir)) {
                throw new ViewRenderException(sprintf('Cannot create directory %s', $dir));
            }
        }
    }
}

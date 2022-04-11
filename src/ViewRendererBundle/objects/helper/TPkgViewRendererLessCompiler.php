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

use ChameleonSystem\ViewRenderer\Interfaces\ThemeServiceInterface;
use Exception;
use MatthiasMullie\Minify\CSS;
use TdbCmsPortal;
use TPkgViewRendererSnippetResourceCollector;
use ViewRenderException;

class TPkgViewRendererLessCompiler
{
    /**
     * @var string
     */
    private $cssDir;

    /**
     * @var string
     */
    private $resourceCollectionRefreshPrefix;

    /**
     * @var array
     */
    private $additionalVariables = [];

    /**
     * @var ThemeServiceInterface
     */
    private $themeService;

    public function __construct(string $cssDirRelativeToWebRoot, string $resourceCollectionRefreshPrefix, ThemeServiceInterface $themeService)
    {
        $this->cssDir = trim($cssDirRelativeToWebRoot, '/');
        $this->resourceCollectionRefreshPrefix = $resourceCollectionRefreshPrefix;
        $this->themeService = $themeService;
    }

    /**
     * @param array $variables - key-value pairs that are passed to the less compiler.
     */
    public function addAdditionalVariables(array $variables): void
    {
        $this->additionalVariables = \array_merge($this->additionalVariables, $variables);
    }

    /**
     * Local path to less directory - this is where the chameleon_?.css files live.
     *
     * @return string - absolute path (guaranteed to be below PATH_WEB) without trailing slash
     */
    public function getLocalPathToCompiledLess()
    {
        return PATH_WEB.'/'.$this->cssDir;
    }

    /**
     * @return string - absolute path (guaranteed to be below PATH_WEB) without trailing slash
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
        $versionSuffix = '?'.$this->resourceCollectionRefreshPrefix;

        return $path.'/'.$filename.$versionSuffix;
    }

    /**
     * @return string - the path part of the URL to the less directory; including a leading slash
     */
    protected function getLessDirUrlPath()
    {
        return '/'.$this->cssDir;
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
     * @return string - the path pattern to the generated CSS file, relative to PATH_WEB, without leading slash
     */
    public function getCssRoutingPattern(): string
    {
        return $this->cssDir.'/'.'chameleon_{portalId}.css';
    }

    /**
     * @return string - the file part for route generation; without a leading slash
     *
     * will be deprecated in 6.3.0 - use getCssRoutingPattern() which includes the relative path
     */
    public function getCompiledCssFilenameRoutingPattern(): string
    {
        return 'chameleon_{portalId}.css';
    }

    /**
     * @see getCompiledCssFilename
     *
     * @return string
     *
     * @deprecated since 6.2.0 - handled in GenerateCssRouteCollectionGenerator
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
        $theme = $this->themeService->getTheme($portal);

        $resourceCollector = new TPkgViewRendererSnippetResourceCollector();
        $resources = $resourceCollector->getLessResources($portal, CMS_SNIPPET_PATH);

        if (null === $theme || empty($theme->fieldLessFile)) {
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
        if (false === class_exists('Less_Parser') && false === class_exists('lessc')) {
            throw new ViewRenderException(
                'No Less support found. You need to install wikimedia/less.php.'
            );
        }

        if (null === $minifyCss) {
            $minifyCss = (true === $this->getMinifyParameter());
        }

        $lessFiles = $this->getLessFilesFromSnippetsAndTheme($portal);

        if (true === class_exists('Less_Parser')) {
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

            $css = '';

            if (_DEVELOPMENT_MODE) {
                \Less_Cache::SetCacheDir($cachedLessDir);
                try {
                    $cssFile = \Less_Cache::Get($filesForLessParsing, $options, $this->additionalVariables);
                } catch (Exception $exc) {
                    if (false !== strpos($exc->getMessage(), 'stat failed')) {
                        // Consider this as a 'File removed! Halp!' and clear the cache and try again
                        array_map('unlink', glob($cachedLessDir.'/*'));

                        $cssFile = \Less_Cache::Get($filesForLessParsing, $options, $this->additionalVariables);
                    } else {
                        throw new ViewRenderException('Exception during less compile', 0, $exc);
                    }
                }

                $absoluteCssFilepath = $cachedLessDir.'/'.$cssFile;

                $css = file_get_contents($absoluteCssFilepath);
            } else {
                try {
                    $parser = new \Less_Parser($options);
                    if (\count($this->additionalVariables) > 0) {
                        $parser->parse(\Less_Parser::serializeVars($this->additionalVariables));
                    }

                    foreach ($filesForLessParsing as $file => $root) {
                        $parser->parseFile($file, $root);
                    }

                    $css = $parser->getCss();
                } catch (Exception $exc) {
                    throw new ViewRenderException('Exception during less compile', 0, $exc);
                }
            }

            $_SERVER['DOCUMENT_ROOT'] = $originalDocumentRoot;

            return $css;
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
            $minifier = new CSS();
            $minifier->add($generatedCss);

            $generatedCss = $minifier->minify();
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
     * @return false|int
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
     * @return mixed
     */
    private function getMinifyParameter()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::getParameter(
            'chameleon_system_core.resources.enable_external_resource_collection_minify');
    }

    /**
     * @param string $dir
     *
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

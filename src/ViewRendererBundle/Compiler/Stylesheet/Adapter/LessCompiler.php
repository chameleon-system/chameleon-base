<?php

namespace ChameleonSystem\ViewRendererBundle\Compiler\Stylesheet\Adapter;

use TdbCmsPortal;

class LessCompiler extends AbstractCompilerAdapter
{
    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'less';
    }

    /**
     * {@inheritdoc}
     */
    public function getLocalPathToCompiled(): string
    {
        return PATH_USER_CMS_PUBLIC.'/outbox/static/less';
    }

    /**
     * {@inheritdoc}
     *
     * @todo refactoring
     */
    public function getGeneratedCssForPortal(TdbCmsPortal $portal, bool $minifyCss = false): string
    {
        $snippetImportStatements = $this->getImportStatementsForSnippetResources($portal);
        try {
            $chameleonLess = $this->generateChameleonLess($portal, $snippetImportStatements);
        } catch (\InvalidArgumentException $e) {
            throw new \ViewRenderException("Error while trying to generate Chameleon CSS: ".$e->getMessage(), 0, $e);
        }

        $lessPortalIdentifier = $portal->getFileSuffix();

        if (class_exists('Less_Parser')) {
            // Workaround (#37218): Remove trailing slash from document root; it will confuse less.php's "path interpretation"
            $originalDocumentRoot = $_SERVER['DOCUMENT_ROOT'];
            $_SERVER['DOCUMENT_ROOT'] = rtrim($_SERVER['DOCUMENT_ROOT'], '/');

            $options = _DEVELOPMENT_MODE ? array(
                'sourceMap' => true,
                'sourceMapWriteTo' => $this->getLocalPathToCompiled().'/lessSourceMap_'.$lessPortalIdentifier.'.map',
                'sourceMapURL' => $this->getDirUrlPath().'/lessSourceMap_'.$lessPortalIdentifier.'.map',
            ) : array();

            if ($minifyCss) {
                $options['compress'] = true;
            }

            $less = new \Less_Parser($options);
            $less->SetImportDirs(array($_SERVER['DOCUMENT_ROOT'] => '/'));
            $less->parse($chameleonLess);
            $generatedCss = $less->getCss();

            $_SERVER['DOCUMENT_ROOT'] = $originalDocumentRoot;
        } elseif (class_exists('lessc')) {
            $less = new \lessc();
            $less->addImportDir($_SERVER['DOCUMENT_ROOT']);
            $generatedCss = $less->compile($chameleonLess);

            if ($minifyCss) {
                $generatedCss = \CssMin::minify($generatedCss);
            }
        } else {
            throw new \ViewRenderException(
                "You need to install lessphp or Less_Parser (oyejorge/less.php) in an appropriate version. See composer.json in chameleon-system/pkgviewrenderer in the suggest section."
            );
        }

        return $generatedCss;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDirUrlPath(): string
    {
        $sOutboxURL = URL_OUTBOX;

        // remove the domain an protocol
        $sOutboxURL = str_replace('http://', '', $sOutboxURL);
        $sOutboxURL = str_replace('https://', '', $sOutboxURL);
        $sOutboxURL = substr($sOutboxURL, strpos($sOutboxURL, '/'));

        if ('/' === substr($sOutboxURL, -1)) {
            $sOutboxURL = substr($sOutboxURL, 0, -1);
        }

        $sOutboxURL .= '/static/less';

        return $sOutboxURL;
    }

    private function generateChameleonLess(TdbCmsPortal $portal, string $snippetIncludes = ''): string
    {
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
            throw new \InvalidArgumentException(
                "In the theme, the less file '$lessFileToImport' is configured to be imported. However, the file could not be found."
            );
        }

        $lessFileContent = '@import "'.$lessFileToImport.'";';
        $lessFileContent .= "\n".$snippetIncludes;

        return $lessFileContent;
    }
}

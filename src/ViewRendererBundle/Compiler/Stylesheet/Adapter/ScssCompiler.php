<?php

namespace ChameleonSystem\ViewRendererBundle\Compiler\Stylesheet\Adapter;

if (file_exists(PATH_VENDORS.'/scssphp/scss.inc.php')) {
    require_once PATH_VENDORS.'/scssphp/scss.inc.php';
}

use Leafo\ScssPhp\Compiler;
use Leafo\ScssPhp\Formatter\Compact;
use Leafo\ScssPhp\Formatter\Compressed;
use TdbCmsPortal;

class ScssCompiler extends AbstractCompilerAdapter
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'scss';
    }

    /**
     * {@inheritdoc}
     */
    public function getLocalPathToCompiled()
    {
        return PATH_USER_CMS_PUBLIC.'/outbox/static/scss';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDirUrlPath()
    {
        $sOutboxURL = URL_OUTBOX;

        // remove the domain an protocol
        $sOutboxURL = str_replace('http://', '', $sOutboxURL);
        $sOutboxURL = str_replace('https://', '', $sOutboxURL);
        $sOutboxURL = substr($sOutboxURL, strpos($sOutboxURL, '/'));

        if ('/' === substr($sOutboxURL, -1)) {
            $sOutboxURL = substr($sOutboxURL, 0, -1);
        }

        $sOutboxURL .= '/static/scss';

        return $sOutboxURL;
    }

    /**
     * {@inheritdoc}
     */
    public function getGeneratedCssForPortal(TdbCmsPortal $portal, $minifyCss = false)
    {
        $snippetImportStatements = $this->getImportStatementsForSnippetResources($portal);
        try {
            $chameleonScss = $this->generateChameleonScss($portal, $snippetImportStatements);
        } catch (\InvalidArgumentException $e) {
            throw new \ViewRenderException("Error while trying to generate Chameleon CSS: ".$e->getMessage(), 0, $e);
        }

        if (!class_exists('\Leafo\ScssPhp\Compiler')) {
            throw new \ViewRenderException(
                "You need to install scssphp (leafo/scssphp) in an appropriate version. See composer.json in chameleon-system/pkgviewrenderer in the suggest section."
           );
        }

        $path = $_SERVER['DOCUMENT_ROOT'];

        if (DIRECTORY_SEPARATOR !== substr($path, -1)) {
            $path .= DIRECTORY_SEPARATOR;
        }

        $scss = new Compiler();
        $scss->addImportPath($path);

        if ($minifyCss) {
            $scss->setFormatter(Compressed::class);
        } else {
            $scss->setFormatter(Compact::class);
        }

        return $scss->compile($chameleonScss);
    }

    /**
     * @param TdbCmsPortal $portal
     * @param string $snippetIncludes
     * @return string
     */
    private function generateChameleonScss(TdbCmsPortal $portal, $snippetIncludes = '')
    {
        $lessFileToImport = '/assets/scss/chameleon.scss';

        if (null !== $portal && null !== ($theme = $portal->GetFieldPkgCmsTheme()) && !empty($theme->fieldLessFile)) {
            $lessFileToImport = $theme->fieldLessFile;
        }

        if (!file_exists(realpath(PATH_WEB.'/'.$lessFileToImport))) {
            throw new \InvalidArgumentException(
                "In the theme, the scss file '$lessFileToImport' is configured to be imported. However, the file could not be found."
            );
        }

        $lessFileContent = '@import "'.$lessFileToImport.'";';
        $lessFileContent .= "\n".$snippetIncludes;

        return $lessFileContent;
    }
}

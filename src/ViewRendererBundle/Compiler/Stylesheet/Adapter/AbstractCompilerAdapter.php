<?php

namespace ChameleonSystem\ViewRendererBundle\Compiler\Stylesheet\Adapter;

use TPkgViewRendererSnippetResourceCollector;
use TdbCmsPortal;

abstract class AbstractCompilerAdapter implements CompilerAdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public function getLocalPathToCompiledCssFileForPortal(TdbCmsPortal $portal): string
    {
        $lessDir = $this->getLocalPathToCompiled();
        $filename = $this->getCompiledCssFilename($portal);

        return $lessDir.'/'.$filename;
    }

    /**
     * {@inheritdoc}
     */
    public function getCompiledCssUrl(TdbCmsPortal $portal = null): string
    {
        $path = $this->getDirUrlPath();
        $filename = $this->getCompiledCssFilename($portal);
        $versionSuffix = '?'.ENABLE_EXTERNAL_RESOURCE_COLLECTION_REFRESH_PREFIX;

        return $path.'/'.$filename.$versionSuffix;
    }

    /**
     * {@inheritdoc}
     */
    private function getCompiledCssFilename(TdbCmsPortal $portal = null): string
    {
        $fileSuffix = null === $portal ? '' : $portal->getFileSuffix();

        return sprintf('chameleon%s.css', $fileSuffix);
    }

    /**
     * {@inheritdoc}
     */
    public function getCompiledCssFilenamePattern(): string
    {
        return '/(.*)chameleon_([0-9]{1,16}).css/';
    }

    /**
     * {@inheritdoc}
     *
     * @todo refactoring
     */
    public function writeCssFileForPortal($generatedCss, TdbCmsPortal $portal): bool
    {
        $filePath = $this->getLocalPathToCompiledCssFileForPortal($portal);

        $cacheFileWriteSuccess = false;
        if (!file_put_contents($filePath, $generatedCss)) {
            $lessDir = $this->getLocalPathToCompiled();
            if (!is_dir($lessDir)) {
                if (mkdir($lessDir, 0777, true)) {
                    if (file_put_contents($filePath, $generatedCss)) {
                        $cacheFileWriteSuccess = true;
                    }
                }
            }
        } else {
            $cacheFileWriteSuccess = true;
        }


        return $cacheFileWriteSuccess;
    }

    protected function getImportStatementsForSnippetResources(TdbCmsPortal $portal): string
    {
        $resourceCollector = new TPkgViewRendererSnippetResourceCollector();
        $resources = $resourceCollector->getLessResources($portal, CMS_SNIPPET_PATH);

        $importResources = array();
        $pattern = '@import "{{}}";';
        foreach ($resources as $resource) {
            $importResources[] = str_replace("{{}}", $resource, $pattern);
        }

        return implode("\n", $importResources);
    }

    abstract protected function getDirUrlPath(): string;
}

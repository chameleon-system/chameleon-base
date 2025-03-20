<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use Symfony\Component\Yaml\Yaml;

class TPkgViewRendererSnippetResourceCollectorEndPoint
{
    /**
     * @param TdbCmsPortal|null $oPortal
     * @param string|null $snippetPath - the base path (snippets/snippets-cms)
     *
     * @return array
     */
    public function getLessResources($oPortal = null, $snippetPath = null)
    {
        $aLessFiles = [];
        $oTree = $this->getViewRendererSnippetDirectory();
        $aSnippetTree = $oTree->getConfigTree($oPortal, $snippetPath);
        $aTmpResources = $this->getConfigsFromTree($aSnippetTree);
        foreach ($aTmpResources as $sPathToConfig) {
            $aConfig = Yaml::parse(\file_get_contents($sPathToConfig));
            if (isset($aConfig['less'])) {
                foreach ($aConfig['less'] as $sResource) {
                    $aLessFiles[$sResource] = true;
                }
            }
        }

        $aLessFiles = array_merge($aLessFiles, $this->getAdditionalLessResources($oPortal));

        return array_keys($aLessFiles);
    }

    /**
     * @param TdbCmsPortal $oPortal
     *
     * @return array
     */
    protected function getAdditionalLessResources($oPortal = null)
    {
        return [];
    }

    public function getResources(bool $bExcludeJS = false): array
    {
        $aResources = [];
        $oTree = $this->getViewRendererSnippetDirectory();
        $aSnippetTree = $oTree->getDirTree();
        $aTmpResources = $this->getResourcesFromTree($aSnippetTree, $bExcludeJS);
        $aResources['css'] = array_keys($aTmpResources['css']);
        $aResources['less'] = array_keys($aTmpResources['less']);
        $aResources['js'] = array_keys($aTmpResources['js']);

        $aResources['css'] = $this->getAsHTMLInclude($aResources['css'], 'css');
        $aResources['less'] = $this->getAsHTMLInclude($aResources['less'], 'less');
        $aResources['js'] = $this->getAsHTMLInclude($aResources['js'], 'js');
        if (true === $this->getRequestInfoService()->isFrontendJsDisabled()) {
            $aResources['nop'] = null;
        }

        return $aResources;
    }

    /**
     * @param array $aFileList
     * @param string $sType
     *
     * @return array
     *
     * @throws ErrorException
     */
    protected function getAsHTMLInclude($aFileList, $sType)
    {
        $bUseStaticUrlTransformation = true;
        switch ($sType) {
            case 'css':
                $sPattern = '<link rel="stylesheet" href="{{}}" type="text/css"/><!--#GLOBALRESOURCECOLLECTION#-->';
                break;
            case 'less':
                $sPattern = '@import "{{}}";';
                $bUseStaticUrlTransformation = false;
                break;
            case 'js':
                $sPattern = '<script src="{{}}" type="text/javascript"></script><!--#GLOBALRESOURCECOLLECTION#-->';
                break;
            default:
                throw new ErrorException("invalid type [{$sType}]", 0, E_USER_ERROR, __FILE__, __LINE__);
        }

        foreach ($aFileList as $sKey => $sFile) {
            if ($bUseStaticUrlTransformation) {
                $sFile = TGlobal::GetStaticURL($sFile);
            }
            $aFileList[$sKey] = str_replace('{{}}', $sFile, $sPattern);
        }

        return $aFileList;
    }

    /**
     * @param array $aSnippetTree
     *
     * @return array
     */
    protected function getConfigsFromTree($aSnippetTree)
    {
        $aResource = [];
        foreach (array_keys($aSnippetTree) as $sDirectory) {
            if (is_array($aSnippetTree[$sDirectory])) {
                $aTmpResource = $this->getConfigsFromTree($aSnippetTree[$sDirectory]);
                $aResource = array_merge($aResource, $aTmpResource);
            } else {
                $aResource[] = $aSnippetTree[$sDirectory];
            }
        }

        return $aResource;
    }

    /**
     * @param array $aSnippetTree
     * @param bool $bExcludeJS
     *
     * @return array
     */
    protected function getResourcesFromTree($aSnippetTree, $bExcludeJS = false)
    {
        $aResource = ['css' => [], 'js' => [], 'less' => []];
        foreach (array_keys($aSnippetTree) as $sDirectory) {
            if (is_array($aSnippetTree[$sDirectory])) {
                $aTmpResource = $this->getResourcesFromTree($aSnippetTree[$sDirectory], $bExcludeJS);
                $aResource['css'] = array_merge($aResource['css'], $aTmpResource['css']);
                $aResource['less'] = array_merge($aResource['less'], $aTmpResource['less']);
                $aResource['js'] = array_merge($aResource['js'], $aTmpResource['js']);
            } else {
                $aTmpResource = $this->getResourceFromSnippet($aSnippetTree[$sDirectory], $bExcludeJS);
                foreach ($aTmpResource['css'] as $sCSS) {
                    $aResource['css'][$sCSS] = true;
                }
                foreach ($aTmpResource['less'] as $sLESS) {
                    $aResource['less'][$sLESS] = true;
                }
                if (false === $bExcludeJS) {
                    foreach ($aTmpResource['js'] as $sJS) {
                        $aResource['js'][$sJS] = true;
                    }
                }
            }
        }

        return $aResource;
    }

    /**
     * @param bool $bExcludeJS
     *
     * @return array
     */
    protected function getResourceFromSnippet(TPkgViewRendererSnippetGalleryItem $oSnippetItem, $bExcludeJS = false)
    {
        $aResource = ['css' => [], 'js' => [], 'less' => []];
        $sFile = $oSnippetItem->sType.'/'.$this->getViewRendererSnippetDirectory()->getSnippetBaseDirectory().$oSnippetItem->sRelativePath.'/'.$oSnippetItem->sSnippetName;
        $sSource = file_get_contents($sFile);
        if (false !== $sSource) {
            $sPattern = '/\\{%\\s*?cmsresources\\s*?%\\}(.*?)\\{%\\s*?endcmsresources\\s*?%\\}/uis';
            $aTmpResource = [];
            $aResourceBlocks = [];
            if (0 < preg_match_all($sPattern, $sSource, $aTmpResource)) {
                $aResourceBlocks = $aTmpResource[1];
            }

            if (count($aResourceBlocks) > 0) {
                $oResourceHelper = new TPkgSnippetRenderer_ResourceHelper();
                $aResource = $oResourceHelper->getResourcesFromSource(implode("\n", $aResourceBlocks), $bExcludeJS);
            }
        }

        return $aResource;
    }

    private function getViewRendererSnippetDirectory(): TPkgViewRendererSnippetDirectoryInterface
    {
        return ServiceLocator::get('chameleon_system_view_renderer.snippet_directory');
    }

    private function getRequestInfoService(): RequestInfoServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.request_info_service');
    }
}

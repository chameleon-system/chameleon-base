<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgSnippetRendererLegacy extends PkgAbstractSnippetRenderer
{
    /**
     * Returns a new instance. The instance uses the given string as snippet source.
     * It is possible to optionally provide it with a path to a file containing the snippet code.
     *
     * @static
     *
     * @param string|TModelBase $sSource the snippet source (or path to a file containing it)
     * @param int $iSourceType
     *
     * @psalm-param IPkgSnippetRenderer::SOURCE_TYPE_* $iSourceType
     *
     * @return TPkgSnippetRenderer|TPkgSnippetRendererLegacy
     */
    public static function GetNewInstance($sSource, $iSourceType = IPkgSnippetRenderer::SOURCE_TYPE_STRING, ?IResourceHandler $oResourceHandler = null)
    {
        $oNewInstance = new self();
        $oNewInstance->InitializeSource($sSource, $iSourceType);

        return $oNewInstance;
    }

    /**
     * Renders the snippet and returns the rendered content.
     *
     * @return string - the rendered content
     */
    public function render()
    {
        $oViewRenderer = new TViewParser();
        $oViewRenderer->AddVarArray($this->getVars());
        $sPath = $this->getFilename();
        if (null !== $this->getSourceModule()) {
            $sPath = $this->getSourceModule()->viewTemplate;
        }

        return $oViewRenderer->RenderBackendModuleViewByFullPath($sPath);
    }
}

<?php

namespace ChameleonSystem\ViewRendererBundle\Compiler\Stylesheet;

use TdbCmsPortal;

interface CompilerInterface
{
    /**
     * @return string
     */
    public function getLocalPathToCompiled();

    /**
     * @param TdbCmsPortal|null $portal
     * @return mixed
     */
    public function getCompiledCssUrl(TdbCmsPortal $portal = null);

    /**
     * @param TdbCmsPortal $portal
     * @return string
     */
    public function getLocalPathToCompiledCssFileForPortal(TdbCmsPortal $portal);

    /**
     * @return string
     */
    public function getCompiledCssFilenamePattern();

    /**
     * @param TdbCmsPortal $portal
     * @param bool $minifyCss
     * @return string
     */
    public function getGeneratedCssForPortal(TdbCmsPortal $portal, $minifyCss = false);

    /**
     * @param string $generatedCss
     * @param $portal
     * @return bool
     */
    public function writeCssFileForPortal($generatedCss, TdbCmsPortal $portal);
}

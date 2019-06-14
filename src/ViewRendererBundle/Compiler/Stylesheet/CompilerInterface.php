<?php

namespace ChameleonSystem\ViewRendererBundle\Compiler\Stylesheet;

use TdbCmsPortal;

interface CompilerInterface
{
    public function getLocalPathToCompiled(): string;

    /**
     * @param TdbCmsPortal|null $portal
     * @return mixed
     */
    public function getCompiledCssUrl(TdbCmsPortal $portal = null);

    public function getLocalPathToCompiledCssFileForPortal(TdbCmsPortal $portal): string;

    public function getCompiledCssFilenamePattern(): string;

    public function getGeneratedCssForPortal(TdbCmsPortal $portal, bool $minifyCss = false): string;

    public function writeCssFileForPortal(string $generatedCss, TdbCmsPortal $portal): bool;
}

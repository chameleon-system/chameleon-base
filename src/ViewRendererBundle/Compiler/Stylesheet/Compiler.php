<?php

namespace ChameleonSystem\ViewRendererBundle\Compiler\Stylesheet;

use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\ViewRendererBundle\Compiler\Stylesheet\Adapter\CompilerAdapterInterface;
use TdbCmsPortal;

class Compiler implements CompilerInterface
{
    /**
     * @var CompilerAdapterInterface[]
     */
    private $compilerAdapters;

    /**
     * @var string
     */
    private $defaultType;

    /**
     * @var PortalDomainServiceInterface
     */
    private $portalDomainService;

    /**
     * @param array $compilerAdapters
     * @param string $defaultType
     * @param PortalDomainServiceInterface $portalDomainService
     */
    public function __construct(
        array $compilerAdapters,
        $defaultType = 'less',
        PortalDomainServiceInterface $portalDomainService = null
    ) {
        foreach ($compilerAdapters as $compilerAdapter) {
            $this->compilerAdapters[$compilerAdapter->getType()] = $compilerAdapter;
        }

        $this->defaultType = $defaultType;
        $this->portalDomainService = $portalDomainService;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocalPathToCompiled()
    {
        $type = $this->getTypeByPortalTheme();
        return $this->compilerAdapters[$type]->getLocalPathToCompiled();
    }

    /**
     * {@inheritdoc}
     */
    public function getCompiledCssUrl(TdbCmsPortal $portal = null)
    {
        $type = $this->getTypeByPortalTheme($portal);
        return $this->compilerAdapters[$type]->getCompiledCssUrl($portal);
    }

    /**
     * {@inheritdoc}
     */
    public function getLocalPathToCompiledCssFileForPortal(TdbCmsPortal $portal)
    {
        $type = $this->getTypeByPortalTheme($portal);
        return $this->compilerAdapters[$type]->getLocalPathToCompiledCssFileForPortal($portal);
    }

    /**
     * {@inheritdoc}
     */
    public function getCompiledCssFilenamePattern()
    {
        $type = $this->getTypeByPortalTheme();
        return $this->compilerAdapters[$type]->getCompiledCssFilenamePattern();
    }

    /**
     * {@inheritdoc}
     */
    public function getGeneratedCssForPortal(TdbCmsPortal $portal, $minifyCss = false)
    {
        $type = $this->getTypeByPortalTheme($portal);
        return $this->compilerAdapters[$type]->getGeneratedCssForPortal($portal, $minifyCss);
    }

    /**
     * {@inheritdoc}
     */
    public function writeCssFileForPortal($generatedCss, TdbCmsPortal $portal)
    {
        $type = $this->getTypeByPortalTheme($portal);
        return $this->compilerAdapters[$type]->writeCssFileForPortal($generatedCss, $portal);
    }

    /**
     * @return string
     */
    private function getTypeByPortalTheme(TdbCmsPortal $portal = null)
    {
        if (null === $portal) {
            $portal = $this->getActivePortal();
        }

        if (null === $portal) {
            return $this->defaultType;
        }

        $theme = $portal->GetFieldPkgCmsTheme();

        if (null === $theme) {
            return $this->defaultType;
        }

        $file = $theme->fieldLessFile;
        if (empty($file)) {
            return $this->defaultType;
        }

        $type = strtolower(substr($file, -4));
        if (!isset($this->compilerAdapters[$type])) {
            return $this->defaultType;
        }

        return $type;
    }

    /**
     * @return string
     */
    private function getActivePortal()
    {
        $this->portalDomainService->getActivePortal();
    }
}

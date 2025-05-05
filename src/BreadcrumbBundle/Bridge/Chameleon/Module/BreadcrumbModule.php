<?php

namespace ChameleonSystem\BreadcrumbBundle\Bridge\Chameleon\Module;

use ChameleonSystem\BreadcrumbBundle\Interfaces\BreadcrumbGeneratorProviderInterface;
use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;

final class BreadcrumbModule extends \MTPkgViewRendererAbstractModuleMapper
{
    public function __construct(
        private readonly BreadcrumbGeneratorProviderInterface $breadcrumbGeneratorProvider,
        private readonly ActivePageServiceInterface $activePageService
    ) {
        parent::__construct();
    }

    /**
     * returns always false, because the Generators itself are providing
     * a cache. therefore a cache in the module ist not needed.
     *
     * @return false
     */
    public function _AllowCache()
    {
        return false;
    }

    public function Accept(
        \IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        \IMapperCacheTriggerRestricted $oCacheTriggerManager
    ) {
        $breadcrumb = $this->breadcrumbGeneratorProvider->generateBreadcrumb();
        $oVisitor->SetMappedValue('breadcrumb', $breadcrumb);
    }
}

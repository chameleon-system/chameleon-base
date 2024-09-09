<?php

namespace ChameleonSystem\BreadcrumbBundle\Bridge\Chameleon\Module;

use ChameleonSystem\BreadcrumbBundle\Interfaces\BreadcrumbGeneratorInterface;
use ChameleonSystem\BreadcrumbBundle\Interfaces\BreadcrumbGeneratorProviderInterface;
use ChameleonSystem\BreadcrumbBundle\Provider\BreadcrumbGeneratorProvider;
use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;

final class BreadcrumbModule extends \MTPkgViewRendererAbstractModuleMapper
{
    public function __construct(
        private readonly BreadcrumbGeneratorProviderInterface $breadcrumbGeneratorProvider,
        private readonly ActivePageServiceInterface $activePageService
    ) {
        parent::__construct();
    }

    public function _AllowCache()
    {
        return false;
    }

    /**
     * return an assoc array of parameters that describe the state of the module.
     *
     * @return array
     */
    public function _GetCacheParameters()
    {
        $cacheParameters = parent::_GetCacheParameters();
        $cacheParameters['activePageId'] = $this->activePageService->getActivePage()->id;

        return $cacheParameters;
    }

    public function Accept(
        \IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        \IMapperCacheTriggerRestricted $oCacheTriggerManager
    ) {
        $activeBreadcrumbGenerator = null;
        foreach($this->breadcrumbGeneratorProvider->getBreadcrumbGeneratorList() as $breadcrumbGenerator) {
            if (true === $breadcrumbGenerator->isActive()) {
                $activeBreadcrumbGenerator = $breadcrumbGenerator;
                break;
            }
        }
        $oVisitor->SetMappedValue('breadcrumb', $activeBreadcrumbGenerator->generate());
    }
}

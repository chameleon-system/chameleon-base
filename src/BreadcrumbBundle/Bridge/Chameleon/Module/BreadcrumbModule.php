<?php

namespace ChameleonSystem\BreadcrumbBundle\Bridge\Chameleon\Module;

use ChameleonSystem\BreadcrumbBundle\Interfaces\BreadcrumbGeneratorInterface;
use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;

final class BreadcrumbModule extends \MTPkgViewRendererAbstractModuleMapper
{
    private BreadcrumbGeneratorInterface $breadcrumbGenerator;

    private ActivePageServiceInterface $activePageService;

    public function __construct(
        BreadcrumbGeneratorInterface $breadcrumbGenerator,
        ActivePageServiceInterface $activePageService
    ) {
        $this->breadcrumbGenerator = $breadcrumbGenerator;
        $this->activePageService = $activePageService;

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
        $oVisitor->SetMappedValue('breadcrumb', $this->breadcrumbGenerator->generate());
    }
}

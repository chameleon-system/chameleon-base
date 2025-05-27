<?php

namespace ChameleonSystem\BreadcrumbBundle\Bridge\Chameleon\Mapper;

use ChameleonSystem\BreadcrumbBundle\Interfaces\BreadcrumbGeneratorProviderInterface;

class BreadcrumbMapper extends \AbstractViewMapper
{
    public function __construct(
        private readonly BreadcrumbGeneratorProviderInterface $breadcrumbGeneratorProvider,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function GetRequirements(\IMapperRequirementsRestricted $oRequirements)
    {
    }

    /**
     * @inheritDoc
     */
    public function Accept(\IMapperVisitorRestricted $oVisitor, $bCachingEnabled, \IMapperCacheTriggerRestricted $oCacheTriggerManager)
    {
        $breadcrumb = $this->breadcrumbGeneratorProvider->generateBreadcrumb();
        $oVisitor->SetMappedValue('breadcrumb', $breadcrumb);
    }
}

<?php

namespace ChameleonSystem\BreadcrumbBundle\Bridge\Chameleon\Breadcrumb;

use ChameleonSystem\BreadcrumbBundle\Interfaces\BreadcrumbGeneratorInterface;
use ChameleonSystem\BreadcrumbBundle\Interfaces\BreadcrumbGeneratorUtilsInterface;
use ChameleonSystem\BreadcrumbBundle\Library\DataModel\BreadcrumbDataModel;
use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;

final class BreadcrumbStandardPageGenerator implements BreadcrumbGeneratorInterface
{
    public function __construct(
        private readonly BreadcrumbGeneratorUtilsInterface $breadcrumbGeneratorUtils,
        private readonly ActivePageServiceInterface $activePageService
    ) {
    }

    public function isActive(): bool
    {
        return true;
    }

    public function generate(): BreadcrumbDataModel
    {
        $breadcrumb = new BreadcrumbDataModel();

        $activePage = $this->activePageService->getActivePage();

        $treeId = $activePage->GetMainTreeId();
        $this->breadcrumbGeneratorUtils->attachBreadcrumbItemsByTreeId($breadcrumb, $treeId, false);
        $this->breadcrumbGeneratorUtils->attachHomePageBreadcrumbItem($breadcrumb);

        return $breadcrumb;
    }
}

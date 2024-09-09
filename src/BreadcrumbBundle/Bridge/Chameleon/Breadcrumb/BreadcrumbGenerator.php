<?php

namespace ChameleonSystem\BreadcrumbBundle\Bridge\Chameleon\Breadcrumb;

use ChameleonSystem\BreadcrumbBundle\Interfaces\BreadcrumbGeneratorInterface;
use ChameleonSystem\BreadcrumbBundle\Library\DataModel\BreadcrumbDataModel;
use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;

final class BreadcrumbGenerator implements BreadcrumbGeneratorInterface
{
    public function __construct(
        private readonly ActivePageServiceInterface $activePageService,
        private readonly BreadcrumbGeneratorInterface $breadcrumbStandardPageGenerator,
        private readonly array $breadcrumbGenerators
    ) {
    }

    public function isActive(): bool
    {
        return true;
    }

    public function generate(): ?BreadcrumbDataModel
    {
        $activePage = $this->activePageService->getActivePage();

        if (!$activePage) {
            return null;
        }

        if ($activePage->IsHomePage()) {
            return null;
        }

        foreach ($this->breadcrumbGenerators as $breadcrumbPageGenerator) {
            if ($breadcrumbPageGenerator->isActive()) {
                return $breadcrumbPageGenerator->generate();
            }
        }

        return $this->breadcrumbStandardPageGenerator->generate();
    }
}

<?php

namespace ChameleonSystem\BreadcrumbBundle\Provider;

use ChameleonSystem\BreadcrumbBundle\Interfaces\BreadcrumbGeneratorProviderInterface;
use ChameleonSystem\BreadcrumbBundle\Library\DataModel\BreadcrumbDataModel;

class BreadcrumbGeneratorProvider implements BreadcrumbGeneratorProviderInterface
{
    public function __construct(
        private readonly array $breadcrumbGeneratorList = []
    ) {
    }

    public function generateBreadcrumb(): ?BreadcrumbDataModel
    {
        $activeBreadcrumbGenerator = null;
        foreach ($this->breadcrumbGeneratorList as $breadcrumbGenerator) {
            if (true === $breadcrumbGenerator->isActive()) {
                return $breadcrumbGenerator->generate();
            }
        }

        return null;
    }
}

<?php

namespace ChameleonSystem\BreadcrumbBundle\Provider;

use ChameleonSystem\BreadcrumbBundle\Interfaces\BreadcrumbGeneratorProviderInterface;

class BreadcrumbGeneratorProvider implements BreadcrumbGeneratorProviderInterface
{
    public function __construct(
        private readonly array $breadcrumbGeneratorList = []
    ) {
    }

    public function getBreadcrumbGeneratorList(): array
    {
        return $this->breadcrumbGeneratorList;
    }
}

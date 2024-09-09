<?php

namespace ChameleonSystem\BreadcrumbBundle\Interfaces;

interface BreadcrumbGeneratorProviderInterface
{
    /**
     * @return BreadcrumbGeneratorInterface[]
     */
    public function getBreadcrumbGeneratorList(): array;
}

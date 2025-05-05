<?php

namespace ChameleonSystem\BreadcrumbBundle\Bridge\Chameleon\Breadcrumb;

use ChameleonSystem\BreadcrumbBundle\Interfaces\BreadcrumbGeneratorInterface;
use ChameleonSystem\BreadcrumbBundle\Interfaces\BreadcrumbGeneratorUtilsInterface;
use ChameleonSystem\BreadcrumbBundle\Library\DataModel\BreadcrumbDataModel;

abstract class AbstractBreadcrumbGenerator implements BreadcrumbGeneratorInterface
{
    private const triggerTable = '';

    public function __construct(
        private readonly BreadcrumbGeneratorUtilsInterface $breadcrumbGeneratorUtils,
    ) {
    }

    abstract public function isActive(): bool;

    abstract public function generate(): BreadcrumbDataModel;

    abstract protected function setCache(BreadcrumbDataModel $breadcrumb): void;

    abstract protected function getFromCache(): ?BreadcrumbDataModel;

    abstract protected function generateCacheKey(): string;
}

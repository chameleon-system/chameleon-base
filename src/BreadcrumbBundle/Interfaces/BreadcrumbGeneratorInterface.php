<?php

namespace ChameleonSystem\BreadcrumbBundle\Interfaces;

use ChameleonSystem\BreadcrumbBundle\Library\DataModel\BreadcrumbDataModel;

interface BreadcrumbGeneratorInterface
{
    public function isActive(): bool;

    public function generate(): BreadcrumbDataModel;
}

<?php

namespace ChameleonSystem\BreadcrumbBundle\Interfaces;

use ChameleonSystem\BreadcrumbBundle\Library\DataModel\BreadcrumbDataModel;

interface BreadcrumbGeneratorProviderInterface
{
    public function generateBreadcrumb(): ?BreadcrumbDataModel;
}

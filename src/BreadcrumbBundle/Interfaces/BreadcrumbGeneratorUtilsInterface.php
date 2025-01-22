<?php

namespace ChameleonSystem\BreadcrumbBundle\Interfaces;

use ChameleonSystem\BreadcrumbBundle\Library\DataModel\BreadcrumbDataModel;

interface BreadcrumbGeneratorUtilsInterface
{
    public function attachBreadcrumbItem(BreadcrumbDataModel $breadcrumb, \TdbCmsTree $tree, bool $ignoreHiddenTree = true): void;

    public function attachBreadcrumbItemsByTreeId(
        BreadcrumbDataModel $breadcrumb,
        string $treeId,
        bool $ignoreFirstTreeIfHidden = true
    ): void;

    public function attachHomePageBreadcrumbItem(BreadcrumbDataModel $breadcrumb): void;
}

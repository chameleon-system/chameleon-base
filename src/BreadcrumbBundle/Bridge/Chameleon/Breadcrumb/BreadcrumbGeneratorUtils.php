<?php

namespace ChameleonSystem\BreadcrumbBundle\Bridge\Chameleon\Breadcrumb;

use ChameleonSystem\BreadcrumbBundle\Interfaces\BreadcrumbGeneratorUtilsInterface;
use ChameleonSystem\BreadcrumbBundle\Library\DataModel\BreadcrumbDataModel;
use ChameleonSystem\BreadcrumbBundle\Library\DataModel\BreadcrumbItemDataModel;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Service\TreeServiceInterface;

class BreadcrumbGeneratorUtils implements BreadcrumbGeneratorUtilsInterface
{
    public function __construct(
        private readonly TreeServiceInterface $treeService,
        protected readonly PortalDomainServiceInterface $portalDomainService
    ) {
    }

    public function attachBreadcrumbItem(
        BreadcrumbDataModel $breadcrumb,
        \TdbCmsTree $tree,
        bool $ignoreHiddenTree = true
    ): void {
        if ($ignoreHiddenTree && $tree->fieldHidden) {
            return;
        }

        $link = $tree->getLink(true);
        if (null === $link) {
            return;
        }

        $breadcrumbItem = new BreadcrumbItemDataModel($tree->GetName(), $link);
        $breadcrumb->add($breadcrumbItem);
    }

    public function attachBreadcrumbItemsByTreeId(
        BreadcrumbDataModel $breadcrumb,
        string $treeId,
        bool $ignoreFirstTreeIfHidden = true
    ): void {
        $tree = $this->treeService->getById($treeId);
        if (null === $tree) {
            return;
        }

        $this->attachBreadcrumbItem($breadcrumb, $tree, $ignoreFirstTreeIfHidden);

        while ($tree = $tree->GetParentNode()) {
            $this->attachBreadcrumbItem($breadcrumb, $tree);
        }
    }

    public function attachHomePageBreadcrumbItem(BreadcrumbDataModel $breadcrumb): void
    {
        $portal = $this->portalDomainService->getActivePortal();
        if (null === $portal) {
            return;
        }

        $tree = $portal->GetPortalHomeNode();
        $this->attachBreadcrumbItem($breadcrumb, $tree, false);
    }
}

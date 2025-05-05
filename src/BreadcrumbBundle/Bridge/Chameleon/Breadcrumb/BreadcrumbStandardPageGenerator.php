<?php

namespace ChameleonSystem\BreadcrumbBundle\Bridge\Chameleon\Breadcrumb;

use ChameleonSystem\BreadcrumbBundle\Interfaces\BreadcrumbGeneratorUtilsInterface;
use ChameleonSystem\BreadcrumbBundle\Library\DataModel\BreadcrumbDataModel;
use ChameleonSystem\BreadcrumbBundle\Library\DataModel\BreadcrumbItemDataModel;
use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;
use ChameleonSystem\CoreBundle\Service\TreeService;
use esono\pkgCmsCache\Cache;

class BreadcrumbStandardPageGenerator extends AbstractBreadcrumbGenerator
{
    private const triggerTable = 'cms_tpl_page';

    private ?\TCMSActivePage $activePage = null;

    public function __construct(
        private readonly BreadcrumbGeneratorUtilsInterface $breadcrumbGeneratorUtils,
        private readonly ActivePageServiceInterface $activePageService,
        private readonly TreeService $treeService,
        private readonly Cache $cache
    ) {
    }

    public function isActive(): bool
    {
        return true;
    }

    public function generate(): BreadcrumbDataModel
    {
        $cacheResult = $this->getFromCache();

        if (null !== $cacheResult) {
            return $cacheResult;
        }

        $breadcrumb = new BreadcrumbDataModel();

        $activePage = $this->getActivePage();
        $portal = $activePage->GetPortal();

        // Never show the Breadcrumb on the HomePage.
        if ($activePage->IsHomePage()) {
            return $breadcrumb;
        }

        $this->attachBreadcrumbByCmsTree($breadcrumb, $activePage);

        $this->breadcrumbGeneratorUtils->attachHomePageBreadcrumbItem($breadcrumb);

        $this->setCache($breadcrumb);

        return $breadcrumb;
    }

    private function attachBreadcrumbByCmsTree(BreadcrumbDataModel $breadcrumb, \TCMSActivePage $activePage): void
    {
        $portal = $activePage->GetPortal();
        if (null === $portal) {
            return;
        }

        $stopNodes = $this->getStopNodes($portal);

        $recordExists = true;
        $treeNode = null;
        $nodeId = $activePage->GetMainTreeId();

        do {
            $treeNode = $this->treeService->getById($nodeId);
            if (null !== $treeNode) {
                $breadcrumbItem = new BreadcrumbItemDataModel($treeNode->GetName(), $treeNode->getLink());
                $breadcrumb->add($breadcrumbItem);
                $nodeId = $treeNode->sqlData['parent_id'];
            } else {
                $recordExists = false;
            }
        } while ($recordExists && !in_array($nodeId, $stopNodes) && !in_array($treeNode->id, $stopNodes));
        // now add the stop node as well... if a page is assigned to it...
        if ($recordExists) {
            $treeNode = $this->treeService->getById($nodeId);
            if (null !== $treeNode) {
                if (false !== $treeNode->GetLinkedPage()) {
                    $breadcrumbItem = new BreadcrumbItemDataModel($treeNode->GetName(), $treeNode->getLink());
                    $breadcrumb->add($breadcrumbItem);
                }
            }
        }
    }

    private function getStopNodes(\TdbCmsPortal $portal): array
    {
        $stopNodes = \TdbCmsDivision::GetStopNodes();

        // add navi nodes as stop nodes...
        $query = "SELECT `tree_node` FROM `cms_portal_navigation` WHERE `cms_portal_id` = '"
            .\MySqlLegacySupport::getInstance()->real_escape_string($portal->id)."'";
        $navis = \MySqlLegacySupport::getInstance()->query($query);
        $naviStopNodes = [];

        while ($navi = \MySqlLegacySupport::getInstance()->fetch_assoc($navis)) {
            $naviStopNodes[] = $navi['tree_node'];
        }

        $stopNodes = array_merge($naviStopNodes, $stopNodes);

        return $stopNodes;
    }

    protected function setCache(BreadcrumbDataModel $breadcrumb): void
    {
        $activePage = $this->getActivePage();
        $cacheParameter = ['table' => self::triggerTable];
        if (null !== $activePage) {
            $cacheParameter['id'] = $activePage->id;
        }

        $this->cache->set($this->generateCacheKey(), $breadcrumb, $cacheParameter);
    }

    protected function generateCacheKey(): string
    {
        $activePage = $this->getActivePage();

        return 'breadcrumb_'.self::triggerTable.'_'.$activePage?->id;
    }

    protected function getFromCache(): ?BreadcrumbDataModel
    {
        return $this->cache->get($this->generateCacheKey());
    }

    private function getActivePage(): ?\TCMSActivePage
    {
        $activePage = null;
        if (null === $this->activePage) {
            $activePage = $this->activePageService->getActivePage();
            $this->activePage = $activePage;
        }

        return $this->activePage;
    }
}

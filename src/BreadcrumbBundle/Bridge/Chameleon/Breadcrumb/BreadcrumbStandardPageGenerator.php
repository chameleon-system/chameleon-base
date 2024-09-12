<?php

namespace ChameleonSystem\BreadcrumbBundle\Bridge\Chameleon\Breadcrumb;

use ChameleonSystem\BreadcrumbBundle\Interfaces\BreadcrumbGeneratorInterface;
use ChameleonSystem\BreadcrumbBundle\Interfaces\BreadcrumbGeneratorUtilsInterface;
use ChameleonSystem\BreadcrumbBundle\Library\DataModel\BreadcrumbDataModel;
use ChameleonSystem\BreadcrumbBundle\Library\DataModel\BreadcrumbItemDataModel;
use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;
use esono\pkgCmsCache\Cache;

final class BreadcrumbStandardPageGenerator extends AbstractBreadcrumbGenerator
{
    private const triggerTable = 'cms_tbl_page';

    private ?\TCMSActivePage $activePage = null;

    public function __construct(
        private readonly BreadcrumbGeneratorUtilsInterface $breadcrumbGeneratorUtils,
        private readonly ActivePageServiceInterface $activePageService,
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
        if(null !== $cacheResult){
            return $cacheResult;
        }

        $breadcrumb = new BreadcrumbDataModel();

        $activePage = $this->getActivePage();

//        Do we need to create a breadcrumb for a normal page?
//        $breadcrumbItem = new BreadcrumbItemDataModel($activePage->GetName(), $activePage->GetRealURL());
//        $breadcrumb->add($breadcrumbItem);
//        $this->breadcrumbGeneratorUtils->attachHomePageBreadcrumbItem($breadcrumb);

        $this->setCache($breadcrumb);
        return $breadcrumb;
    }

    protected function setCache(BreadcrumbDataModel $breadcrumb): void
    {
        $activePage = $this->getActivePage();
        $cacheParameter = ['table' => self::triggerTable];
        if(null !== $activePage){
            $cacheParameter['id'] = $activePage->id;
        }

        $this->cache->set($this->generateCacheKey(), $breadcrumb, $cacheParameter);
    }

    protected function generateCacheKey(): string
    {
        $activePage = $this->getActivePage();

        return 'breadcrumb_'.self::triggerTable.'_'.$activePage?->id;
    }

    protected function getFromCache(): ?BreadcrumbDataModel{
        return $this->cache->Get($this->generateCacheKey());
    }

    private function getActivePage()
    {
        $activePage = null;
        if (null === $this->activePage) {
            $activePage = $this->activePageService->getActivePage();
            $this->activePage = $activePage;
        }


        return $this->activePage;
    }
}

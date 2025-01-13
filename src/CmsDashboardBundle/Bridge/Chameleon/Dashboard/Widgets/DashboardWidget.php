<?php
/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard\Widgets;

use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Service\DashboardCacheService;

abstract class DashboardWidget implements DashboardWidgetInterface
{
    public function __construct(
        private readonly DashboardCacheService $dashboardCacheService)
    {
    }

    abstract public function getTitle(): string;

    abstract public function getChartId(): string;

    abstract public function getDropdownItems(): array;

    public function getFooterIncludes(): array
    {
        return [];
    }

    public function getBodyHtml(bool $forceCacheReload = false): string
    {
        if (false === $forceCacheReload) {
            $bodyFromCache = $this->dashboardCacheService->getCachedBodyHtml($this->dashboardCacheService->getCacheKey(static::class));

            if (null !== $bodyFromCache) {
                return $bodyFromCache;
            }
        }

        $body = $this->generateBodyHtml();

        $this->dashboardCacheService->setCachedBodyHtml($this->dashboardCacheService->getCacheKey(static::class), $body);

        return $body;
    }

    abstract protected function generateBodyHtml(): string;

    protected function getCacheCreationTime(): ?int
    {
        return $this->dashboardCacheService->getCacheCreationTime($this->dashboardCacheService->getCacheTimestampKey($this->dashboardCacheService->getCacheKey(static::class)));
    }

    public function getColorCssClass(): string
    {
        return '';
    }

    public function getFooterHtml(): string
    {
        return '<div class="mx-3 my-2">zuletzt aktualisiert: <span class="widget-timestamp">'.date('d.m.Y H:i', $this->getCacheCreationTime()).'</span></div>';
    }
}

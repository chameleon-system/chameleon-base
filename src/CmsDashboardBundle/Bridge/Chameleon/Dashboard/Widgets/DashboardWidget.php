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

use esono\pkgCmsCache\CacheInterface;

abstract class DashboardWidget implements DashboardWidgetInterface
{
    private CacheInterface $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    abstract public function getTitle(): string;

    abstract public function getDropdownItems(): array;

    public function getFooterIncludes(): array
    {
        return [];
    }

    protected function getCacheTimeToLiveInSec(): int
    {
        return 60 * 60 * 24; // one day
    }

    public function getBodyHtml(): string
    {
        $cacheKey = $this->getCacheKey();
        $bodyFromCache = $this->cache->get($cacheKey);

        if (null !== $bodyFromCache) {
            return $bodyFromCache;
        }

        $body = $this->generateBodyHtml();

        $this->cache->set($cacheKey, $body, [], $this->getCacheTimeToLiveInSec());

        return $body;
    }

    abstract protected function generateBodyHtml(): string;

    public function getFooterHtml(): string
    {
        return '';
    }

    protected function getCacheKey(): string
    {
        return md5('widget_body_'.static::class);
    }

    public function getColorCssClass(): string
    {
        return '';
    }
}

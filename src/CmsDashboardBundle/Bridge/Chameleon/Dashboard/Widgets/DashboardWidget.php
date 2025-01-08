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
    public const DEFAULT_TIMEFRAME_FOR_STATS = '-90 days';

    public function __construct(private readonly CacheInterface $cache)
    {
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
        $this->cache->set($this->getCacheTimestampKey(), time(), [], $this->getCacheTimeToLiveInSec());

        return $body;
    }

    abstract protected function generateBodyHtml(): string;

    protected function getCacheKey(): string
    {
        return md5('widget_body_'.static::class);
    }

    protected function getCacheTimestampKey(): string
    {
        return md5('widget_body_timestamp_'.static::class);
    }

    protected function getCacheCreationTime(): ?int
    {
        return $this->cache->get($this->getCacheTimestampKey());
    }

    public function getColorCssClass(): string
    {
        return '';
    }

    public function getFooterHtml(): string
    {
        return '';
    }
}

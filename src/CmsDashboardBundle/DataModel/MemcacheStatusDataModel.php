<?php

namespace ChameleonSystem\CmsDashboardBundle\DataModel;

readonly class MemcacheStatusDataModel
{
    public function __construct(
        public bool $enabled,
        public string $version,
        public string $uptime,
        public string $memoryLimit,
        public string $memoryUsage,
        public float $memoryUsagePercent
    ) {
    }
}
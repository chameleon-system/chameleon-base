<?php

namespace ChameleonSystem\CmsDashboardBundle\DataModel;

readonly class MemoryUsageDataModel
{
    public function __construct(
        public string $total,
        public string $used,
        public string $free,
        public float $usagePercent
    ) {
    }
}
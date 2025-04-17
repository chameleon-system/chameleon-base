<?php

namespace ChameleonSystem\CmsDashboardBundle\DataModel;

readonly class ServerStatusDataModel
{
    public function __construct(
        public string $phpVersion,
        public string $serverSoftware,
        public string $memoryLimit,
        public string $diskFreeSpace,
        public string $diskTotalSpace,
        public float $diskUsagePercent,
        public array $loadAverage, // [1min, 5min, 15min]
        public MemoryUsageDataModel $memoryUsage,
        public MemcacheStatusDataModel $memcacheCache,
        public MemcacheStatusDataModel $memcacheSession,
        public DatabaseStatusDataModel $database
    ) {
    }
}

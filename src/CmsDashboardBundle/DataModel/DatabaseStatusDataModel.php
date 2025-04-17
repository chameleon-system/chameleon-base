<?php

namespace ChameleonSystem\CmsDashboardBundle\DataModel;

readonly class DatabaseStatusDataModel
{
    /** @param array<array{name: string, size: string}> $topTables */
    public function __construct(
        public string $totalSize,
        public int $threadsConnected,
        public array $topTables
    ) {
    }
}

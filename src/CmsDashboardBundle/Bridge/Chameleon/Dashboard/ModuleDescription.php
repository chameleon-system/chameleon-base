<?php

namespace ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard;

// ModuleDescription represents an available dashboard module with its id, name and description
final readonly class ModuleDescription
{
    public function __construct(
        public string $name,
        public string $description,
        public string $id
    ) {
    }
}

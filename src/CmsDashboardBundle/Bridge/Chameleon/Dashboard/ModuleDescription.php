<?php

namespace ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard;

// ModuleDescription represents an available dashboard module with its id, name and description
final class ModuleDescription
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly string $id
    ) {}
}
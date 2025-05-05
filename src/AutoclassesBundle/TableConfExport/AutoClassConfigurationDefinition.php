<?php

namespace ChameleonSystem\AutoclassesBundle\TableConfExport;

class AutoClassConfigurationDefinition
{
    public function __construct(
        private readonly string $entryClass,
        private readonly string $exitClass,
        private readonly array $classList
    ) {
    }

    public function getEntryClass(): string
    {
        return $this->entryClass;
    }

    public function getExitClass(): string
    {
        return $this->exitClass;
    }

    public function getClassList(): array
    {
        return $this->classList;
    }

    public function asArray(): array
    {
        return [
            'entry_class' => $this->entryClass,
            'exit_class' => $this->exitClass,
            'class_list' => $this->classList,
        ];
    }
}

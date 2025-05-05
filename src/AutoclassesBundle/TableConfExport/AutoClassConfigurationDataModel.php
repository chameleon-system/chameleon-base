<?php

namespace ChameleonSystem\AutoclassesBundle\TableConfExport;

class AutoClassConfigurationDataModel
{
    public function __construct(
        private readonly string $name,
        private readonly ?AutoClassConfigurationDefinition $record,
        private readonly ?AutoClassConfigurationDefinition $list
    ) {
    }

    public function getRecord(): ?AutoClassConfigurationDefinition
    {
        return $this->record;
    }

    public function getList(): ?AutoClassConfigurationDefinition
    {
        return $this->list;
    }

    public function asArray(): array
    {
        $record = $this->record?->asArray();
        $list = $this->list?->asArray();

        if (null !== $record) {
            $result['record'] = $record;
        }
        if (null !== $list) {
            $result['list'] = $list;
        }
        if (0 === count($result)) {
            return [];
        }

        return [
            $this->name => $result,
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }
}

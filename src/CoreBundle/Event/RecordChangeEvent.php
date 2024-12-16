<?php

namespace ChameleonSystem\CoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class RecordChangeEvent extends Event
{
    private string $tableName;
    private string $recordId;

    public function __construct(string $tableName, string $recordId)
    {
        $this->tableName = $tableName;
        $this->recordId = $recordId;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return string
     */
    public function getRecordId()
    {
        return $this->recordId;
    }
}

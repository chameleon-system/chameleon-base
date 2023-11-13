<?php

namespace ChameleonSystem\CoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class RecordChangeEvent extends Event
{
    private string $tableName;
    private string $recordId;

    private string $cmsTblConfId;

    public function __construct(string $tableName, string $recordId)
    {
        $this->tableName = $tableName;
        $this->recordId = $recordId;
    }

    /**
     * @return string
     * @deprecated use `self::getTableName()` instead
     */
    public function getTableId()
    {
        return $this->tableName;
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

    public function getCmsTblConfId(): string
    {
        return $this->cmsTblConfId;
    }

    public function setCmsTblConfId(string $cmsTblConfId): void
    {
        $this->cmsTblConfId = $cmsTblConfId;
    }
}

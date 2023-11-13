<?php

namespace ChameleonSystem\CoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class RecordChangeEvent extends Event
{
    private string $cmsTblConfId;

    public function __construct(
        readonly private string $tableName,
        readonly private string $recordId,
    )
    {
    }

    /**
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

<?php

namespace ChameleonSystem\CoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class RecordChangeEvent extends Event
{
    /**
     * @var string
     */
    private $tableId;
    /**
     * @var string
     */
    private $recordId;

    private string $cmsTblConfId;

    /**
     * @param string $tableId
     * @param string $recordId
     */
    public function __construct($tableId, $recordId)
    {
        $this->tableId = $tableId;
        $this->recordId = $recordId;
    }

    /**
     * @return string
     */
    public function getTableId()
    {
        return $this->tableId;
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

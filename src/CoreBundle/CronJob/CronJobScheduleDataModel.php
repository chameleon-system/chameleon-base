<?php

namespace ChameleonSystem\CoreBundle\CronJob;

class CronJobScheduleDataModel
{
    /**
     * @var \DateTime|null
     */
    private $lastExecutedUtc;
    /**
     * @var int
     */
    private $executeEveryNMinutes = 0;
    /**
     * @var int
     */
    private $unlockAfterNMinutes = 0;
    /**
     * @var bool
     */
    private $isLocked;

    /**
     * @param int       $executeEveryNMinutes
     * @param int       $unlockAfterNMinutes
     * @param \DateTime $lastExecutedUtc
     */
    public function __construct(int $executeEveryNMinutes, int $unlockAfterNMinutes, $isLocked, ?\DateTime $lastExecutedUtc)
    {
        $this->lastExecutedUtc = $lastExecutedUtc;
        $this->executeEveryNMinutes = $executeEveryNMinutes;
        $this->unlockAfterNMinutes = $unlockAfterNMinutes;
        $this->isLocked = $isLocked;
    }

    public function getLastExecutedUtc(): ?\DateTime
    {
        return $this->lastExecutedUtc;
    }

    public function getExecuteEveryNMinutes(): int
    {
        return $this->executeEveryNMinutes;
    }

    public function getUnlockAfterNMinutes(): int
    {
        return $this->unlockAfterNMinutes;
    }

    /**
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this->isLocked;
    }
}

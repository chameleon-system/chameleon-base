<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\CronJob;

class CronJobScheduleDataModel
{
    /**
     * @var \DateTime|null
     */
    private $lastPlannedExecution;
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

    public function __construct(int $executeEveryNMinutes, int $unlockAfterNMinutes, bool $isLocked, ?\DateTime $lastPlannedExecution)
    {
        $this->lastPlannedExecution = $lastPlannedExecution;
        $this->executeEveryNMinutes = $executeEveryNMinutes;
        $this->unlockAfterNMinutes = $unlockAfterNMinutes;
        $this->isLocked = $isLocked;
    }

    public function getLastPlannedExecution(): ?\DateTime
    {
        return $this->lastPlannedExecution;
    }

    public function getExecuteEveryNMinutes(): int
    {
        return $this->executeEveryNMinutes;
    }

    public function getUnlockAfterNMinutes(): int
    {
        return $this->unlockAfterNMinutes;
    }

    public function isLocked(): bool
    {
        return $this->isLocked;
    }
}

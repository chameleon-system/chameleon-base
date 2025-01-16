<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\CronJob\DataModel;

readonly class CronJobScheduleDataModel
{
    public function __construct(
        private int $executeEveryNMinutes,
        private int $unlockAfterNMinutes,
        private bool $isLocked,
        private ?\DateTime $lastPlannedExecution,
        private ?\DateTime $realLastExecution)
    {
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

    public function getRealLastExecution(): ?\DateTime
    {
        return $this->realLastExecution;
    }
}

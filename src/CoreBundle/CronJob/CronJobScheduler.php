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

use ChameleonSystem\CoreBundle\Interfaces\TimeProviderInterface;

class CronJobScheduler implements CronJobSchedulerInterface
{
    /**
     * @var TimeProviderInterface
     */
    private $timeProvider;

    public function __construct(TimeProviderInterface $timeProvider)
    {
        $this->timeProvider = $timeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresExecution(CronJobScheduleDataModel $schedule): bool
    {
        $this->validateSchedule($schedule);

        $lastPlannedExecution = $schedule->getLastPlannedExecution();
        if (null === $lastPlannedExecution) {
            return true;
        }

        $now = $this->timeProvider->getDateTime($lastPlannedExecution->getTimezone());

        $nextExecution = clone $lastPlannedExecution;
        $nextExecution->add(new \DateInterval(sprintf('PT%sM', $schedule->getExecuteEveryNMinutes())));

        if ($now < $nextExecution) {

            return false;
        }

        if (false === $schedule->isLocked()) {

            return true;
        }

        $iMaxLockTime = $schedule->getUnlockAfterNMinutes();
        $maxLock = clone $lastPlannedExecution;
        $maxLock->add(new \DateInterval(sprintf('PT%sM', $iMaxLockTime)));
        if ($now <= $maxLock) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateCurrentPlannedExecutionDate(CronJobScheduleDataModel $schedule): \DateTime
    {
        $this->validateSchedule($schedule);

        $lastPlannedExecution = $schedule->getLastPlannedExecution();

        if (null === $lastPlannedExecution) {
            return $this->timeProvider->getDateTime();
        }
        $timeZone = $lastPlannedExecution->getTimezone();

        $now = $this->timeProvider->getDateTime($timeZone);

        if ($now < $lastPlannedExecution) {
            return $lastPlannedExecution;
        }

        $executionInterval = new \DateInterval(sprintf('PT%sM', $schedule->getExecuteEveryNMinutes()));

        $timePassedSinceLastPlannedExecution = $now->diff($lastPlannedExecution);

        if ($this->isLess($timePassedSinceLastPlannedExecution, $executionInterval)) {
            return $lastPlannedExecution;
        }

        $plannedExecution = clone $lastPlannedExecution;

        do {
            $plannedExecution->add($executionInterval);
        } while ($plannedExecution < $now);

        if ($plannedExecution > $now) {
            $plannedExecution->sub($executionInterval);
        }

        return $plannedExecution;
    }

    private function isLess(\DateInterval $a, \DateInterval  $b): bool
    {
        $now = $this->timeProvider->getDateTime();
        $aNow = clone $now;
        $aNow = $aNow->add($a);
        $bNow = clone $now;
        $bNow->add($b);

        return ($aNow >= $bNow);
    }

    /**
     * @param CronJobScheduleDataModel $schedule
     */
    private function validateSchedule(CronJobScheduleDataModel $schedule): void
    {
        if ($schedule->getExecuteEveryNMinutes() <= 0) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid schedule value of "%s" for executeEveryNMinutes property',
                    $schedule->getExecuteEveryNMinutes()
                )
            );
        }
    }
}

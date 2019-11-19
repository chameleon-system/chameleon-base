<?php

namespace ChameleonSystem\CoreBundle\CronJob;

use ChameleonSystem\CoreBundle\Interfaces\ChameleonTimeProviderInterface;

class CronJobScheduler implements CronJobSchedulerInterface
{
    /**
     * @var ChameleonTimeProviderInterface
     */
    private $timeProvider;

    public function __construct(ChameleonTimeProviderInterface $timeProvider)
    {
        $this->timeProvider = $timeProvider;
    }

    public function requiresExecution(CronJobScheduleDataModel $schedule): bool
    {
        $this->validateSchedule($schedule);

        $lastPlannedExecution = $schedule->getLastPlannedExecution();
        if (null === $lastPlannedExecution) {
            return true;
        }

        $nowObject = $this->timeProvider->getDateTime($lastPlannedExecution->getTimezone());

        $nextExecution = clone $lastPlannedExecution;
        $nextExecution->add(new \DateInterval(sprintf('PT%sM', $schedule->getExecuteEveryNMinutes())));

        // not time to execute again
        if ($nowObject < $nextExecution) {
            return false;
        }

        if (false === $schedule->isLocked()) {
            // not locked - so execution required.
            return true;
        }

        // check if cronjob is locked, but last execution is older than 24h and older than execute interval
        // may be possible if the cronjob was interrupted before end of script by an error or server time-out
        $iMaxLockTime = $schedule->getUnlockAfterNMinutes();
        $maxLock = clone $lastPlannedExecution;
        $maxLock->add(new \DateInterval(sprintf('PT%sM', $iMaxLockTime)));
        if ($nowObject <= $maxLock) {
            return false;
        }

        return true;
    }

    public function calculateCurrentPlanedExecutionDate(CronJobScheduleDataModel $schedule): \DateTime
    {
        $this->validateSchedule($schedule);

        $lastPannedExecution = $schedule->getLastPlannedExecution();

        if (null === $lastPannedExecution) {
            return $this->timeProvider->getDateTime();
        }
        $timeZone = $lastPannedExecution->getTimezone();

        $now = $this->timeProvider->getDateTime($timeZone);

        if ($now < $lastPannedExecution) {
            return $lastPannedExecution;
        }

        $executionInterval = new \DateInterval(sprintf('PT%sM', $schedule->getExecuteEveryNMinutes()));

        $timePassedSinceLastPlannedExecution = $now->diff($lastPannedExecution);

        if ($timePassedSinceLastPlannedExecution < $executionInterval) {
            return $lastPannedExecution;
        }

        // find the next execution point
        $plannedExecution = clone $lastPannedExecution;

        do {
            $plannedExecution->add($executionInterval);
        } while ($plannedExecution < $now);

        if ($plannedExecution > $now) {
            $plannedExecution->sub($executionInterval);
        }

        return $plannedExecution;
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

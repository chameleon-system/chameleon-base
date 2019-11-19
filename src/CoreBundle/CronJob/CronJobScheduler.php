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
        $needExecution = false;
        if (null === $schedule->getLastExecutedUtc()) {
            return true;
        }

        $utc = new \DateTimeZone('UTC');
        $nowObject = $this->timeProvider->getDateTime($utc);

        $lastPlannedExecution = $schedule->getLastExecutedUtc();

//            $iLastExecutionTimeInSeconds = strtotime($this->sqlData['last_execution']);
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

    public function calculateCurrentPlanedExecutionDateUtc(CronJobScheduleDataModel $scheduleDataModel): \DateTime
    {
        // TODO: Implement calculateCurrentPlanedExecutionDateUtc() method.
    }
}

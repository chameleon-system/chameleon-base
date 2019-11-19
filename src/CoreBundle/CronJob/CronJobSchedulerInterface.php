<?php

namespace ChameleonSystem\CoreBundle\CronJob;

interface CronJobSchedulerInterface
{
    public function requiresExecution(CronJobScheduleDataModel $schedule): bool;

    public function calculateCurrentPlanedExecutionDateUtc(CronJobScheduleDataModel $scheduleDataModel): \DateTime;
}

<?php

namespace ChameleonSystem\CoreBundle\CronJob;

interface CronJobSchedulerInterface
{
    public function requiresExecution(CronJobScheduleDataModel $schedule): bool;

    public function calculateCurrentPlanedExecutionDate(CronJobScheduleDataModel $scheduleDataModel): \DateTime;
}

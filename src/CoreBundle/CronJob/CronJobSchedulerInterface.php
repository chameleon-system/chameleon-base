<?php

namespace ChameleonSystem\CoreBundle\CronJob;

interface CronJobSchedulerInterface
{
    /**
     * @param CronJobScheduleDataModel $schedule
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function requiresExecution(CronJobScheduleDataModel $schedule): bool;

    /**
     * @param CronJobScheduleDataModel $schedule
     *
     * @return \DateTime
     * @throws \InvalidArgumentException
     */
    public function calculateCurrentPlanedExecutionDate(CronJobScheduleDataModel $schedule): \DateTime;
}

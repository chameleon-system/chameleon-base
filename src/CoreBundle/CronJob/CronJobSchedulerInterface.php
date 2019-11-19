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

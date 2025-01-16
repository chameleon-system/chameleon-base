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

use ChameleonSystem\CoreBundle\CronJob\DataModel\CronJobScheduleDataModel;

interface CronJobSchedulerInterface
{
    /**
     * @throws \InvalidArgumentException
     */
    public function requiresExecution(CronJobScheduleDataModel $schedule): bool;

    /**
     * @throws \InvalidArgumentException
     */
    public function calculateCurrentPlannedExecutionDate(CronJobScheduleDataModel $schedule): \DateTime;
}

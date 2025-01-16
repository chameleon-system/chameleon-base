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

use ChameleonSystem\CoreBundle\CronJob\DataModel\CronJobDataModel;
use ChameleonSystem\CoreBundle\Exception\CronjobHandlingException;

interface CronjobStateServiceInterface
{
    /**
     * @throws CronjobHandlingException
     */
    public function isCronjobRunning(): bool;

    /**
     * @return array<CronJobDataModel>
     */
    public function getLastRunCronJobs(int $limit = 5): array;

    /**
     * @return array<CronJobDataModel>
     */
    public function getRunningRunCronJobs(int $limit = 5): array;
}

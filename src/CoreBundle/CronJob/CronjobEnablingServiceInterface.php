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

use ChameleonSystem\CoreBundle\Exception\CronjobEnableException;

/**
 * Enables or disables all cronjobs.
 */
interface CronjobEnablingServiceInterface
{
    /**
     * Checks if all cron jobs should be able to run.
     *
     * @return bool
     */
    public function isCronjobExecutionEnabled(): bool;

    /**
     * @throws CronjobEnableException
     */
    public function enableCronjobExecution() : void;

    /**
     * @throws CronjobEnableException
     */
    public function disableCronjobExecution() :void;
}

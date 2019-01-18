<?php

namespace ChameleonSystem\CoreBundle\CronJob;

use ChameleonSystem\CoreBundle\Exception\CronjobHandlingException;

interface CronjobStateServiceInterface
{
    /**
     * @throws CronjobHandlingException
     */
    public function isCronjobRunning(): bool;
}

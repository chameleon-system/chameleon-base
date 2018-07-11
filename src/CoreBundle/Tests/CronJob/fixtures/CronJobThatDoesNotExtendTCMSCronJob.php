<?php

namespace ChameleonSystem\CoreBundle\Tests\CronJob\fixtures;

class CronJobThatDoesNotExtendTCMSCronJob
{
    public function __construct()
    {
        // Do not call parent constructor in order to avoid ServiceLocator calls.
    }
}

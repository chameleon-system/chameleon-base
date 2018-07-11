<?php

namespace ChameleonSystem\CoreBundle\Tests\CronJob\fixtures;

use TCMSCronJob;

class CronJobThatExtendsTCMSCronJob extends TCMSCronJob
{
    public function __construct()
    {
        // Do not call parent constructor in order to avoid ServiceLocator calls.
    }
}

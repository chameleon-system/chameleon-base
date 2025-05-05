<?php

namespace ChameleonSystem\CoreBundle\Tests\CronJob\fixtures;

class CronJobThatExtendsTCMSCronJob extends \TCMSCronJob
{
    public function __construct()
    {
        // Does not call parent constructor
    }
}

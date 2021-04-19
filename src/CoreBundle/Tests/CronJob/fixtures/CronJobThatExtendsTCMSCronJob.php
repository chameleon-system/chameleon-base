<?php

namespace ChameleonSystem\CoreBundle\Tests\CronJob\fixtures;

use TCMSCronJob;

class CronJobThatExtendsTCMSCronJob extends TCMSCronJob
{

    public function __construct()
    {
        parent::__construct();
    }

}

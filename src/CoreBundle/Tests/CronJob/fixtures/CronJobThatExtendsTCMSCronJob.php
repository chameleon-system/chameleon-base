<?php

namespace ChameleonSystem\CoreBundle\Tests\CronJob\fixtures;

use TdbCmsCronjobs;

class CronJobThatExtendsTCMSCronJob extends TdbCmsCronjobs
{

    public function __construct()
    {
        parent::__construct();
    }

}

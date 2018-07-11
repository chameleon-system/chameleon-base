<?php

namespace ChameleonSystem\CoreBundle\CronJob;

use TCMSCronJob;

interface CronJobFactoryInterface
{
    /**
     * @param string $identifier
     * @param array  $data
     *
     * @return TCMSCronJob
     *
     * @throws \InvalidArgumentException
     */
    public function constructCronJob($identifier, array $data);
}

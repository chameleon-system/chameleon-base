<?php

namespace ChameleonSystem\CoreBundle\CronJob;

interface CronJobFactoryInterface
{
    /**
     * @param string $identifier
     *
     * @return \TCMSCronJob
     *
     * @throws \InvalidArgumentException
     */
    public function constructCronJob($identifier, array $data);
}

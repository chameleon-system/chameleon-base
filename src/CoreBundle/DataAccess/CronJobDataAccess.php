<?php

namespace ChameleonSystem\CoreBundle\DataAccess;

class CronJobDataAccess
{
    private const CRONJOB_TIMESTAMP_PARAMETER = 'cronjob_last_call_timestamp';

    public function getTimestampOfLastCronJobCall(): int
    {
        $cmsConfig = \TCMSConfig::GetInstance();

        if (null === $cmsConfig) {
            return 0;
        }

        $lastCronJobCall = $cmsConfig->GetConfigParameter(self::CRONJOB_TIMESTAMP_PARAMETER, false, true);

        return null === $lastCronJobCall ? 0 : (int) $lastCronJobCall;
    }

    public function refreshTimestampOfLastCronJobCall(): void
    {
        $cmsConfig = \TCMSConfig::GetInstance();

        if (null === $cmsConfig) {
            return;
        }

        $cmsConfig->SetConfigParameter(self::CRONJOB_TIMESTAMP_PARAMETER, time(), true);
    }
}

<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\SanityCheck;

class CronJobDataAccess implements CronJobCheckDataAccessInterface
{
    const CRONJOB_TIMESTAMP_PARAMETER = 'cronjob_last_call_timestamp';

    /**
     * {@inheritdoc}
     */
    public function getTimestampOfLastCronJobCall()
    {
        $cmsConfig = \TCMSConfig::GetInstance();
        $lastCronJobCall = $cmsConfig->GetConfigParameter(self::CRONJOB_TIMESTAMP_PARAMETER, false, true);

        return null === $lastCronJobCall ? 0 : (int) $lastCronJobCall;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshTimestampOfLastCronJobCall()
    {
        $cmsConfig = \TCMSConfig::GetInstance();
        $cmsConfig->SetConfigParameter(self::CRONJOB_TIMESTAMP_PARAMETER, (string) time(), true);
    }
}

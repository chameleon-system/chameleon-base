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

interface CronJobCheckDataAccessInterface
{
    /**
     * @return int
     */
    public function getTimestampOfLastCronJobCall();

    /**
     * @return void
     */
    public function refreshTimestampOfLastCronJobCall();
}

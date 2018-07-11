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

use ChameleonSystem\SanityCheck\Check\AbstractCheck;
use ChameleonSystem\SanityCheck\Outcome\CheckOutcome;

class CronJobCheck extends AbstractCheck
{
    /**
     * @var int
     */
    private $timestampThresholdInSeconds;
    /**
     * @var CronJobCheckDataAccessInterface
     */
    private $dataAccess;

    /**
     * @param int                             $level
     * @param CronJobCheckDataAccessInterface $dataAccess
     * @param int                             $timestampThresholdInSeconds
     */
    public function __construct($level, CronJobCheckDataAccessInterface $dataAccess, $timestampThresholdInSeconds)
    {
        parent::__construct($level);
        $this->timestampThresholdInSeconds = $timestampThresholdInSeconds;
        $this->dataAccess = $dataAccess;
    }

    /**
     * {@inheritdoc}
     */
    public function performCheck()
    {
        $lastCallTimestamp = $this->dataAccess->getTimestampOfLastCronJobCall();

        if (0 === $lastCallTimestamp) {
            return array(new CheckOutcome('check.cronjob.never', array(), $this->getLevel()));
        }

        $now = time();
        $delta = $now - $lastCallTimestamp;

        if ($delta > $this->timestampThresholdInSeconds) {
            return array(new CheckOutcome('check.cronjob.toolong', array('%0%' => $delta, '%1%' => $this->timestampThresholdInSeconds), $this->getLevel()));
        }

        return array(new CheckOutcome('check.cronjob.ok', array(), CheckOutcome::OK));
    }
}

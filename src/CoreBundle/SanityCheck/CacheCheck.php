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

/**
 * Class CacheCheck Checks if caching is activated outside dev mode.
 */
class CacheCheck extends AbstractCheck
{
    /**
     * @var bool
     */
    private $allowCache;
    /**
     * @var bool
     */
    private $memcacheActive;

    /**
     * @param int $level
     * @param bool $allowCache
     * @param bool $memcacheActive
     */
    public function __construct($level, $allowCache, $memcacheActive)
    {
        parent::__construct($level);
        $this->allowCache = $allowCache;
        $this->memcacheActive = $memcacheActive;
    }

    /**
     * @return array(CheckOutcome)
     */
    public function performCheck()
    {
        return $this->checkCache();
    }

    /**
     * @return CheckOutcome[]
     */
    private function checkCache()
    {
        $retValue = array();

        if (_DEVELOPMENT_MODE) {
            if ($this->allowCache && $this->memcacheActive) {
                $retValue[] = new CheckOutcome('check.cache.activedev', array(), CheckOutcome::NOTICE);
            } else {
                $retValue[] = new CheckOutcome('check.cache.inactivedev', array(), CheckOutcome::OK);
            }
        } else {
            if ($this->allowCache && $this->memcacheActive) {
                $retValue[] = new CheckOutcome('check.cache.active', array(), CheckOutcome::OK);
            } else {
                $retValue[] = new CheckOutcome('check.cache.notactive', array(), $this->getLevel());
            }
        }

        return $retValue;
    }
}

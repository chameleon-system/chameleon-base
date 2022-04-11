<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\UpdateCounterMigrationBundle\Exception;

use Exception;

class InvalidMigrationCounterException extends CounterMigrationException
{
    /**
     * @var string[]
     */
    private $invalidCounters;

    /**
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     * @param string[] $invalidCounters
     */
    public function __construct($message = '', $code = 0, Exception $previous = null, array $invalidCounters = array())
    {
        parent::__construct($message, $code, $previous);
        $this->invalidCounters = $invalidCounters;
    }

    /**
     * @return string[]
     */
    public function getInvalidCounters()
    {
        return $this->invalidCounters;
    }
}

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
use Doctrine\DBAL\Connection;
use Symfony\Component\Form\Exception\LogicException;

class SqlStrictModeCheck extends AbstractCheck
{
    const STRICT_ON = 'on';
    const STRICT_OFF = 'off';

    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var string
     */
    private $mode;

    /**
     * @param int $level
     * @param string $mode
     * @psalm-param self::STRICT_* $mode
     */
    public function __construct($level, Connection $connection, $mode)
    {
        parent::__construct($level);
        if (self::STRICT_ON !== $mode && self::STRICT_OFF !== $mode) {
            throw new LogicException('mode parameter must be one of (SqlStrictModeCheck::STRICT_ON, SqlStrictModeCheck::STRICT_OFF), got: "'.$mode.'"');
        }
        $this->connection = $connection;
        $this->mode = $mode;
    }

    /**
     * Perform this check.
     *
     * @return CheckOutcome[]
     */
    public function performCheck()
    {
        $retValue = array();

        $statement = $this->connection->query('SELECT @@sql_mode');
        $success = $statement->execute();
        if ($success) {
            $result = $statement->fetchColumn(0);
            $isStrict = false !== strpos($result, 'STRICT_TRANS_TABLES') || false !== strpos($result, 'STRICT_ALL_TABLES');
            if ($isStrict) {
                if (self::STRICT_OFF === $this->mode) {
                    $retValue[] = new CheckOutcome('check.sql_strict.mustnotbestrict', array(), $this->getLevel());
                }
            } else {
                if (self::STRICT_ON === $this->mode) {
                    $retValue[] = new CheckOutcome('check.sql_strict.mustbestrict', array(), $this->getLevel());
                }
            }
        } else {
            $retValue[] = new CheckOutcome('check.sql_strict.databaseerror', array('%0%' => $statement->errorCode()), CheckOutcome::EXCEPTION);
        }

        if (empty($retValue)) {
            $retValue[] = new CheckOutcome('check.sql_strict.ok', array(), CheckOutcome::OK);
        }

        return $retValue;
    }
}

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

/**
 * Class DatabaseUtf8Check Checks if all the character sets for the given database connection are set to UTF-8.
 */
class DatabaseUtf8Check extends AbstractCheck
{
    /** @var \Doctrine\DBAL\Connection */
    private $connection;

    /**
     * @param int $level
     * @param Connection $connection
     */
    public function __construct($level, Connection $connection)
    {
        parent::__construct($level);
        $this->connection = $connection;
    }

    /**
     * @return array(CheckOutcome)
     */
    public function performCheck()
    {
        $retValue = $this->checkUtf8();

        if (empty($retValue)) {
            $retValue[] = new CheckOutcome('check.database_utf8check.ok', array(), CheckOutcome::OK);
        }

        return $retValue;
    }

    /**
     * @return CheckOutcome[]
     */
    private function checkUtf8()
    {
        $retValue = array();

        /* Character Set */
        $query = "SHOW VARIABLES LIKE 'character\_set\_%'";
        $result = $this->connection->fetchAll($query);

        foreach ($result as $row) {
            $type = $row['Variable_name'];
            $value = $row['Value'];
            if ('character_set_filesystem' === $type && 'binary' === $value) {
                continue;
            }
            $isValid = preg_match('/utf8/', $value) > 0;
            if (false === $isValid) {
                $retValue[] = new CheckOutcome('check.database_utf8check.wrongcharset', array('%0%' => $type, '%1%' => $value), $this->getLevel());
            }
        }

        /* Collation */
        $query = "SHOW VARIABLES LIKE 'collation\_%'";
        $result = $this->connection->fetchAll($query);
        foreach ($result as $row) {
            $type = $row['Variable_name'];
            $value = $row['Value'];
            $isValid = preg_match('/utf8/', $value) > 0;

            if (false === $isValid) {
                $retValue[] = new CheckOutcome('check.database_utf8check.wrongcollation', array('%0%' => $type, '%1%' => $value), $this->getLevel());
            }
        }

        return $retValue;
    }
}

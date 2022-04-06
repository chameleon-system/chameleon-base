<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DebugBundle\Statement;

use ChameleonSystem\DebugBundle\Connection\ProfilerDatabaseConnection;
use Doctrine\DBAL\Driver\PDOStatement;
use PDO;

class ProfilerStatement extends PDOStatement
{
    /** @var PDOStatement $statement */
    private $statement;
    /**
     * @var \ChameleonSystem\DebugBundle\Connection\ProfilerDatabaseConnection
     */
    private $databaseConnection;

    public function __construct($statement, ProfilerDatabaseConnection $databaseConnection)
    {
        $this->statement = $statement;
        $this->databaseConnection = $databaseConnection;
    }

    public function execute($input_parameters = null)
    {
        $startTime = microtime(true);
        $result = $this->statement->execute($input_parameters);
        $this->databaseConnection->addToQueryTimer(microtime(true) - $startTime);

        return $result;
    }

    public function fetch($fetch_style = null, $cursor_orientation = PDO::FETCH_ORI_NEXT, $cursor_offset = 0)
    {
        $startTime = microtime(true);
        $result = $this->statement->fetch($fetch_style, $cursor_orientation, $cursor_offset);
        $this->databaseConnection->addToQueryTimer(microtime(true) - $startTime);

        return $result;
    }

    public function bindParam(
        $parameter,
        &$variable,
        $data_type = PDO::PARAM_STR,
        $length = null,
        $driver_options = null
    ) {
        $startTime = microtime(true);
        $result = $this->statement->bindParam(
            $parameter,
            $variable,
            $data_type,
            $length,
            $driver_options
        );
        $this->databaseConnection->addToQueryTimer(microtime(true) - $startTime);

        return $result;
    }

    public function bindColumn($column, &$param, $type = null, $maxlen = null, $driverdata = null)
    {
        $startTime = microtime(true);
        $result = $this->statement->bindColumn($column, $param, $type, $maxlen, $driverdata);
        $this->databaseConnection->addToQueryTimer(microtime(true) - $startTime);

        return $result;
    }

    public function bindValue($parameter, $value, $data_type = PDO::PARAM_STR)
    {
        $startTime = microtime(true);
        $result = $this->statement->bindValue($parameter, $value, $data_type);
        $this->databaseConnection->addToQueryTimer(microtime(true) - $startTime);

        return $result;
    }

    public function rowCount()
    {
        $startTime = microtime(true);
        $result = $this->statement->rowCount();
        $this->databaseConnection->addToQueryTimer(microtime(true) - $startTime);

        return $result;
    }

    public function fetchColumn($column_number = 0)
    {
        $startTime = microtime(true);
        $result = $result = $this->statement->fetchColumn($column_number);
        $this->databaseConnection->addToQueryTimer(microtime(true) - $startTime);

        return $result;
    }

    public function fetchAll($fetch_style = null, $fetch_argument = null, $ctor_args = null)
    {
        $startTime = microtime(true);
        $result = $this->statement->fetchAll($fetch_style, $fetch_argument, $ctor_args);
        $this->databaseConnection->addToQueryTimer(microtime(true) - $startTime);

        return $result;
    }

    public function fetchObject($class_name = null, $ctor_args = null)
    {
        $startTime = microtime(true);
        $result = $this->statement->fetchObject($class_name, $ctor_args);
        $this->databaseConnection->addToQueryTimer(microtime(true) - $startTime);

        return $result;
    }

    public function errorCode()
    {
        $startTime = microtime(true);
        $result = $this->statement->errorCode();
        $this->databaseConnection->addToQueryTimer(microtime(true) - $startTime);

        return $result;
    }

    public function errorInfo()
    {
        $startTime = microtime(true);
        $result = $this->statement->errorInfo();
        $this->databaseConnection->addToQueryTimer(microtime(true) - $startTime);

        return $result;
    }

    public function setAttribute($attribute, $value)
    {
        $startTime = microtime(true);
        $result = $this->statement->setAttribute($attribute, $value);
        $this->databaseConnection->addToQueryTimer(microtime(true) - $startTime);

        return $result;
    }

    public function getAttribute($attribute)
    {
        $startTime = microtime(true);
        $result = $this->statement->getAttribute($attribute);
        $this->databaseConnection->addToQueryTimer(microtime(true) - $startTime);

        return $result;
    }

    public function columnCount()
    {
        $startTime = microtime(true);
        $result = $this->statement->columnCount();
        $this->databaseConnection->addToQueryTimer(microtime(true) - $startTime);

        return $result;
    }

    public function getColumnMeta($column)
    {
        $startTime = microtime(true);
        $result = $this->statement->getColumnMeta($column);
        $this->databaseConnection->addToQueryTimer(microtime(true) - $startTime);

        return $result;
    }

    public function nextRowset()
    {
        $startTime = microtime(true);
        $result = $this->statement->nextRowset();
        $this->databaseConnection->addToQueryTimer(microtime(true) - $startTime);

        return $result;
    }

    public function closeCursor()
    {
        $startTime = microtime(true);
        $result = $this->statement->closeCursor();
        $this->databaseConnection->addToQueryTimer(microtime(true) - $startTime);

        return $result;
    }

    /**
     * @psalm-suppress AssignmentToVoid - Psalm thinks that `debugDumpParams` is a void function
     */
    public function debugDumpParams()
    {
        $startTime = microtime(true);
        $result = $this->statement->debugDumpParams();
        $this->databaseConnection->addToQueryTimer(microtime(true) - $startTime);

        return $result;
    }
}

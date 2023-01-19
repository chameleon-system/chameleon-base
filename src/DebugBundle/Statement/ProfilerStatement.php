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
use Doctrine\DBAL\Statement;
use Doctrine\DBAL\Driver\Statement as DriverStatement;
use PDO;

class ProfilerStatement extends Statement
{
    /** @var DriverStatement $statement */
    private $statement;
    /**
     * @var ProfilerDatabaseConnection
     */
    private $databaseConnection;

    /**
     * @param DriverStatement $statement
     * @param ProfilerDatabaseConnection $databaseConnection
     */
    public function __construct($statement, ProfilerDatabaseConnection $databaseConnection)
    {
        $this->statement = $statement;
        $this->databaseConnection = $databaseConnection;
    }

    /**
     * @param array|null $input_parameters
     * @return bool
     */
    public function execute($input_parameters = null)
    {
        $startTime = microtime(true);
        $result = $this->statement->execute($input_parameters);
        $this->databaseConnection->addToQueryTimer(microtime(true) - $startTime);

        return $result;
    }

    /**
     * @param int $fetch_style
     * @param int $cursor_orientation
     * @param int $cursor_offset
     * @return mixed
     */
    public function fetch($fetch_style = null, $cursor_orientation = PDO::FETCH_ORI_NEXT, $cursor_offset = 0)
    {
        $startTime = microtime(true);
        $result = $this->statement->fetch($fetch_style, $cursor_orientation, $cursor_offset);
        $this->databaseConnection->addToQueryTimer(microtime(true) - $startTime);

        return $result;
    }

    /**
     * @param int|string $parameter
     * @param mixed $variable
     * @param int $data_type
     * @param int $length
     * @param mixed $driver_options
     * @return bool
     */
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

    /**
     * @param int|string $parameter
     * @param mixed $value
     * @param int $data_type
     * @return bool
     */
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

    /**
     * @param int $column_number
     * @return null|scalar
     */
    public function fetchColumn($column_number = 0)
    {
        $startTime = microtime(true);
        $result = $result = $this->statement->fetchColumn($column_number);
        $this->databaseConnection->addToQueryTimer(microtime(true) - $startTime);

        return $result;
    }

    public function fetchAll($fetchMode = null, $fetchArgument = null, $ctorArgs = null)
    {
        $startTime = microtime(true);
        $result = $this->statement->fetchAll($fetchMode, $fetchArgument, $ctorArgs);
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

    /**
     * @param int $attribute
     * @param mixed $value
     * @return bool
     */
    public function setAttribute($attribute, $value)
    {
        $startTime = microtime(true);
        $result = $this->statement->setAttribute($attribute, $value);
        $this->databaseConnection->addToQueryTimer(microtime(true) - $startTime);

        return $result;
    }

    /**
     * @param int $attribute
     * @return mixed
     */
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

    public function closeCursor()
    {
        $startTime = microtime(true);
        $result = $this->statement->closeCursor();
        $this->databaseConnection->addToQueryTimer(microtime(true) - $startTime);

        return $result;
    }
}

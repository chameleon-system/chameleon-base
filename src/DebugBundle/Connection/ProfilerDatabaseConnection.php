<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DebugBundle\Connection;

use ChameleonSystem\DebugBundle\Statement\ProfilerStatement;
use Closure;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query;

class ProfilerDatabaseConnection extends Connection
{
    /**
     * @var \Doctrine\DBAL\Driver\Connection
     */
    private $connection;

    private $backtraceEnabled = false;

    private $backtraceLimit = 10;

    private $queries = array();
    private $queryTimer = 0;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function addToQueryTimer($time)
    {
        $this->queryTimer += $time;
    }

    public function query()
    {
        $args = func_get_args();
        $sql = $args[0];

        $result = null;
        $startTime = microtime(true);
        if (2 === count($args)) {
            $result = $this->connection->query($args[0], $args[1]);
        } else {
            $result = $this->connection->query($args[0]);
        }
        $time = (microtime(true) - $startTime);
        $this->queryTimer += $time;
        $this->queries[] = array($this->get_caller(__FUNCTION__), $sql, $this->getBacktraceForQuery(), $time);

        return new ProfilerStatement($result, $this);
    }

    public function fetchAssoc($statement, array $params = array(), array $types = array())
    {
        $sql = $statement;
        $startTime = microtime(true);
        $result = $this->connection->fetchAssoc($statement, $params, $types);
        $time = (microtime(true) - $startTime);
        $this->queryTimer += $time;
        $this->queries[] = array($this->get_caller(__FUNCTION__), $sql, $this->getBacktraceForQuery(), $time);

        return $result;
    }

    public function fetchArray($statement, array $params = array(), array $types = array())
    {
        $sql = $statement;
        $startTime = microtime(true);
        $result = $this->connection->fetchArray($statement, $params, $types);
        $time = (microtime(true) - $startTime);
        $this->queryTimer += $time;
        $this->queries[] = array($this->get_caller(__FUNCTION__), $sql, $this->getBacktraceForQuery(), $time);

        return $result;
    }

    public function fetchAll($sql, array $params = array(), $types = array())
    {
        $startTime = microtime(true);
        $result = $this->connection->fetchAll($sql, $params, $types);
        $time = (microtime(true) - $startTime);
        $this->queryTimer += $time;
        $this->queries[] = array($this->get_caller(__FUNCTION__), $sql, $this->getBacktraceForQuery(), $time);

        return $result;
    }

    public function getParams()
    {
        return $this->connection->getParams();
    }

    public function getDatabase()
    {
        return $this->connection->getDatabase();
    }

    public function getHost()
    {
        return $this->connection->getHost();
    }

    public function getPort()
    {
        return $this->connection->getPort();
    }

    public function getUsername()
    {
        return $this->connection->getUsername();
    }

    public function getPassword()
    {
        return $this->connection->getPassword();
    }

    public function getDriver()
    {
        return $this->connection->getDriver();
    }

    public function getConfiguration()
    {
        return $this->connection->getConfiguration();
    }

    public function getEventManager()
    {
        return $this->connection->getEventManager();
    }

    public function getDatabasePlatform()
    {
        return $this->connection->getDatabasePlatform();
    }

    public function getExpressionBuilder()
    {
        return $this->connection->getExpressionBuilder();
    }

    public function connect()
    {
        return $this->connection->connect();
    }

    public function setFetchMode($fetchMode)
    {
        $this->connection->setFetchMode($fetchMode);
    }

    public function fetchColumn($statement, array $params = array(), $column = 0, array $types = array())
    {
        $startTime = microtime(true);
        $result = $this->connection->fetchColumn($statement, $params, $column, $types);
        $time = (microtime(true) - $startTime);
        $this->queryTimer += $time;

        return $result;
    }

    public function isConnected()
    {
        return $this->connection->isConnected();
    }

    public function isTransactionActive()
    {
        return $this->connection->isTransactionActive();
    }

    public function delete($tableExpression, array $identifier, array $types = array())
    {
        $startTime = microtime(true);
        $result = $this->connection->delete($tableExpression, $identifier, $types);
        $time = (microtime(true) - $startTime);
        $this->queryTimer += $time;

        return $result;
    }

    public function close()
    {
        $this->connection->close();
    }

    public function setTransactionIsolation($level)
    {
        return $this->connection->setTransactionIsolation($level);
    }

    public function getTransactionIsolation()
    {
        return $this->connection->getTransactionIsolation();
    }

    public function update($tableName, array $data, array $identifier, array $types = array())
    {
        $startTime = microtime(true);
        $result = $this->connection->update($tableName, $data, $identifier, $types);
        $this->queryTimer += (microtime(true) - $startTime);

        return $result;
    }

    public function insert($tableName, array $data, array $types = array())
    {
        $startTime = microtime(true);
        $result = $this->connection->insert($tableName, $data, $types);
        $this->queryTimer += (microtime(true) - $startTime);

        return $result;
    }

    public function quoteIdentifier($str)
    {
        return $this->connection->quoteIdentifier($str);
    }

    public function quote($input, $type = null)
    {
        return $this->connection->quote($input, $type);
    }

    public function prepare($statement)
    {
        $startTime = microtime(true);
        $result = $this->connection->prepare($statement);
        $time = (microtime(true) - $startTime);
        $this->queryTimer += $time;

        return $result;
    }

    public function executeQuery($query, array $params = array(), $types = array(), QueryCacheProfile $qcp = null)
    {
        $startTime = microtime(true);
        $result = $this->connection->executeQuery($query, $params, $types, $qcp);
        $time = (microtime(true) - $startTime);
        $this->queryTimer += $time;
        $this->queries[] = array($this->get_caller(__FUNCTION__), $query, $this->getBacktraceForQuery(), $time);

        return $result;
    }

    public function executeCacheQuery($query, $params, $types, QueryCacheProfile $qcp)
    {
        $startTime = microtime(true);
        $result = $this->connection->executeCacheQuery($query, $params, $types, $qcp);
        $time = (microtime(true) - $startTime);
        $this->queryTimer += $time;
        $this->queries[] = array($this->get_caller(__FUNCTION__), $query, $this->getBacktraceForQuery(), $time);

        return $result;
    }

    public function project($query, array $params, Closure $function)
    {
        $startTime = microtime(true);
        $result = $this->connection->project($query, $params, $function);
        $this->queryTimer += (microtime(true) - $startTime);

        return $result;
    }

    public function executeUpdate($query, array $params = array(), array $types = array())
    {
        $startTime = microtime(true);
        $result = $this->connection->executeUpdate($query, $params, $types);
        $time = (microtime(true) - $startTime);
        $this->queryTimer += $time;

        return $result;
    }

    public function exec($statement)
    {
        $startTime = microtime(true);
        $result = $this->connection->exec($statement);
        $time = (microtime(true) - $startTime);
        $this->queryTimer += $time;
        $this->queries[] = array($this->get_caller(__FUNCTION__), $statement, $this->getBacktraceForQuery(), $time);

        return $result;
    }

    public function getTransactionNestingLevel()
    {
        return $this->connection->getTransactionNestingLevel();
    }

    public function errorCode()
    {
        return $this->connection->errorCode();
    }

    public function errorInfo()
    {
        return $this->connection->errorInfo();
    }

    public function lastInsertId($seqName = null)
    {
        return $this->connection->lastInsertId($seqName);
    }

    /**
     * @psalm-suppress InvalidReturnType
     * @FIXME Parent method returns `mixed`, we should add a `return` statement here.
     */
    public function transactional(Closure $func)
    {
        $this->connection->transactional($func);
    }

    /**
     * @psalm-suppress InvalidReturnStatement
     * @FIXME This is a `void` method, it should not return
     */
    public function setNestTransactionsWithSavepoints($nestTransactionsWithSavepoints)
    {
        return $this->connection->setNestTransactionsWithSavepoints(
            $nestTransactionsWithSavepoints
        );
    }

    public function getNestTransactionsWithSavepoints()
    {
        return $this->connection->getNestTransactionsWithSavepoints();
    }

    /**
     * @psalm-suppress InvalidReturnType
     * @FIXME Parent method returns `mixed`, we should add a `return` statement here.
     */
    public function beginTransaction()
    {
        $this->connection->beginTransaction();
    }

    /**
     * @psalm-suppress InvalidReturnType
     * @FIXME Parent method returns `mixed`, we should add a `return` statement here.
     */
    public function commit()
    {
        $this->connection->commit();
    }

    /**
     * @psalm-suppress InvalidReturnType
     * @FIXME Parent method returns `mixed`, we should add a `return` statement here.
     */
    public function rollBack()
    {
        $this->connection->rollBack();
    }

    public function createSavepoint($savepoint)
    {
        $this->connection->createSavepoint($savepoint);
    }

    public function releaseSavepoint($savepoint)
    {
        $this->connection->releaseSavepoint($savepoint);
    }

    public function rollbackSavepoint($savepoint)
    {
        $this->connection->rollbackSavepoint($savepoint);
    }

    public function getWrappedConnection()
    {
        return $this->connection->getWrappedConnection();
    }

    public function getSchemaManager()
    {
        return $this->connection->getSchemaManager();
    }

    public function setRollbackOnly()
    {
        $this->connection->setRollbackOnly();
    }

    public function isRollbackOnly()
    {
        return $this->connection->isRollbackOnly();
    }

    public function convertToDatabaseValue($value, $type)
    {
        return $this->connection->convertToDatabaseValue($value, $type);
    }

    public function convertToPHPValue($value, $type)
    {
        return $this->connection->convertToPHPValue($value, $type);
    }

    public function resolveParams(array $params, array $types)
    {
        return $this->connection->resolveParams($params, $types);
    }

    public function createQueryBuilder()
    {
        return $this->connection->createQueryBuilder();
    }

    /* This function will return the name string of the function that called $function. To return the
        caller of your function, either call get_caller(), or get_caller(__FUNCTION__).
    */
    public function get_caller($function = null, $use_stack = null)
    {
        if (is_array($use_stack)) {
            // If a function stack has been provided, used that.
            $stack = $use_stack;
        } else {
            // Otherwise create a fresh one.
            $stack = debug_backtrace();
        }

        if (is_string($function) && '' != $function) {
            // If we are given a function name as a string, go through the function stack and find
            // it's caller.
            for ($i = 0; $i < count($stack); ++$i) {
                $curr_function = $stack[$i];
                // Make sure that a caller exists, a function being called within the main script
                // won't have a caller.
                if ($curr_function['function'] == $function && ($i + 1) < count($stack)) {
                    return $stack[$i + 1]['function'];
                }
            }
        }

        return 'unknown';
    }

    protected function getBacktraceForQuery()
    {
        $backtrace = array();
        if (true === $this->backtraceEnabled) {
            $backtrace = debug_backtrace(~DEBUG_BACKTRACE_PROVIDE_OBJECT, $this->backtraceLimit);
        }

        return $backtrace;
    }

    /**
     * @return array
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * @return int
     */
    public function getQueryTimer()
    {
        return $this->queryTimer;
    }

    /**
     * @param bool $backtraceEnabled
     */
    public function setBacktraceEnabled($backtraceEnabled)
    {
        $this->backtraceEnabled = $backtraceEnabled;
    }

    /**
     * @param int $backtraceLimit
     */
    public function setBacktraceLimit($backtraceLimit)
    {
        $this->backtraceLimit = $backtraceLimit;
    }
}

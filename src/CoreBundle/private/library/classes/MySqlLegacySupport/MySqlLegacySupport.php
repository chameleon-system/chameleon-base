<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Statement;

if (!defined('MYSQL_ASSOC')) {
    define('MYSQL_ASSOC', 1);
}
if (!defined('MYSQL_NUM')) {
    define('MYSQL_NUM', 2);
}
if (!defined('MYSQL_BOTH')) {
    define('MYSQL_BOTH', 3);
}

/**
 * Class MySqlLegacySupport is used to replace the deprecated mysql_ calls by DBAL equivalent calls. Note that many methods are not supported at all.
 *
 * @deprecated  you should avoid using the database directly - always use it via an abstraction that contains those data access methods you need
 */
class MySqlLegacySupport
{
    /**
     * @var Connection
     */
    private $databaseConnection;
    /**
     * @var Statement|null
     */
    private $lastStatement = null;
    /**
     * @var DBALException|null
     */
    private $lastError;

    /**
     * @param Connection $databaseConnection
     */
    private function __construct(Connection $databaseConnection)
    {
        $this->databaseConnection = $databaseConnection;
    }

    /**
     * Get number of affected rows in previous MySQL operation.
     *
     * @param string|null $linkIdentifier
     *
     * @return int
     *
     * @throws TPkgCmsException_Log
     */
    public function affected_rows($linkIdentifier = null)
    {
        if (null !== $linkIdentifier) {
            throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_affected_rows does not support passing a link identifier');
        }
        if (null === $this->lastStatement) {
            return -1;
        }

        return $this->lastStatement->rowCount();
    }

    /**
     * Close MySQL connection.
     *
     * @param string|null $linkIdentifier
     *
     * @throws TPkgCmsException_Log
     */
    public function close($linkIdentifier = null)
    {
        if (null !== $linkIdentifier) {
            throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_affected_rows does not support passing a link identifier');
        }
        $this->databaseConnection->close();
    }

    /**
     * Open a connection to a MySQL Server.
     *
     * @param string|null $server
     * @param string|null $username
     * @param string|null $password
     * @param string|null $new_link
     * @param int         $client_flag
     *
     * @throws TPkgCmsException_Log
     */
    public function connect($server = null, $username = null, $password = null, $new_link = null, $client_flag = 0)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_connect should not be used. use Doctrine\DBAL\Connection instead');
    }

    /**
     * Move internal result pointer.
     *
     * @param Statement $resultSet
     * @param int       $row_number
     *
     * @throws TPkgCmsException_Log
     */
    public function data_seek($resultSet, $row_number)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::data_seek not supported since mysql does not support PDO_CURSOR_SCROLL and working with a local copy should be done within the class that is handling it (only it knows how to do this efficiently');
    }

    /**
     * Returns the numerical value of the error message from previous MySQL operation.
     *
     * @param string|null $linkIdentifier
     *
     * @return int|mixed
     *
     * @throws TPkgCmsException_Log
     */
    public function errno($linkIdentifier = null)
    {
        if (null !== $linkIdentifier) {
            throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_errno does not support passing a link identifier');
        }
        if (null === $this->lastError) {
            return 0;
        }
        if (null !== $this->lastError->getPrevious()) {
            return $this->lastError->getPrevious()->getCode();
        }

        return $this->lastError->getCode();
    }

    /**
     * Returns the text of the error message from previous MySQL operation.
     *
     * @param string|null $linkIdentifier
     *
     * @return string
     *
     * @throws TPkgCmsException_Log
     */
    public function error($linkIdentifier = null)
    {
        if (null !== $linkIdentifier) {
            throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_error does not support passing a link identifier');
        }
        if (null === $this->lastError) {
            return '';
        }

        return (string) $this->lastError;
    }

    /**
     * Escapes a string for use in a * mysql_query.
     *
     * @param string $unescaped_string
     *
     * @return bool|string
     */
    public function escape_string($unescaped_string)
    {
        return $this->real_escape_string($unescaped_string);
    }

    /**
     * Escapes special characters in a string for use in an SQL statement.
     *
     * @param string      $unescaped_string
     * @param string|null $link_identifier
     *
     * @return bool|string
     *
     * @throws TPkgCmsException_Log
     */
    public function real_escape_string($unescaped_string, $link_identifier = null)
    {
        if (null !== $link_identifier) {
            throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_fetch_array does not support passing a link identifier');
        }
        $escaped = $this->databaseConnection->quote($unescaped_string);

        // quote adds quotes and escapes. mysql_real_escape_string only escapes. so we need to remove the quotes
        if ("'" === substr($escaped, 0, 1) && "'" === substr($escaped, -1)) {
            $escaped = substr($escaped, 1, -1);
        }

        return $escaped;
    }

    /**
     * Fetch a result row as an associative array, a numeric array, or both.
     *
     * @param Statement $result
     * @param int       $result_type
     *
     * @return mixed
     */
    public function fetch_array($result, $result_type = MYSQL_BOTH)
    {
        $mappedType = null;
        switch ($result_type) {
            case MYSQL_ASSOC:
                $mappedType = \PDO::FETCH_ASSOC;
                break;
            case MYSQL_NUM:
                $mappedType = \PDO::FETCH_NUM;
                break;
            case MYSQL_BOTH:
                $mappedType = \PDO::FETCH_BOTH;
                break;
        }

        return $result->fetch($mappedType);
    }

    /**
     * Fetch a result row as an associative array.
     *
     * @param Statement $result
     *
     * @return mixed
     */
    public function fetch_assoc($result)
    {
        return $result->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Get column information from a result and return as an object.
     *
     * @param Statement $result
     * @param int       $field_offset
     *
     * @throws TPkgCmsException_Log
     */
    public function fetch_field($result, $field_offset = 0)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_fetch_field not supported');
    }

    /**
     * Get the length of each output in a result.
     *
     * @param Statement $result
     *
     * @throws TPkgCmsException_Log
     */
    public function fetch_lengths($result)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_fetch_lengths not supported');
    }

    /**
     * Fetch a result row as an object.
     *
     * @param Statement   $result
     * @param string|null $class_name
     * @param array|null  $params
     *
     * @return mixed
     *
     * @throws TPkgCmsException_Log
     */
    public function fetch_object($result, $class_name = null, array $params = null)
    {
        if (null !== $class_name) {
            throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_fetch_object does not support passing a class');
        }

        return $result->fetch(\PDO::FETCH_OBJ);
    }

    /**
     * Get a result row as an enumerated array.
     *
     * @param Statement $result
     *
     * @return mixed
     */
    public function fetch_row($result)
    {
        return $result->fetch(\PDO::FETCH_NUM);
    }

    /**
     * Get the ID generated in the last query.
     *
     * @param string|null $link_identifier
     *
     * @return string
     *
     * @throws TPkgCmsException_Log
     */
    public function insert_id($link_identifier = null)
    {
        if (null !== $link_identifier) {
            throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_insert_id does not support passing a link identifier');
        }

        return $this->databaseConnection->lastInsertId();
    }

    /**
     * List MySQL table fields.
     *
     * @param string      $database_name
     * @param string      $table_name
     * @param string|null $link_identifier
     *
     * @throws TPkgCmsException_Log
     */
    public function list_fields($database_name, $table_name, $link_identifier = null)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_list_fields is no longer supported!');
    }

    /**
     * List tables in a MySQL database.
     *
     * @param string      $database
     * @param string|null $link_identifier
     *
     * @throws TPkgCmsException_Log
     */
    public function list_tables($database, $link_identifier = null)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_list_tables is no longer supported!');
    }

    /**
     * Get number of fields in result.
     *
     * @param Statement $result
     *
     * @return int
     */
    public function num_fields($result)
    {
        return $result->columnCount();
    }

    /**
     * Get number of rows in result.
     *
     * @param Statement $result
     *
     * @return int
     */
    public function num_rows($result)
    {
        return $result->rowCount();
    }

    /**
     * Send a MySQL query.
     *
     * @param string      $query
     * @param string|null $link_identifier
     *
     * @return Statement
     *
     * @throws TPkgCmsException_Log
     */
    public function query($query, $link_identifier = null)
    {
        if (null !== $link_identifier) {
            throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_insert_id does not support passing a link identifier');
        }

        $this->lastStatement = false;
        try {
            $this->lastStatement = $this->databaseConnection->query($query);
            $this->lastError = null;
        } catch (DBALException $e) {
            $this->lastStatement = false;
            $this->lastError = $e;
        }

        return $this->lastStatement;
    }

    /**
     * Sets the client character set.
     */
    public function set_charset($charset, $link_identifier = null)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_set_charset is no longer supported!');
    }

    /**
     * Select a MySQL database.
     */
    public function select_db($database_name, $link_identifier = null)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_select_db is no longer supported!');
    }

    /**
     * Returns the name of the character set.
     */
    public function client_encoding($link_identifier = null)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_client_encoding is no longer supported!');
    }

    /**
     * Create a MySQL database.
     */
    public function create_db($database_name, $link_identifier = null)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_create_db is no longer supported!');
    }

    /**
     * Retrieves database name from the call to * mysql_list_dbs.
     */
    public function db_name($result, $row, $field = null)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_db_name is no longer supported!');
    }

    /**
     * Selects a database and executes a query on it.
     */
    public function db_query($database, $query, $link_identifier = null)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_db_query is no longer supported!');
    }

    /**
     * Drop (delete) a MySQL database.
     */
    public function drop_db($database_name, $link_identifier = null)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_drop_db is no longer supported!');
    }

    /**
     * Get the flags associated with the specified field in a result.
     */
    public function field_flags($result, $field_offset)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_field_flags is no longer supported!');
    }

    /**
     * Returns the length of the specified field.
     */
    public function field_len($result, $field_offset)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_field_len is no longer supported!');
    }

    /**
     * Get the name of the specified field in a result.
     */
    public function field_name($result, $field_offset)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_field_name is no longer supported!');
    }

    /**
     * Set result pointer to a specified field offset.
     */
    public function field_seek($result, $field_offset)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_field_seek is no longer supported!');
    }

    /**
     * Get name of the table the specified field is in.
     */
    public function field_table($result, $field_offset)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_field_table is no longer supported!');
    }

    /**
     * Get the type of the specified field in a result.
     */
    public function field_type($result, $field_offset)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_field_type is no longer supported!');
    }

    /**
     * Free result memory.
     *
     * @param Statement $result
     */
    public function free_result($result)
    {
        $result->closeCursor();
    }

    /**
     * Get MySQL client info.
     */
    public function get_client_info()
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_get_client_info is no longer supported!');
    }

    /**
     * Get MySQL host info.
     */
    public function get_host_info($link_identifier = null)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_get_host_info is no longer supported!');
    }

    /**
     * Get MySQL protocol info.
     */
    public function get_proto_info($link_identifier = null)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_get_proto_info is no longer supported!');
    }

    /**
     * Get MySQL server info.
     */
    public function get_server_info($link_identifier = null)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_get_server_info is no longer supported!');
    }

    /**
     * Get information about the most recent query.
     */
    public function info($link_identifier = null)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_info is no longer supported!');
    }

    /**
     * List databases available on a MySQL server.
     */
    public function list_dbs($link_identifier = null)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_list_dbs is no longer supported!');
    }

    /**
     * List MySQL processes.
     */
    public function list_processes($link_identifier = null)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_list_processes is no longer supported!');
    }

    /**
     * Open a persistent connection to a MySQL server.
     */
    public function pconnect($server = null, $username = null, $password = null, $new_link = null, $client_flag = 0)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_pconnect is no longer supported!');
    }

    /**
     * Ping a server connection or reconnect if there is no connection.
     */
    public function ping($link_identifier = null)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_ping is no longer supported!');
    }

    /**
     * Get result data.
     *
     * @param Statement $result
     * @param array     $row
     * @param int       $field
     *
     * @return string|bool
     */
    public function result($result, $row, $field = 0)
    {
        $result->fetch(\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_NEXT, $row);

        return $result->fetchColumn($field);
    }

    /**
     * Get current system status.
     */
    public function stat($link_identifier = null)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_stat is no longer supported!');
    }

    /**
     * Get table name of field.
     */
    public function tablename($result, $i)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_tablename is no longer supported!');
    }

    /**
     * Return the current thread ID.
     */
    public function thread_id($link_identifier = null)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_thread_id is no longer supported!');
    }

    /**
     * Sen.
     */
    public function unbuffered_query($query, $link_identifier = null)
    {
        throw new TPkgCmsException_Log('MySqlLegacySupport::mysql_unbuffered_query is no longer supported!');
    }

    /**
     * @return MySqlLegacySupport
     */
    public static function getInstance()
    {
        static $instance = null;

        if (null !== $instance) {
            return $instance;
        }

        $instance = new self(ServiceLocator::get('database_connection'));

        return $instance;
    }
}

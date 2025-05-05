<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\DataAccess;

use ChameleonSystem\CoreBundle\Exception\DataAccessException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;

class DataAccessClassFromTableTableFieldProvider implements DataAccessClassFromTableFieldProviderInterface
{
    /**
     * @var Connection
     */
    private $databaseConnection;

    public function __construct(Connection $databaseConnection)
    {
        $this->databaseConnection = $databaseConnection;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldClassNameFromDictionaryValues($tableName, $fieldName)
    {
        $query = $this->getFieldClassRowQuery();

        try {
            $fieldClassName = $this->databaseConnection->fetchOne($query, [
                'tableName' => $tableName,
                'fieldName' => $fieldName,
            ]);
            if (false === $fieldClassName) {
                return null;
            }
        } catch (DBALException $exception) {
            throw new DataAccessException(sprintf("Expected executable internal query to get field class name from table '%s' and field '%s', got: '%s'.", $tableName, $fieldName, $exception->getMessage()), 0, $exception);
        }

        return $fieldClassName;
    }

    /**
     * Provides a query to fetch a field class row from a table and field name.
     *
     * @return string
     */
    private function getFieldClassRowQuery()
    {
        return '  SELECT `cms_field_type`.`fieldclass`
                    FROM `cms_tbl_conf`
               LEFT JOIN `cms_field_conf`
                      ON `cms_field_conf`.`cms_tbl_conf_id` = `cms_tbl_conf`.`id`
                     AND `cms_field_conf`.`name` = :fieldName
               LEFT JOIN `cms_field_type`
                      ON `cms_field_type`.`id` = `cms_field_conf`.`cms_field_type_id`
                   WHERE `cms_tbl_conf`.`name` = :tableName';
    }
}

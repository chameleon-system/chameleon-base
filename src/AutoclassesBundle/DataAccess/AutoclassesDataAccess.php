<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\AutoclassesBundle\DataAccess;

use Doctrine\DBAL\Connection;
use PDO;
use TCMSConfig;
use TCMSField;
use TCMSTableToField_TCMSFieldType;
use TCMSTableToField_TCMSTableFieldConf;
use TIterator;

class AutoclassesDataAccess implements AutoclassesDataAccessInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getTableExtensionData()
    {
        $data = array();
        $query = 'SELECT *
                  FROM `cms_tbl_extension`
                  ORDER BY `position` DESC';
        $statement = $this->connection->executeQuery($query);
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[$row['cms_tbl_conf_id']][] = $row;
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldData()
    {
        $data = array();
        $query = 'SELECT `cms_field_conf`.*, `cms_tbl_conf`.`name` AS tablename
                  FROM `cms_field_conf`
                  INNER JOIN `cms_tbl_conf` ON `cms_field_conf`.`cms_tbl_conf_id` = `cms_tbl_conf`.`id`
                  ORDER BY `position` ASC';
        $statement = $this->connection->executeQuery($query);
        $fieldTypes = array();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $tableConfId = $row['cms_tbl_conf_id'];
            if (false === isset($data[$tableConfId])) {
                $data[$tableConfId] = new TIterator();
            }
            $className = trim($row['fieldclass']);

            $fieldType = $row['cms_field_type_id'];
            if (false === isset($fieldTypes[$fieldType])) {
                $fieldDef = new TCMSTableToField_TCMSFieldType();
                $fieldDef->Load($fieldType);
                $fieldTypes[$fieldType] = $fieldDef;
            } else {
                $fieldDef = $fieldTypes[$fieldType];
            }

            if ('' === $className) {
                $className = trim($fieldDef->sqlData['fieldclass']);
            }

            /** @var $field TCMSField */
            $field = new $className();
            $field->setDatabaseConnection($this->connection);
            $field->data = $row['field_default_value'];
            $field->sTableName = $row['tablename'];
            $field->name = $row['name'];
            $field->oDefinition = new TCMSTableToField_TCMSTableFieldConf($fieldDef);
            $field->oDefinition->LoadFromRow($row);
            $data[$tableConfId]->AddItem($field);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $cmsConfig = new TCMSConfig();
        $cmsConfig->Load(1);

        return $cmsConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getTableOrderByData()
    {
        $data = array();

        $query = 'SELECT `name`, `sort_order_direction`, `cms_tbl_conf_id` 
                  FROM `cms_tbl_display_orderfields` 
                  ORDER BY `position` ASC';
        $statement = $this->connection->executeQuery($query);

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $tableConfId = $row['cms_tbl_conf_id'];
            if (false === isset($data[$tableConfId])) {
                $data[$tableConfId] = array();
            }
            $data[$tableConfId][] = $row;
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getTableConfigData()
    {
        $data = array();

        $query = 'SELECT * 
                  FROM `cms_tbl_conf`';
        $statement = $this->connection->executeQuery($query);
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[$row['id']] = $row;
        }

        return $data;
    }
}

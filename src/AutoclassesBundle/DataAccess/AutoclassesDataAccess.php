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
use TCMSField;

class AutoclassesDataAccess implements AutoclassesDataAccessInterface
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getTableExtensionData()
    {
        $data = [];
        $query = 'SELECT *
                  FROM `cms_tbl_extension`
                  ORDER BY `position` DESC';
        $result = $this->connection->executeQuery($query);
        while ($row = $result->fetchAssociative()) {
            $data[$row['cms_tbl_conf_id']][] = $row;
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldData()
    {
        $data = [];
        $query = 'SELECT `cms_field_conf`.*, `cms_tbl_conf`.`name` AS tablename
                  FROM `cms_field_conf`
                  INNER JOIN `cms_tbl_conf` ON `cms_field_conf`.`cms_tbl_conf_id` = `cms_tbl_conf`.`id`
                  ORDER BY `position` ASC';
        $result = $this->connection->executeQuery($query);
        $fieldTypes = [];
        while ($row = $result->fetchAssociative()) {
            $tableConfId = $row['cms_tbl_conf_id'];
            if (false === isset($data[$tableConfId])) {
                $data[$tableConfId] = new \TIterator();
            }

            /** @psalm-var class-string<TCMSField> $className */
            $className = trim($row['fieldclass']);

            $fieldType = $row['cms_field_type_id'];
            if (false === isset($fieldTypes[$fieldType])) {
                $fieldDef = new \TCMSTableToField_TCMSFieldType($this->connection);
                $fieldDef->Load($fieldType);
                $fieldTypes[$fieldType] = $fieldDef;
            } else {
                $fieldDef = $fieldTypes[$fieldType];
            }

            if ('' === $className) {
                $className = trim($fieldDef->sqlData['fieldclass']);
            }

            /** @var \TCMSField $field */
            $field = new $className();
            $field->setDatabaseConnection($this->connection);
            $field->data = $row['field_default_value'];
            $field->sTableName = $row['tablename'];
            $field->name = $row['name'];
            $field->oDefinition = new \TCMSTableToField_TCMSTableFieldConf($this->connection, $fieldDef);
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
        $cmsConfig = new \TCMSConfig();
        $cmsConfig->Load(1);

        return $cmsConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getTableOrderByData()
    {
        $query = 'SELECT * FROM `cms_tbl_display_orderfields` ORDER BY `position` ASC';
        $result = $this->connection->executeQuery($query);

        $data = [];
        while ($row = $result->fetchAssociative()) {
            $tableConfId = $row['cms_tbl_conf_id'];
            if (false === isset($data[$tableConfId])) {
                $data[$tableConfId] = [];
            }
            // we try to circumvent a chicken and egg problem here:
            // the field only_backend was added in a later migration and that migration may not have been executed yet
            // in this way we only keep the fields that are needed and a missing only_backend column will be ignored
            $data[$tableConfId][] = array_filter($row, function ($key) {
                return in_array($key, ['name', 'sort_order_direction', 'cms_tbl_conf_id', 'only_backend'], true);
            }, ARRAY_FILTER_USE_KEY);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getTableConfigData()
    {
        $data = [];

        $query = 'SELECT *
                  FROM `cms_tbl_conf`';
        $result = $this->connection->executeQuery($query);
        while ($row = $result->fetchAssociative()) {
            $data[$row['id']] = $row;
        }

        return $data;
    }
}

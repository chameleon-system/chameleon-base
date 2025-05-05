<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\DatabaseAccessLayer;

use TCMSField;

class DatabaseAccessLayerFieldConfig extends AbstractDatabaseAccessLayer
{
    /**
     * @var \TCMSField[][]
     */
    private $cache = [];

    /**
     * @param string $tableName
     * @param string $fieldName
     * @param bool $loadDefaults
     *
     * @return \TCMSField
     */
    public function getFieldConfig($tableName, $fieldName, \TCMSRecord $dataRow, $loadDefaults = false)
    {
        $fieldObject = null;
        if (!isset($this->cache[$tableName])) {
            $this->cache[$tableName] = [];
        }
        if (isset($this->cache[$tableName][$fieldName])) {
            $fieldObject = $this->cache[$tableName][$fieldName];
        }

        if (null === $fieldObject) {
            $fieldObject = $this->GetField($tableName, $fieldName);
        }

        $fieldObject = $this->setTableRowData($fieldObject, $dataRow, $loadDefaults);
        $this->cache[$tableName][$fieldName] = $fieldObject;

        return $this->cache[$tableName][$fieldName];
    }

    /**
     * returns the TCMSField object of a field.
     *
     * @param string $tableName
     * @param string $fieldName
     *
     * @return \TCMSField|null
     */
    private function GetField($tableName, $fieldName)
    {
        $oField = null;
        $oFieldDef = $this->GetFieldDefinition($tableName, $fieldName);
        if (null !== $oFieldDef) {
            $oField = $oFieldDef->GetFieldObject();
            $oField->sTableName = $tableName;
            $oField->name = $oFieldDef->sqlData['name'];
            $oField->oDefinition = $oFieldDef;
        }

        return $oField;
    }

    /**
     * @param bool $loadDefaultValue
     *
     * @return \TCMSField
     */
    private function setTableRowData(\TCMSField $oField, \TCMSRecord $oTableRow, $loadDefaultValue)
    {
        $oField->recordId = $oTableRow->id;
        $oField->oTableRow = $oTableRow;
        if ($loadDefaultValue) {
            $oField->data = $oField->oDefinition->sqlData['field_default_value'];
        } elseif (isset($oTableRow->sqlData[$oField->name])) {
            $oField->data = $oTableRow->sqlData[$oField->name];
        }

        return $oField;
    }

    /**
     * returns a fielddefinition for a given field.
     *
     * @param string $tableName
     * @param string $fieldName the sql name of the field
     *
     * @return \TdbCmsFieldConf|null
     */
    public function GetFieldDefinition($tableName, $fieldName)
    {
        $query = 'SELECT `cms_field_conf`.*
                    FROM `cms_field_conf`
              INNER JOIN `cms_tbl_conf` ON `cms_field_conf`.`cms_tbl_conf_id` = `cms_tbl_conf`.`id`
                 WHERE `cms_field_conf`.`name`= :fieldName
                   AND `cms_tbl_conf`.`name` = :tableName';
        $oFieldDefinition = null;
        $row = $this->getDatabaseConnection()->fetchAssociative($query, ['fieldName' => $fieldName, 'tableName' => $tableName]);

        if (false !== $row) {
            $oFieldDefinition = \TdbCmsFieldConf::GetNewInstance($row);
        }

        return $oFieldDefinition;
    }
}

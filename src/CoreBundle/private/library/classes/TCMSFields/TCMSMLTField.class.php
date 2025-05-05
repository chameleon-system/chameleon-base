<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\AutoclassesBundle\TableConfExport\DataModelParts;
use ChameleonSystem\AutoclassesBundle\TableConfExport\DoctrineTransformableInterface;

abstract class TCMSMLTField extends TCMSField implements DoctrineTransformableInterface
{
    public function __construct()
    {
        $this->isMLTField = true;
    }

    public function getDoctrineDataModelParts(string $namespace, array $tableNamespaceMapping): DataModelParts
    {
        $propertyName = $this->name;
        if (str_ends_with($propertyName, '_mlt')) {
            $propertyName = substr($propertyName, 0, -4);
        }
        $targetClass = $this->snakeToPascalCase($this->GetForeignTableName());
        $parameters = [
            'source' => get_class($this),
            'type' => $targetClass,
            'description' => $this->oDefinition->sqlData['translation'],
            'propertyName' => $this->snakeToCamelCase($propertyName.'_collection'),
            'methodParameter' => $this->snakeToCamelCase($this->name),
        ];
        $parameters['docCommentType'] = sprintf('Collection<int, %s>', $parameters['type']);

        $propertyCode = $this->getDoctrineRenderer('model/many-to-many.property.php.twig', $parameters)->render();
        $methodCode = $this->getDoctrineRenderer('model/many-to-many.methods.php.twig', $parameters)->render();

        return new DataModelParts(
            $propertyCode,
            $methodCode,
            $this->getDoctrineDataModelXml($namespace, $tableNamespaceMapping),
            [
                ltrim(sprintf('%s\\%s', $tableNamespaceMapping[$this->GetForeignTableName()], $targetClass), '\\'),
                'Doctrine\\Common\\Collections\\Collection',
                'Doctrine\\Common\\Collections\\ArrayCollection',
            ],
            true
        );
    }

    protected function getDoctrineDataModelXml(string $namespace, $tableNamespaceMapping): string
    {
        $propertyName = $this->name;
        if (str_ends_with($propertyName, '_mlt')) {
            $propertyName = substr($propertyName, 0, -4);
        }

        $viewName = 'mapping/many-to-many.xml.twig';

        return $this->getDoctrineRenderer($viewName, [
            'fieldName' => $this->snakeToCamelCase($propertyName.'_collection'),
            'targetClass' => sprintf(
                '%s\\%s',
                $tableNamespaceMapping[$this->GetForeignTableName()],
                $this->snakeToPascalCase($this->GetForeignTableName())
            ),
            'joinTable' => $this->GetMLTTableName(),
            'comment' => $this->oDefinition->sqlData['translation'],
        ])->render();
    }

    public function getMltValues()
    {
        $sMltTableName = $this->GetMLTTableName();
        $oFieldTableRow = $this->oTableRow;
        $aConnectedIds = [];

        if (is_array($oFieldTableRow->sqlData) && array_key_exists('id', $oFieldTableRow->sqlData)
            && !empty($oFieldTableRow->sqlData['id'])) {
            $recordId = $oFieldTableRow->sqlData['id'];

            $oTableConf = new TCMSTableConf();
            $oTableConf->LoadFromField('name', $this->sTableName);

            $sAlreadyConnectedQuery = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string(
                $sMltTableName
            )."` WHERE `source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($recordId)."'";
            $tRes = MySqlLegacySupport::getInstance()->query($sAlreadyConnectedQuery);
            while ($aRow = MySqlLegacySupport::getInstance()->fetch_assoc($tRes)) {
                $aConnectedIds[] = $aRow['target_id'];
            }
        }

        return $aConnectedIds;
    }

    /**
     * @return string
     */
    abstract public function GetMLTTableName();

    /**
     * @return TCMSRecordList
     */
    abstract public function FetchMLTRecords();

    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        if (!$this->data) {
            return '';
        }
        $sTableName = $this->GetConnectedTableName();
        $sTableId = TTools::GetCMSTableId($sTableName);
        if (!$sTableId) {
            return '';
        }
        $oTableConf = new TCMSTableConf($sTableId);
        $sNameColumn = $oTableConf->GetNameColumn();
        if (null == $sNameColumn) {
            return '';
        }

        return $this->getDataAsString($sTableName, $sNameColumn);
    }

    /**
     * @param string $tableName
     * @param string $nameColumn
     *
     * @return string
     *
     * @throws ErrorException
     * @throws TPkgCmsException_Log
     */
    protected function getDataAsString($tableName, $nameColumn)
    {
        $databaseConnection = $this->getDatabaseConnection();
        $quotedTableName = $databaseConnection->quoteIdentifier($tableName);
        $quotedNameColumn = $databaseConnection->quoteIdentifier($nameColumn);
        if (is_array($this->data)) {
            $idList = join(',', array_map([$databaseConnection, 'quote'], $this->data));
        } else {
            $idList = $databaseConnection->quote($this->data);
        }
        $sQuery = "SELECT `id`, $quotedNameColumn FROM $quotedTableName WHERE `id` in ($idList) ORDER BY $quotedNameColumn";

        try {
            $result = $databaseConnection->executeQuery($sQuery);
        } catch (Doctrine\DBAL\Exception $e) {
            // If the query fails, handle it here
            return '';
        }

        $aRetValueArray = [];
        while ($row = $result->fetchAssociative()) {
            $aRetValueArray[] = $row[$nameColumn];
        }

        return implode(', ', $aRetValueArray);
    }

    /**
     * returns the name of the connected table.
     * Get connected table name form field or from field configuration (connectedTableName)
     * If table name comes from field name _mlt and field counts will be filtered.
     *
     * @return string
     */
    protected function GetForeignTableName()
    {
        $sForeignTableName = $this->GetConnectedTableName();

        return $sForeignTableName;
    }

    /**
     * Returns the name of the table this field is connected with
     * Get connected table name from field config (connectedTableName) or from field name.
     *
     * @return string
     */
    public function GetConnectedTableName($bExistingCount = true)
    {
        $sTableName = $this->oDefinition->GetFieldtypeConfigKey('connectedTableName');
        $sTableName = $this->GetClearedTableName($sTableName);
        $sFieldLastCharacter = substr($sTableName, -1);
        if (is_numeric($sFieldLastCharacter) && 'mp3' != $sFieldLastCharacter && $bExistingCount) {
            $sTableName = substr($sTableName, 0, -1);
        }

        return $sTableName;
    }

    /**
     * Returns the cleared table without _mlt post fix.
     * If given table name was empty get table name from field name.
     *
     * @param string $sTableName can be mlt field name or table name
     * @param array $aFieldData If filled get table name from sql field data (only if given table name was empty)
     *
     * @return string|null
     */
    protected function GetClearedTableName($sTableName, $aFieldData = [])
    {
        if (is_null($sTableName) || empty($sTableName)) {
            $sName = $this->name;
            if (count($aFieldData) > 0) {
                $sName = $aFieldData['name'];
            }
            if ('_mlt' == mb_substr($sName, -4)) {
                $sTableName = mb_substr($sName, 0, -4);
            } else {
                $sTableName = $sName;
            }
        } else {
            if ('_mlt' == mb_substr($sTableName, -4)) {
                $sTableName = mb_substr($sTableName, 0, -4);
            }
        }

        return $sTableName;
    }

    /**
     * generates the filter query for FetchMLTRecords().
     *
     * @return string
     */
    protected function GetMLTFilterQuery()
    {
        $foreignTableName = $this->GetForeignTableName();

        $oTableConf = new TCMSTableConf();
        $oTableConf->LoadFromField('name', $foreignTableName);
        $sNameField = $oTableConf->GetNameColumn();

        $query = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($foreignTableName).'` AS parenttable
    WHERE 1=1
    '.$this->GetMLTRecordRestrictions().'';
        $bShowCustomsort = $this->oDefinition->GetFieldtypeConfigKey('bAllowCustomSortOrder');
        if (true == $bShowCustomsort) {
            $query .= 'ORDER BY MLT.`entry_sort` ASC , `parenttable`.`'.MySqlLegacySupport::getInstance()
                    ->real_escape_string($sNameField).'`';
        }

        return $query;
    }

    protected function GetMLTRecordRestrictions()
    {
        return '';
    }
}

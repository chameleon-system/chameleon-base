<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\DatabaseMigration\DataModel\LogChangeDataModel;
use ChameleonSystem\DatabaseMigration\Query\MigrationQueryData;
use Doctrine\Common\Collections\Expr\Comparison;

/**
 * A yes/no field that allows at most one record in the table to be set to yes.
 * /**/
class TCMSFieldUniqueMarker extends TCMSFieldBoolean
{
    /**
     * {@inheritdoc}
     */
    public function PreGetSQLHook()
    {
        // we need to check if this record is set as the unique record and reset all other records
        if ('1' != $this->ConvertPostDataToSQL()) {
            return true;
        }

        $oTableConf = $this->oTableRow->GetTableConf();
        $databaseConnection = $this->getDatabaseConnection();

        $setFields = [];
        $whereEquals = [];
        $whereExpressions = [];

        $quotedTableName = $databaseConnection->quoteIdentifier($oTableConf->sqlData['name']);
        $quotedFieldName = $databaseConnection->quoteIdentifier($this->name);

        $updateQuery = "UPDATE $quotedTableName SET $quotedFieldName = '0' WHERE `id` != ? AND $quotedFieldName = '1' ";
        $parameters = [
            $this->oTableRow->id,
        ];
        $setFields[$this->name] = '0';
        $whereExpressions[] = new Comparison('id', Comparison::NEQ, $this->oTableRow->id);
        $whereEquals[$this->name] = '1';

        // check if the table has a foreign key field
        /**
         * @var TdbCmsFieldConfList $oFields
         */
        $oFields = $oTableConf->GetFieldDefinitions(['CMSFIELD_PROPERTY_PARENT_ID'], true);
        if (1 === $oFields->Length()) {
            $oField = $oFields->Current();
            $nameField = $oField->sqlData['name'];
            $quotedNameField = $databaseConnection->quoteIdentifier($nameField);
            $nameValue = $this->oTableRow->sqlData[$nameField];

            $updateQuery .= " AND $quotedNameField = ? ";
            $parameters[] = $nameValue;

            $whereEquals[$nameField] = $nameValue;
        }

        // also allow restricting based on a field in the current row
        $sRestriction = $this->oDefinition->GetFieldtypeConfigKey('restriction');
        if (!empty($sRestriction) && array_key_exists($sRestriction, $this->oTableRow->sqlData)) {
            $quotedRestrictionField = $databaseConnection->quoteIdentifier($sRestriction);
            $restrictionValue = $this->oTableRow->sqlData[$sRestriction];

            $updateQuery .= " AND $quotedRestrictionField = ? ";
            $parameters[] = $restrictionValue;

            $whereEquals[$sRestriction] = $this->oTableRow->sqlData[$sRestriction];
        }

        $databaseConnection->executeStatement($updateQuery, $parameters);

        $this->getBackendSession()->getCurrentEditLanguageId();
        $editLanguageIsoCode = $this->getBackendSession()->getCurrentEditLanguageIso6391();
        $migrationQueryData = new MigrationQueryData($oTableConf->sqlData['name'], $editLanguageIsoCode);
        $migrationQueryData->setFields($setFields);
        $migrationQueryData->setWhereEquals($whereEquals);
        $migrationQueryData->setWhereExpressions($whereExpressions);

        $aQuery = [new LogChangeDataModel($migrationQueryData, LogChangeDataModel::TYPE_UPDATE)];
        TCMSLogChange::WriteTransaction($aQuery);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function DataIsValid()
    {
        $bDataIsValid = parent::DataIsValid();
        if ($bDataIsValid && $this->HasContent() && '1' == $this->data) {
            $oMessageManager = TCMSMessageManager::GetInstance();
            $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
            $sFieldTitle = $this->oDefinition->GetName();
            $oMessageManager->AddMessage($sConsumerName, 'TABLEEDITOR_FIELD_UNIQUEMARKER_CHANGE_TO_YES', ['sFieldName' => $this->name, 'sFieldTitle' => $sFieldTitle]);
        }

        return $bDataIsValid;
    }
}

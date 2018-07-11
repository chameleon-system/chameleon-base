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
/**/
class TCMSFieldUniqueMarker extends TCMSFieldBoolean
{
    /**
     * {@inheritdoc}
     */
    public function PreGetSQLHook()
    {
        // we need to check if this record is set as the unique record and reset all other records
        if ('1' == $this->ConvertPostDataToSQL()) {
            $oTableConf = &$this->oTableRow->GetTableConf();

            $setFields = array();
            $whereConditions = array();
            $updateQuery = 'UPDATE `'.MySqlLegacySupport::getInstance()->real_escape_string($oTableConf->sqlData['name']).'` SET `'.MySqlLegacySupport::getInstance()->real_escape_string($this->name)."` = '0' WHERE `id` != '".MySqlLegacySupport::getInstance()->real_escape_string($this->oTableRow->id)."' AND `".MySqlLegacySupport::getInstance()->real_escape_string($this->name)."` = '1' ";
            $setFields[$this->name] = '0';
            $whereConditions[] = new Comparison('id', Comparison::NEQ, $this->oTableRow->id);
            $whereConditions[] = new Comparison($this->name, Comparison::EQ, '1');

            // check if the table has a foreign key field
            /**
             * @var TdbCmsFieldConfList $oFields
             */
            $oFields = $oTableConf->GetFieldDefinitions(array('CMSFIELD_PROPERTY_PARENT_ID'), true);
            if (1 == $oFields->Length()) {
                $oField = $oFields->Current();
                $databaseConnection = $this->getDatabaseConnection();
                $quotedNameField = $databaseConnection->quoteIdentifier($oField->sqlData['name']);
                $quotedNameValue = $databaseConnection->quote($this->oTableRow->sqlData[$oField->sqlData['name']]);
                $updateQuery .= " AND $quotedNameField = $quotedNameValue ";
                $whereConditions[] = new Comparison($oField->sqlData['name'], Comparison::EQ, $this->oTableRow->sqlData[$oField->sqlData['name']]);
            }

            // also allow restricting based on a field in the current row
            $sRestriction = $this->oDefinition->GetFieldtypeConfigKey('restriction');
            if (!empty($sRestriction) && array_key_exists($sRestriction, $this->oTableRow->sqlData)) {
                $updateQuery .= ' AND `'.MySqlLegacySupport::getInstance()->real_escape_string($oTableConf->sqlData['name']).'`.`'.MySqlLegacySupport::getInstance()->real_escape_string($sRestriction)."` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->oTableRow->sqlData[$sRestriction])."'";
            }

            MySqlLegacySupport::getInstance()->query($updateQuery);

            $editLanguage = $this->getLanguageService()->getActiveEditLanguage();
            $migrationQueryData = new MigrationQueryData($oTableConf->sqlData['name'], $editLanguage->fieldIso6391);
            $migrationQueryData->setFields($setFields);
            $migrationQueryData->setWhereExpressions($whereConditions);

            $aQuery = array(new LogChangeDataModel($migrationQueryData, LogChangeDataModel::TYPE_UPDATE));
            TCMSLogChange::WriteTransaction($aQuery);
        }

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
            $oMessageManager->AddMessage($sConsumerName, 'TABLEEDITOR_FIELD_UNIQUEMARKER_CHANGE_TO_YES', array('sFieldName' => $this->name, 'sFieldTitle' => $sFieldTitle));
        }

        return $bDataIsValid;
    }
}

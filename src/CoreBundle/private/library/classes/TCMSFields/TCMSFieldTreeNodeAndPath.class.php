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

/**
 * picks a node from a tree. stores the tree id AND the generated path in a hidden field.
/**/
class TCMSFieldTreeNodeAndPath extends TCMSFieldTreeNode
{
    /**
     * changes an existing field definition (alter table).
     *
     * @param string $sOldName
     * @param string $sNewName
     * @param array  $postData
     */
    public function ChangeFieldDefinition($sOldName, $sNewName, &$postData = null)
    {
        parent::ChangeFieldDefinition($sOldName, $sNewName, $postData);

        $query = 'ALTER TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`
                     CHANGE `'.MySqlLegacySupport::getInstance()->real_escape_string($sOldName).'_path`
                            `'.MySqlLegacySupport::getInstance()->real_escape_string($sNewName).'_path` TEXT NOT NULL';
        MySqlLegacySupport::getInstance()->query($query);
        $aQuery = array(new LogChangeDataModel($query));

        TCMSLogChange::WriteTransaction($aQuery);
    }

    /**
     * called on each field after the record is saved (NOT on insert, only on save).
     *
     * @param string $iRecordId - the id of the record
     */
    public function PostSaveHook($iRecordId)
    {
        $oNode = new TCMSTreeNode();
        /** @var $oNode TCMSTreeNode */
        if ($oNode->Load($this->data)) {
            $sPath = $oNode->GetTextPathToNode();

            $query = 'UPDATE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`
                     SET `'.MySqlLegacySupport::getInstance()->real_escape_string($this->name.'_path')."` = '".MySqlLegacySupport::getInstance()->real_escape_string($sPath)."'
                   WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->oTableRow->id)."'
                 ";
            MySqlLegacySupport::getInstance()->query($query);

            $editLanguage = $this->getLanguageService()->getActiveEditLanguage();
            $migrationQueryData = new MigrationQueryData($this->sTableName, $editLanguage->fieldIso6391);
            $migrationQueryData
                ->setFields(array(
                    $this->name.'_path' => $sPath,
                ))
                ->setWhereEquals(array(
                    'id' => $this->oTableRow->id,
                ))
            ;
            $aQuery = array(new LogChangeDataModel($migrationQueryData, LogChangeDataModel::TYPE_UPDATE));
            TCMSLogChange::WriteTransaction($aQuery);
        }
    }

    /**
     * return the new charset latin1 so that we get more memory
     * size for a record.
     *
     * @return string
     */
    public function _GetSQLCharset()
    {
        return ' CHARACTER SET latin1 COLLATE latin1_general_ci';
    }

    /**
     * drop a field definition (alter table).
     */
    public function DeleteFieldDefinition()
    {
        parent::DeleteFieldDefinition();

        $this->RemoveFieldIndex();
        $query = 'ALTER TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`
                       DROP `'.MySqlLegacySupport::getInstance()->real_escape_string($this->name.'_path').'` ';

        MySqlLegacySupport::getInstance()->query($query);
        $aQuery = array(new LogChangeDataModel($query));
        TCMSLogChange::WriteTransaction($aQuery);
    }

    /**
     * called on the OLD field if the field type is changed (before deleting related tables or dropping the index).
     */
    public function ChangeFieldTypePreHook()
    {
        $query = 'ALTER TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`
                       DROP `'.MySqlLegacySupport::getInstance()->real_escape_string($this->name.'_path').'` ';

        MySqlLegacySupport::getInstance()->query($query);
        $aQuery = array(new LogChangeDataModel($query));
        TCMSLogChange::WriteTransaction($aQuery);
    }

    /**
     * called on the NEW field if the field type is changed (BEFORE anything else is done).
     */
    public function ChangeFieldTypePostHook()
    {
        $query = 'ALTER TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`
                        ADD `'.MySqlLegacySupport::getInstance()->real_escape_string($this->name).'_path` TEXT NOT NULL';
        $aQuery = array(new LogChangeDataModel($query));

        TCMSLogChange::WriteTransaction($aQuery);
        MySqlLegacySupport::getInstance()->query($query);
    }
}

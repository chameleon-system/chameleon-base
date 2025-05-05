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

/**
 * index management for tables.
 * /**/
class TCMSTableEditor_CmsTblConfIndex extends TCMSTableEditor
{
    /**
     * create an index for oTable.
     *
     * @param TdbCmsTblConfIndex $oTable
     */
    protected function CreateIndex($oTable)
    {
        $oTableConf = $oTable->GetFieldCmsTblConf();
        $query = 'ALTER TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($oTableConf->sqlData['name'])."`
                        ADD {$oTable->fieldType}  `".MySqlLegacySupport::getInstance()->real_escape_string($oTable->fieldName)."` ( {$oTable->fieldDefinition} )";
        MySqlLegacySupport::getInstance()->query($query);
        TCMSLogChange::WriteTransaction([new LogChangeDataModel($query)]);
    }

    /*
    * Drop the index for record oTable
    * @param TdbCmsTblConfIndex $oTable
    */
    protected function DeleteIndex($oTable)
    {
        // check if the index exists
        $oTableConf = $oTable->GetFieldCmsTblConf();
        $query = 'ALTER TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($oTableConf->sqlData['name']).'` DROP INDEX  `'.MySqlLegacySupport::getInstance()->real_escape_string($oTable->fieldName).'`';
        MySqlLegacySupport::getInstance()->query($query);
        TCMSLogChange::WriteTransaction([new LogChangeDataModel($query)]);
    }

    /**
     * {@inheritdoc}
     */
    public function Delete($sId = null)
    {
        $this->DeleteIndex($this->oTable);
        parent::Delete($sId);
    }

    /**
     * {@inheritdoc}
     */
    public function Save($postData, $bDataIsInSQLForm = false)
    {
        $returnVal = false;
        if ($this->DataIsValid($postData, null)) {
            $returnVal = parent::Save($postData);

            if (false !== $returnVal) {
                if ($this->oTablePreChangeData && '' !== $this->oTablePreChangeData->fieldName) {
                    $this->DeleteIndex($this->oTablePreChangeData);
                }
                $this->CreateIndex($this->oTable);
            }
        }

        return $returnVal;
    }
}

<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Util\MltFieldUtil;
use Doctrine\DBAL\Connection;

/**
 * uses the TFullGroupTable to manage the list.
 * /**/
class TCMSListManagerMLTList extends TCMSListManagerFullGroupTable
{
    /* ------------------------------------------------------------------------
    * generates the tableobj. assumes that all parameters are in post
   /* ----------------------------------------------------------------------*/
    public function CreateTableObj()
    {
        parent::CreateTableObj();
        $this->tableObj->showRecordCount = 20;
    }

    public function _AddFunctionColumn()
    {
    }

    /**
     * returns the name of the javascript function to be called when the user clicks on a
     * record within the table.
     *
     * @return string
     */
    public function _GetRecordClickJavaScriptFunctionName()
    {
        return 'addMLTConnectionPassThrough';
    }

    /**
     * any custom restrictions can be added to the query by overwriting this function.
     */
    public function GetCustomRestriction()
    {
        $query = '';

        // because one table may be connected more than once with the source table, we need
        // to use the parameter "name" instead of the tableconf
        $oGlobal = TGlobal::instance();
        $sFieldMltName = $this->GetFieldMltName();
        $mltTable = substr($this->sRestrictionField, 0, -4).'_'.$sFieldMltName.'_mlt';
        // echo $mltTable;
        // echo "<pre>";var_dump($this); echo "</pre>";

        $MLTquery = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($mltTable)."` WHERE `source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sRestriction)."'";
        $MLTResult = MySqlLegacySupport::getInstance()->query($MLTquery);
        $aIDList = [];
        while ($row = MySqlLegacySupport::getInstance()->fetch_assoc($MLTResult)) {
            $aIDList[] = $row['target_id'];
        }

        if (count($aIDList) > 0) {
            $databaseConnection = $this->getDatabaseConnection();
            $idListString = implode(',', array_map([$databaseConnection, 'quote'], $aIDList));
            $quotedTableName = $databaseConnection->quoteIdentifier($this->oTableConf->sqlData['name']);
            $query .= " $quotedTableName.`id` NOT IN ($idListString)";
        }

        return $query;
    }

    /**
     * Returns the name of the MLt field without source table name.
     * Postfix _mlt was filtered.
     *
     * @return string
     */
    protected function GetFieldMltName()
    {
        $sFieldMltName = $this->oTableConf->sqlData['name'];
        if (array_key_exists('name', $this->tableObj->_postData)) {
            $sPostFieldMltName = $this->tableObj->_postData['name'];
            $mltFieldUtil = $this->getMltFieldUtil();
            $sPostFieldMltName = $mltFieldUtil->cutMltExtension($sPostFieldMltName);
            $cleanMltFieldName = $mltFieldUtil->cutMultiMltFieldNumber($sPostFieldMltName);
            if ($cleanMltFieldName != $sFieldMltName) {
                $sFieldMltName = $sPostFieldMltName.'_'.$sFieldMltName;
            } else {
                $sFieldMltName = $sPostFieldMltName;
            }
        }

        return $sFieldMltName;
    }

    protected function AddRowPrefixFields()
    {
    }

    /**
     * add table-specific buttons to the editor (add them directly to $this->oMenuItems).
     */
    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();
        $this->oMenuItems->RemoveItem('sItemKey', 'deleteall');
        $this->oMenuItems->RemoveItem('sItemKey', 'edittableconf');
    }

    /**
     * @return Connection
     */
    private function getDatabaseConnection()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');
    }

    /**
     * @return MltFieldUtil
     */
    protected function getMltFieldUtil()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.mlt_field');
    }
}

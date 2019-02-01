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
/**/
class TCMSListManagerMLT extends TCMSListManagerFullGroupTable
{
    /* ------------------------------------------------------------------------
     * generates the tableobj. assumes that all parameters are in post
    /* ----------------------------------------------------------------------*/
    public function CreateTableObj()
    {
        parent::CreateTableObj();
        $this->tableObj->showRecordCount = 10;
    }

    public function _AddFunctionColumn()
    {
        ++$this->columnCount;
        $sTranslatedField = TGlobal::Translate('chameleon_system_core.list.column_name_actions');
        $this->tableObj->AddHeaderField(array('id' => $sTranslatedField.'&nbsp;&nbsp;'), 'right', null, 1, false, 100);
        $this->tableObj->AddColumn('id', 'left', array($this, 'CallBackMLTFunctionBlock'), null, 1);
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

    /**
     * Returns the mlt table name.
     *
     * @return string
     */
    protected function GetMLTTableName()
    {
        $sFieldMltName = $this->GetFieldMltName();
        $sMLTTableName = substr($this->sRestrictionField, 0, -4).'_'.$sFieldMltName.'_mlt';

        return $sMLTTableName;
    }

    /**
     * any custom restrictions can be added to the query by overwriting this function.
     *
     * @param string $query
     */
    public function GetCustomRestriction()
    {
        $query = '';
        if (!is_null($this->sRestrictionField) && !is_null($this->sRestriction)) {
            if ('_mlt' == substr($this->sRestrictionField, -4) && array_key_exists('table', $this->tableObj->_postData)) {
                $mltTable = $this->GetMLTTableName();
                if ($this->IsCustomSort()) {
                    $query .= " `{$mltTable}`.`source_id` IN ('".$this->sRestriction."')";
                } else {
                    $MLTquery = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($mltTable)."` WHERE `source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sRestriction)."'";
                    $MLTResult = MySqlLegacySupport::getInstance()->query($MLTquery);
                    $aIDList = array();
                    while ($row = MySqlLegacySupport::getInstance()->fetch_assoc($MLTResult)) {
                        $aIDList[] = $row['target_id'];
                    }
                    if (count($aIDList) > 0) {
                        $databaseConnection = $this->getDatabaseConnection();
                        $idListString = implode(',', array_map(array($databaseConnection, 'quote'), $aIDList));
                        $quotedTableName = $databaseConnection->quoteIdentifier($this->oTableConf->sqlData['name']);
                        $query .= " $quotedTableName.`id` IN ($idListString)";
                    } else {
                        $query .= '1=0';
                    }
                }
            } else {
                $query = parent::GetCustomRestriction();
            }
        }

        return $query;
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
     * displays the function icons (delete, copy, etc) for MLT table lists
     * returns HTML.
     *
     * @param string $id
     * @param array  $row
     *
     * @return string
     */
    public function CallBackMLTFunctionBlock($id, $row)
    {
        $returnValue = '<i class="fas fa-unlink" onclick="deleteConnection(\''.$row['id'].'\');" onMouseOver="$(\'#functionTitle_\'+'.$row['cmsident'].').html(\''.TGlobal::Translate('chameleon_system_core.action.remove_connection').'\');" onMouseOut="$(\'#functionTitle_\'+'.$row['cmsident'].').html(\'\');" /></i>';
        $returnValue .= '<div id="functionTitle_'.$row['cmsident'].'" class="functionTitle"></div>';
        $returnValue .= '</div>';

        return $returnValue;
    }

    /**
     * Join mlt table to the query.
     *
     * @return string
     */
    protected function GetFilterQueryCustomJoins()
    {
        $sJoin = parent::GetFilterQueryCustomJoins();
        if ($this->IsCustomSort()) {
            $databaseConnection = $this->getDatabaseConnection();
            $quotedMltTableName = $databaseConnection->quoteIdentifier($this->GetMLTTableName());
            $quotedTableName = $databaseConnection->quoteIdentifier($this->oTableConf->sqlData['name']);

            $sJoin .= " INNER JOIN $quotedMltTableName ON $quotedMltTableName.`target_id` = $quotedTableName.`id` ";
        }

        return $sJoin;
    }

    /**
     * adds the orderby info to the table.
     */
    public function AddSortInformation()
    {
        if ($this->IsCustomSort()) {
            $sMLTTableName = $this->GetMLTTableName();

            $databaseConnection = $this->getDatabaseConnection();
            $quotedMltTableName = $databaseConnection->quoteIdentifier($sMLTTableName);

            $sMltField = "$quotedMltTableName.`entry_sort`";
            $aTmpField = $this->TransformFieldForTranslations(array('name' => $sMltField, 'db_alias' => ''));
            $this->tableObj->orderList[$aTmpField['name']] = 'ASC';
        } else {
            parent::AddSortInformation();
        }
    }

    public function IsCustomSort()
    {
        static $bIsCustomSort = null;
        if (is_null($bIsCustomSort)) {
            $bIsCustomSort = false;
            if (array_key_exists('table', $this->tableObj->_postData)) {
                $oTableConf = TdbCmsTblConf::GetNewInstance();
                $oTableConf->LoadFromField('name', $this->tableObj->_postData['table']);
                $oMltField = $oTableConf->GetFieldDefinition($this->tableObj->_postData['field']);
                $bShowCustomsort = $oMltField->GetFieldtypeConfigKey('bAllowCustomSortOrder');
                if (true == $bShowCustomsort) {
                    $bIsCustomSort = true;
                }
            }
        }

        return $bIsCustomSort;
    }

    /**
     * @param string $field
     * @param array  $row
     * @param string $name
     *
     * @return string
     *
     * @deprecated since 6.2.0 - workflow is not supported anymore
     */
    public function CallBackWorkflowConnectionActionType($field, $row, $name)
    {
        return '';
    }

    /**
     * @return MltFieldUtil
     */
    protected function getMltFieldUtil()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.mlt_field');
    }

    /**
     * @return Connection
     */
    private function getDatabaseConnection()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');
    }
}

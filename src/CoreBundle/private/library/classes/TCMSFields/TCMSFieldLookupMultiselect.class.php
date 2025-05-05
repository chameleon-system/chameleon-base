<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\MltFieldUtil;
use ChameleonSystem\DatabaseMigration\DataModel\LogChangeDataModel;

/**
 * multi linked table field (MLT).
 */
class TCMSFieldLookupMultiselect extends TCMSMLTField
{
    /**
     * view path for frontend.
     */
    protected $sViewPath = 'TCMSFields/views/TCMSFieldLookupMultiselect';

    public function GetHTML()
    {
        /** @var TTableEditorListFieldState $stateContainer */
        $stateContainer = ChameleonSystem\CoreBundle\ServiceLocator::get('cmsPkgCore.tableEditorListFieldState');

        $inputFilterUtil = $this->getInputFilterUtil();

        $aStateURL = [
            'pagedef' => $inputFilterUtil->getFilteredInput('pagedef'),
            'tableid' => $inputFilterUtil->getFilteredInput('tableid'),
            'id' => $inputFilterUtil->getFilteredInput('id'),
            'fieldname' => $this->name,
            'module_fnc' => ['contentmodule' => 'ExecuteAjaxCall'],
            '_fnc' => 'changeListFieldState',
        ];
        $sStateURL = '?'.TTools::GetArrayAsURLForJavascript($aStateURL);

        $sEscapedName = TGlobal::OutHTML($this->name);

        $html = '<input type="hidden" name="'.$sEscapedName.'[x]" value="-" id="'.$sEscapedName.'[]" />';
        $html .= '<div class="card">
        <div class="card-header p-1">
            <div class="card-action" 
            data-fieldstate="'.TGlobal::OutHTML($stateContainer->getState($this->sTableName, $this->name)).'" 
            id="mltListControllButton'.$sEscapedName.'" 
            onClick="setTableEditorListFieldState(this, \''.$sStateURL.'\'); CHAMELEON.CORE.MTTableEditor.switchMultiSelectListState(\''.$sEscapedName.'_iframe\',\''.$this->GetSelectListURL().'\');">
            <i class="fas fa-eye"></i> '.TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_lookup_multi_select.open_or_close_list')).'
            </div>
        </div>
        <div class="card-body p-0">
            <div id="'.$sEscapedName.'_iframe_block\">
                <iframe id="'.$sEscapedName.'_iframe" width="100%" height="470" frameborder="0" class="d-none"></iframe>
            </div>
        </div>
        </div>';

        if ('true' === $this->oDefinition->GetFieldtypeConfigKey('bOpenOnLoad') || $stateContainer->getState($this->sTableName, $this->name) === $stateContainer::STATE_OPEN) {
            $html .= "
            <script type=\"text/javascript\">
            $(document).ready(function() {
              CHAMELEON.CORE.MTTableEditor.switchMultiSelectListState('".$sEscapedName."_iframe','".str_replace('&amp;', '&', $this->GetSelectListURL())."');
            });
            </script>
          ";
        }

        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function GetReadOnly()
    {
        $html = $this->_GetHiddenField();
        $html .= '
      <div class="card">
          <div class="card-body p-1">';

        $oMLTRecords = $this->FetchConnectedMLTRecords();
        while ($oMLTRecord = $oMLTRecords->Next()) {
            $html .= '<div class="checkboxDIV">'.TGlobal::OutHTML($oMLTRecord->GetDisplayValue()).'</div>';
        }

        $html .= '<div class="cleardiv">&nbsp;</div>
          </div>
      </div>';

        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function _GetHiddenField()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    protected function GetForeignTableName()
    {
        return $this->GetConnectedTableName();
    }

    /**
     * @return TCMSRecordList
     */
    public function FetchConnectedMLTRecords()
    {
        $foreignTableName = $this->GetForeignTableName();

        $oTableConf = new TCMSTableConf();
        $oTableConf->LoadFromField('name', $foreignTableName);
        $sNameField = $oTableConf->GetNameColumn();

        $mltTableName = $this->GetMLTTableName();

        $query = 'SELECT *
    FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($foreignTableName)."`
    INNER JOIN `{$mltTableName}` AS MLT ON `".MySqlLegacySupport::getInstance()->real_escape_string($foreignTableName)."`.`id` = MLT.`target_id`
    WHERE MLT.`source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->oTableRow->sqlData['id'])."'";
        $bShowCustomsort = $this->oDefinition->GetFieldtypeConfigKey('bAllowCustomSortOrder');
        if (true == $bShowCustomsort) {
            $query .= ' ORDER BY MLT.`entry_sort` ASC , `'.MySqlLegacySupport::getInstance()->real_escape_string($sNameField).'`';
        } else {
            $query .= ' ORDER BY `'.MySqlLegacySupport::getInstance()->real_escape_string($sNameField).'` ';
        }
        $oMLTRecords = call_user_func([TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $foreignTableName).'List', 'GetList'], $query);

        return $oMLTRecords;
    }

    public function FetchMLTRecords()
    {
        $foreignTableName = $this->GetForeignTableName();
        $sFilterQuery = $this->GetMLTFilterQuery();
        /** @var $oMLTRecords TCMSRecordList */
        $oMLTRecords = call_user_func([TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $foreignTableName).'List', 'GetList'], $sFilterQuery);

        return $oMLTRecords;
    }

    /**
     * {@inheritdoc}
     */
    protected function GetMLTFilterQuery()
    {
        $foreignTableName = $this->GetForeignTableName();
        $foreignTableConf = new TCMSTableConf();
        $foreignTableConf->LoadFromField('name', $foreignTableName);
        $foreignFieldName = $foreignTableConf->GetNameColumn();

        $foreignTableNameQuoted = $this->getDatabaseConnection()->quoteIdentifier($foreignTableName);
        $foreignFieldNameQuoted = $this->getDatabaseConnection()->quoteIdentifier($foreignFieldName);
        $mltRestrictions = $this->GetMLTRecordRestrictions();

        return sprintf(
            'SELECT * FROM %s AS parenttable WHERE 1=1 %s ORDER BY `parenttable`.%s',
            $foreignTableNameQuoted,
            $mltRestrictions,
            $foreignFieldNameQuoted
        );
    }

    /**
     * Returns the url to open the list in cms.
     *
     * @return string
     */
    public function GetSelectListURL()
    {
        $sForeignTableName = $this->GetConnectedTableName(false);
        /** @var $oForeignTableConf TCMSTableConf */
        $oForeignTableConfig = new TCMSTableConf();
        $oForeignTableConfig->LoadFromField('name', $sForeignTableName);
        $url = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURL(['_isiniframe' => 'true', 'pagedef' => 'mltfield', 'name' => $this->name, 'sRestriction' => $this->recordId, 'sRestrictionField' => $this->sTableName.'_mlt', 'id' => $oForeignTableConfig->id, 'table' => $this->sTableName, 'recordid' => $this->recordId, 'field' => $this->name,
            ]);

        return $url;
    }

    /**
     * returns the mlt table name e.g. data_firstTable_data_secondTable_mlt
     * Get the mlt table name from active field object or from given sql field data.
     *
     * @param array $aFieldData sql field data
     *
     * @return string
     */
    public function GetMLTTableName($aFieldData = [])
    {
        if (count($aFieldData) > 0) {
            $sConnectedTableName = $this->GetConnectedTableNameFromFieldConfig($aFieldData);
            $sName = $aFieldData['name'];
        } else {
            $sConnectedTableName = $this->getConnectedTableNameFromDefinition();
            $sName = $this->name;
        }
        if ($sConnectedTableName) {
            $mltTableName = $this->sTableName.'_'.$sName.'_'.$sConnectedTableName;
        } else {
            $mltTableName = $this->sTableName.'_'.$sName;
        }
        if ('_mlt' != substr($mltTableName, -4)) {
            $mltTableName .= '_mlt';
        }

        return $mltTableName;
    }

    protected function getConnectedTableNameFromDefinition(): ?string
    {
        return $this->oDefinition->GetFieldtypeConfigKey('connectedTableName');
    }

    /**
     * {@inheritdoc}
     */
    public function GetSQL()
    {
        return false; // prevent saving of sql
    }

    /**
     * {@inheritdoc}
     * Overwritten to delete the related download MLT table.
     */
    public function DeleteFieldDefinition()
    {
        $this->DeleteRelatedTables();
        parent::DeleteFieldDefinition();
    }

    /**
     * {@inheritdoc}
     */
    public function CreateRelatedTables($returnDDL = false)
    {
        $sReturnVal = '';
        $tableName = $this->GetMLTTableName();
        if (!TGlobal::TableExists($tableName)) {
            $query = 'CREATE TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($tableName)."` (
                  `source_id` CHAR( 36 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
                  `target_id` CHAR( 36 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
                  `entry_sort` int(11) NOT NULL default '0',
                  PRIMARY KEY ( `source_id` , `target_id` ),
                  INDEX (target_id),
                  INDEX (entry_sort)
                ) ENGINE = InnoDB";

            if (!$returnDDL) {
                MySqlLegacySupport::getInstance()->query($query);
                $aQuery = [new LogChangeDataModel($query)];

                TCMSLogChange::WriteTransaction($aQuery);
            } else {
                $sReturnVal .= $query.";\n";
            }
        }

        return $sReturnVal;
    }

    /**
     * {@inheritdoc}
     * Allow create new related table if target table name changed or field typ changed form none mlt to mlt.
     */
    public function AllowCreateRelatedTablesAfterFieldSave($aOldFieldData, $aOldFieldTypeRow, $aNewFieldTypeRow)
    {
        $bAllowCreateRelatedTables = false;
        $sConnectedTableNameFromOldFieldConfig = $this->GetConnectedTableNameFromSQLData($aOldFieldData);
        $sConnectedTableNameFromNewFieldConfig = $this->GetConnectedTableName();
        if ($sConnectedTableNameFromNewFieldConfig != $sConnectedTableNameFromOldFieldConfig) {
            $bAllowCreateRelatedTables = true;
        } elseif ('mlt' != $aOldFieldTypeRow['base_type'] && 'mlt' == $aNewFieldTypeRow['base_type']) {
            $bAllowCreateRelatedTables = true;
        }

        return $bAllowCreateRelatedTables;
    }

    /**
     * {@inheritdoc}
     * Allow renaming related table if field type is mlt and not changed and target table not changed but field name changed.
     */
    public function AllowRenameRelatedTablesBeforeFieldSave($aNewFieldData, $aOldFieldTypeRow, $aNewFieldTypeRow)
    {
        $bAllowRenameRelatedTables = false;
        if ('mlt' == $aOldFieldTypeRow['base_type'] && 'mlt' == $aNewFieldTypeRow['base_type']) {
            $sFieldConfigConnectedTableForNewField = $this->GetConnectedTableNameFromFieldConfig($aNewFieldData);
            $sFieldConfigConnectedTableForOldField = $this->getConnectedTableNameFromDefinition();
            if (!empty($sFieldConfigConnectedTableForNewField) && !empty($sFieldConfigConnectedTableForOldField)) {
                if ($sFieldConfigConnectedTableForNewField == $sFieldConfigConnectedTableForOldField && $this->name != $aNewFieldData['name']) {
                    $bAllowRenameRelatedTables = true;
                }
            } elseif (empty($sFieldConfigConnectedTableForNewField) && !empty($sFieldConfigConnectedTableForOldField)) {
                if ($sFieldConfigConnectedTableForOldField == $this->GetClearedTableName($aNewFieldData['name'])) {
                    $bAllowRenameRelatedTables = true;
                }
            } elseif (!empty($sFieldConfigConnectedTableForNewField) && empty($sFieldConfigConnectedTableForOldField)) {
                if ($sFieldConfigConnectedTableForNewField == $this->GetClearedTableName($this->name)) {
                    $bAllowRenameRelatedTables = true;
                }
            } elseif ($this->name !== $aNewFieldData['name']) {
                $bAllowRenameRelatedTables = true;
            }
        }

        return $bAllowRenameRelatedTables;
    }

    /**
     * {@inheritdoc}
     */
    public function RenameRelatedTables($aNewFieldData, $returnDDL = false)
    {
        $sReturnVal = '';
        $sTableName = $this->GetMLTTableName();
        $sNewTableName = $this->GetMLTTableName($aNewFieldData);
        if (TGlobal::TableExists($sTableName)) {
            $query = 'RENAME TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($sTableName).'` TO `'.MySqlLegacySupport::getInstance()->real_escape_string($sNewTableName).'` ';
            if (!$returnDDL) {
                MySqlLegacySupport::getInstance()->query($query);
                $aQuery = [new LogChangeDataModel($query)];

                TCMSLogChange::WriteTransaction($aQuery);
            } else {
                $sReturnVal .= $query.";\n";
            }
        }

        return $sReturnVal;
    }

    /**
     * {@inheritdoc}
     */
    public function GetConnectedTableName($bExistingCount = true)
    {
        $sTableName = $this->getConnectedTableNameFromDefinition();

        return $this->GetClearedTableName($sTableName);
    }

    /**
     * Returns the name of the table this field is connected with from given sql field data.
     * Get connected table name from field config (connectedTableName) or from field name.
     *
     * @param array $aNewFieldData
     *
     * @return string|null
     */
    protected function GetConnectedTableNameFromSQLData($aNewFieldData)
    {
        $sTableName = $this->GetConnectedTableNameFromFieldConfig($aNewFieldData);
        $sTableName = $this->GetClearedTableName($sTableName, $aNewFieldData);

        return $sTableName;
    }

    /**
     * {@inheritdoc}
     */
    protected function GetClearedTableName($sTableName, $aFieldData = [])
    {
        $mltFieldUtil = self::getMltFieldUtil();
        if (is_null($sTableName) || empty($sTableName)) {
            $sName = $this->name;
            if (count($aFieldData) > 0) {
                $sName = $aFieldData['name'];
            }
            $sTableName = $mltFieldUtil->cutMltExtension($sName);
        } else {
            $sTableName = $mltFieldUtil->cutMltExtension($sTableName);
        }
        $sTableName = $mltFieldUtil->cutMultiMltFieldNumber($sTableName);

        return $sTableName;
    }

    /**
     * {@inheritdoc}
     */
    public function DeleteRelatedTables()
    {
        $tableName = $this->GetMLTTableName();
        if (TGlobal::TableExists($tableName)) {
            $query = 'DROP TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($tableName).'`';
            MySqlLegacySupport::getInstance()->query($query);
            $aQuery = [new LogChangeDataModel($query)];
            TCMSLogChange::WriteTransaction($aQuery);
        }
    }

    /**
     * {@inheritdoc}
     *
     * Allow delete related table if field type changed from mlt to none mlt or connected table changed.
     */
    public function AllowDeleteRelatedTablesBeforeFieldSave($aNewFieldData, $aOldFieldTypeRow, $aNewFieldTypeRow)
    {
        $bAllowDeleteRelatedTables = false;
        $sConnectedTableNameFromNewFieldConfig = $this->GetConnectedTableNameFromSQLData($aNewFieldData);
        $sConnectedTableNameFromOldFieldConfig = $this->GetConnectedTableName();
        if ($sConnectedTableNameFromOldFieldConfig != $sConnectedTableNameFromNewFieldConfig) {
            $bAllowDeleteRelatedTables = true;
        } elseif ('mlt' == $aOldFieldTypeRow['base_type'] && 'mlt' != $aNewFieldTypeRow['base_type']) {
            $bAllowDeleteRelatedTables = true;
        }

        return $bAllowDeleteRelatedTables;
    }

    /**
     * Returns config parameter for given field data and config parameter.
     *
     * @param array $aFieldSQLData sql data array of field config
     * @param string $sParameterKey
     *
     * @return string
     */
    protected function GetConnectedTableNameFromFieldConfig($aFieldSQLData, $sParameterKey = 'connectedTableName')
    {
        $oConfig = new TPkgCmsStringUtilities_ReadConfig($aFieldSQLData['fieldtype_config']);

        return $oConfig->getConfigValue($sParameterKey);
    }

    public function GetHTMLExport()
    {
        $html = '';
        $oMLTRecords = $this->FetchConnectedMLTRecords();

        while ($oMLTRecord = $oMLTRecords->Next()) {
            $html .= $oMLTRecord->GetDisplayValue().', ';
        }

        if (', ' === substr($html, -2, 2)) {
            $html = substr($html, 0, -2);
        }

        return $html;
    }

    public function RenderFieldPostLoadString()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    protected function GetFieldMethodName($sMethodPostString = '')
    {
        $sName = parent::GetFieldMethodName($sMethodPostString);
        // remove the 'Id' from the end
        if ('_mlt' == mb_substr($this->name, -4) && 'Mlt' == mb_substr($sName, -3)) {
            $sName = mb_substr($sName, 0, -3);
        }

        return $sName;
    }

    public function RenderFieldMethodsString()
    {
        $aMethodData = $this->GetFieldMethodBaseDataArray();

        $sTargetTableName = $this->GetForeignTableName();

        $query = "SELECT * FROM cms_tbl_conf where `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->GetConnectedTableName())."'";
        $aTargetTable = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query));

        $aMethodData['sMethodName'] = $this->GetFieldMethodName().'List';
        $aMethodData['sMethodDescription'] = '';
        $sItemClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $sTargetTableName);
        $aMethodData['sReturnType'] = $sItemClassName.'List';
        $aMethodData['sTargetTableName'] = $sTargetTableName;
        $aMethodData['sClassName'] = $sItemClassName;
        $aMethodData['sClassSubType'] = 'CMSDataObjects';
        $aMethodData['sClassType'] = $aTargetTable['dbobject_type'];

        $aMethodData['aParameters']['sOrderBy'] = self::GetMethodParameterArray('string', "''", 'an sql order by string (without the order by)');

        $oViewParser = new TViewParser();
        $oViewParser->bShowTemplatePathAsHTMLHint = false;
        $oViewParser->AddVarArray($aMethodData);

        $sMethodCode = $oViewParser->RenderObjectView('getobject', 'TCMSFields/TCMSFieldLookupMultiselect');
        $oViewParser->AddVar('sMethodCode', $sMethodCode);

        $sMethodOutput = $oViewParser->RenderObjectView('method', 'TCMSFields/TCMSFieldLookupMultiselect');

        // IdList method
        $aMethodData['sCalledMethod'] = $aMethodData['sMethodName'].'($sOrderBy)';
        $aMethodData['sMethodName'] = $this->GetFieldMethodName().'IdList';
        $aMethodData['sMethodDescription'] = 'ID List';
        $aMethodData['sReturnType'] = 'array|string';
        $aMethodData['aParameters']['bReturnAsCommaSeparatedString'] = self::GetMethodParameterArray('bool', 'false', "set this to true if you need the id list for a query e.g. WHERE `related_record_id` IN ('1','2','abcd-234')");

        $oViewParser->AddVarArray($aMethodData);
        $sMethodCode = $oViewParser->RenderObjectView('getobjectIDs', 'TCMSFields/TCMSFieldLookupMultiselect');
        $oViewParser->AddVar('sMethodCode', $sMethodCode);
        $sMethodOutput .= "\n".$oViewParser->RenderObjectView('method', 'TCMSFields/TCMSFieldLookupMultiselect');

        return $sMethodOutput;
    }

    /**
     * {@inheritdoc}
     */
    public function HasContent()
    {
        $bHasContent = false;
        if (is_array($this->data)) {
            if (array_key_exists('x', $this->data)) {
                $oConnectedMLTRecords = $this->FetchConnectedMLTRecords();
                if ($oConnectedMLTRecords->Length() > 0) {
                    $bHasContent = true;
                }
            } else {
                $bHasContent = true;
            }
        }

        return $bHasContent;
    }

    /**
     * fetch the name of the target table from the mlt table - this should be rebuilt at some point
     * to take the name from the field definition instead.
     *
     * @param string $sSourceTable
     * @param string $sMltTable
     *
     * @return string
     */
    public static function GetTargetTableNameFromMLTTable($sSourceTable, $sMltTable)
    {
        $sTargetTable = substr($sMltTable, strlen($sSourceTable) + 1, -4);

        return self::getMltFieldUtil()->cutMultiMltFieldNumber($sTargetTable);
    }

    /**
     * {@inheritdoc}
     */
    public function PkgCmsFormPostSaveHook($sId, $oForm)
    {
        if (is_array($this->data)) {
            $oConnectedRecords = $this->FetchConnectedMLTRecords();
            $aConnectedRecords = $oConnectedRecords->GetIdList();
            $aNewConnections = array_diff($this->data, $aConnectedRecords);
            $aDeletedConnections = array_diff($aConnectedRecords, $this->data);

            $oTableEditor = TTools::GetTableEditorManager($this->sTableName, $sId);
            if ($oTableEditor) {
                foreach ($aNewConnections as $sConnectedId) {
                    $oTableEditor->AddMLTConnection($this->name, $sConnectedId);
                }
                foreach ($aDeletedConnections as $sDeletedId) {
                    $oTableEditor->RemoveMLTConnection($this->name, $sDeletedId);
                }
            }
        }
    }

    /**
     * Get an array of either posted data or data from db if nothing has been posted.
     *
     * @return TCMSRecordList
     */
    protected function GetRecordsConnectedFrontend()
    {
        if (is_array($this->data) && count($this->data) > 0) {
            // we assume data was already posted
            $foreignTableName = str_replace('_mlt', '', $this->name);
            $oMLTRecords = new TCMSRecordList();
            $oMLTRecords->sTableName = $foreignTableName;
            $databaseConnection = $this->getDatabaseConnection();
            $quotedForeignTableName = $databaseConnection->quoteIdentifier($foreignTableName);
            $dataString = implode(',', array_map([$databaseConnection, 'quote'], $this->data));
            $query = "SELECT * FROM $quotedForeignTableName WHERE `id` IN ($dataString)";
            $oMLTRecords->Load($query);
        } else {
            $oMLTRecords = $this->FetchConnectedMLTRecords();
        }

        return $oMLTRecords;
    }

    /**
     * {@inheritdoc}
     */
    protected function GetAdditionalViewData()
    {
        $aAdditionalViewData = parent::GetAdditionalViewData();
        $aAdditionalViewData['oMLTRecords'] = $this->FetchMLTRecords();
        $aAdditionalViewData['oConnectedMLTRecords'] = $this->GetRecordsConnectedFrontend();

        return $aAdditionalViewData;
    }

    /**
     * @return InputFilterUtilInterface
     */
    private function getInputFilterUtil()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.input_filter');
    }

    /**
     * @return MltFieldUtil
     */
    private static function getMltFieldUtil()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.mlt_field');
    }
}

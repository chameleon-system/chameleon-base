<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;

/**
 * ReloadOnChange=true.
 *
 * it is possible to add a custom restriction using the "feldtyp konfiguration" from the field definition:
 * restriction=expression
 * - expression may contain references to the current record in the form [{fieldname}]
 * : example: restriction=some_field_in_the_lookup_id=[{some_field_in_the_owning_record}]
 * the restriction will be added "as is" to the sql query
 * you may also connect to a table with a different name than the field. just add:
 * connectedTableName=tablename
 * to the field type configuration.
 */
class TCMSFieldLookup extends TCMSField
{
    /**
     * set this to true if you want to allow an empty selection in the select box.
     *
     * @var bool
     */
    public $allowEmptySelection = false;

    /**
     * view path for frontend.
     */
    protected $sViewPath = 'TCMSFields/views/TCMSFieldLookup';

    /**
     * all selectbox options based on user rights (portal).
     *
     * @var array
     */
    protected $options = array();

    public function GetHTML()
    {
        $this->GetOptions();
        $connectedRecord = $this->getConnectedRecordObject();

        if (!empty($this->data) && (!isset($this->options[$this->data]) && false !== $connectedRecord)) {
            return $this->GetReadOnly();
        }

        $viewRenderer = $this->getViewRenderer();
        $viewRenderer = $this->addFieldRenderVariables($viewRenderer);

        // current ID is an orphan, show message
        if (!empty($this->data) && false === $connectedRecord) {
            $viewRenderer->AddSourceObject('showErrorMessage', true);
        }

        return $viewRenderer->Render('TCMSFieldLookup/fieldLookup.html.twig', null, false);
    }

    private function addFieldRenderVariables(ViewRenderer $viewRenderer): ViewRenderer
    {
        $sClass = '';
        $sOnChangeAttr = '';
        if ($this->GetReloadOnChangeParam()) {
            $sOnChangeAttr = "OnChange=\"CHAMELEON.CORE.MTTableEditor.bCmsContentChanged=false;CHAMELEON.CORE.showProcessingModal();document.cmseditform.elements['module_fnc[contentmodule]'].value='Save';document.cmseditform.submit();\"";
            $sClass .= 'cmsdisablechangemessage';
        }

        $viewRenderer->AddSourceObject('fieldName', $this->name);
        $viewRenderer->AddSourceObject('fieldValue', $this->_GetHTMLValue());
        $viewRenderer->AddSourceObject('language', TCMSUser::GetActiveUser()->GetCurrentEditLanguage());
        $viewRenderer->AddSourceObject('sClass', $sClass);
        $viewRenderer->AddSourceObject('onchangeAttr', $sOnChangeAttr);
        $viewRenderer->AddSourceObject('options', $this->options);
        $viewRenderer->AddSourceObject('allowEmptySelection', $this->allowEmptySelection);

        $foreignTableName = $this->GetConnectedTableName();
        $viewRenderer->AddSourceObject('foreignTableName', $foreignTableName);
        $oGlobal = TGlobal::instance();
        if ($oGlobal->oUser->oAccessManager->HasEditPermission($foreignTableName)) {
            $viewRenderer->AddSourceObject('buttonLink', $this->GoToRecordJS());
        }
        $viewRenderer->AddSourceObject('connectedRecordId', $this->data);

        return $viewRenderer;
    }

    /**
     * comboBox is enabled on 30 elements or more
     * you may disable the combobox using "disableComboBox=true" in field config or by extending this method.
     *
     * @deprecated sind 6.3.0
     *
     * @return bool
     */
    protected function enableComboBox()
    {
        $bReturnVal = true;
        if (count($this->options) < 30) {
            $bReturnVal = false;
        }

        $disableComboBox = $this->oDefinition->GetFieldtypeConfigKey('disableComboBox');
        if (!empty($disableComboBox) && ('1' == $disableComboBox || 'true' == $disableComboBox)) {
            $bReturnVal = false;
        }

        return $bReturnVal;
    }

    public function GetOptions()
    {
        if (in_array($this->GetDisplayType(), array('readonly', 'hidden'))) {
            $connectedRecord = $this->getConnectedRecordObject();
            if ($connectedRecord) {
                $this->options = array($this->data => $connectedRecord->GetName());
            } else {
                $this->options = array();
            }

            return;
        }

        $tblName = $this->GetConnectedTableName();

        $this->options = array();
        $query = $this->GetOptionsQuery();
        $oList = call_user_func(array(TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $tblName).'List', 'GetList'), $query);
        $this->allowEmptySelection = true; // add the "please choose" option
        while ($oRow = $oList->Next()) {
            $name = $oRow->GetName();
            if (!empty($name)) {
                $this->options[$oRow->id] = $oRow->GetName();
            }
        }
        unset($oList);
    }

    protected function GetFieldWriterData()
    {
        $aData = parent::GetFieldWriterData();

        $value = \str_replace("'", "\'", $this->data);
        $aData['sFieldDefaultValue'] = "'$value'";

        return $aData;
    }

    public function GetReadOnly()
    {
        $this->GetOptions();
        if (array_key_exists($this->data, $this->options)) {
            return $this->_GetHiddenField().$this->options[$this->data];
        } else {
            return $this->_GetHiddenField();
        }
    }

    public function GoToRecordJS()
    {
        $tblName = $this->GetConnectedTableName();
        $oTableConf = new TCMSTableConf();
        $oTableConf->LoadFromField('name', $tblName);

        $js = "GoToRecordBySelectBox('".$oTableConf->id."','".TGlobal::OutHTML($this->name)."')";

        return $js;
    }

    protected function GetCleanedRestriction($sRestriction)
    {
        if (!empty($sRestriction)) {
            if (preg_match("/\[\{.*?\}\]/", $sRestriction)) {
                $sRestriction = '';
            }
        }

        return $sRestriction;
    }

    protected function GetOptionsQuery()
    {
        $tblName = $this->GetConnectedTableName();

        // check if the field has a counter and fix the lookup table name
        if (is_numeric(mb_substr($tblName, -1))) {
            $tblName = mb_substr($tblName, 0, -2);
        }
        $sRestriction = $this->oDefinition->GetFieldtypeConfigKey('restriction');
        if (!empty($sRestriction)) {
            // replace any fields...
            reset($this->oTableRow->sqlData);
            foreach ($this->oTableRow->sqlData as $key => $value) {
                if (!is_array($value)) {
                    $escapedValue = MySqlLegacySupport::getInstance()->real_escape_string($value);
                    if (stristr($sRestriction, '[{'.$key.'}]')) {
                        if (false === stristr($sRestriction, "'")) {
                            $escapedValue = "'".$escapedValue."'";
                        }
                        $sRestriction = str_replace('[{'.$key.'}]', $escapedValue, $sRestriction);
                    }
                }
            }
            $sRestriction = $this->GetCleanedRestriction($sRestriction);
            reset($this->oTableRow->sqlData);
        }

        $oTableConf = new TCMSTableConf();
        /** @var $oTableConf TCMSTableConf */
        $oTableConf->LoadFromField('name', $tblName);
        $sNameField = $oTableConf->GetNameColumn();

        $sCustomQuery = trim($oTableConf->sqlData['list_query']);
        if (!empty($sCustomQuery) && stristr($sCustomQuery, 'SELECT ')) {
            $query = $sCustomQuery;
            if (!stristr($query, 'WHERE')) {
                $query .= ' WHERE 1=1 ';
            }
        } else {
            $query = 'SELECT `'.MySqlLegacySupport::getInstance()->real_escape_string($tblName).'`.* FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($tblName).'` WHERE 1=1 ';
        }

        if (!empty($sRestriction)) {
            $query .= ' AND '.$sRestriction;
        }

        if ('cms_portal' == $tblName) {
            $oGlobal = TGlobal::instance();
            $sPortalList = $oGlobal->oUser->oAccessManager->user->portals->PortalList();
            if (!empty($sPortalList)) {
                $query .= ' AND `cms_portal`.`id` IN ('.$sPortalList.')';
            }
        } elseif (TTools::FieldExists($tblName, 'cms_portal_id')) {
            // if the table holds a portal id, then restrict the list
            $oGlobal = TGlobal::instance();
            $sPortalList = $oGlobal->oUser->oAccessManager->user->portals->PortalList();
            if (!empty($sPortalList)) {
                $query .= ' AND `'.MySqlLegacySupport::getInstance()->real_escape_string($tblName)."`.`cms_portal_id` IN ('', ".$sPortalList.')';
            } else {
                $query .= ' AND `'.MySqlLegacySupport::getInstance()->real_escape_string($tblName)."`.`cms_portal_id` = ''";
            }
        }

        $sOrderByQuery = "SELECT *
                FROM `cms_tbl_display_orderfields`
               WHERE `cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oTableConf->id)."'
            ORDER BY `position` ASC
             ";
        $rFieldListResult = MySqlLegacySupport::getInstance()->query($sOrderByQuery);
        if (MySqlLegacySupport::getInstance()->num_rows($rFieldListResult) > 0) {
            $databaseConnection = $this->getDatabaseConnection();
            $isFirst = true;
            while ($aField = MySqlLegacySupport::getInstance()->fetch_assoc($rFieldListResult)) {
                $sFieldName = $aField['name'];
                $dir = $aField['sort_order_direction'];

                if (!empty($sFieldName)) {
                    if ($isFirst) {
                        $query .= ' ORDER BY ';
                        $isFirst = false;
                    } else {
                        $query .= ', ';
                    }
                    // we only want to escape the cell name if it has not already been escaped
                    if (false === mb_strpos($sFieldName, '`')) {
                        $sFieldName = $databaseConnection->quoteIdentifier($sFieldName);
                    }
                    $query .= "{$sFieldName} {$dir}";
                }
            }
        } else {
            $query .= ' ORDER BY '.MySqlLegacySupport::getInstance()->real_escape_string($sNameField);
        }

        return $query;
    }

    /**
     * returns the record name.
     *
     * @return string
     */
    public function GetHTMLExport()
    {
        $returnValue = 'not set';
        if (!empty($this->data)) {
            /** @var $oTableRecord TCMSRecord */
            $oRecord = $this->getConnectedRecordObject();
            if (false !== $oRecord) {
                $returnValue = $oRecord->GetName();
            } else {
                $returnValue = 'reference not found';
            }
        }

        return $returnValue;
    }

    /**
     * returns the name of the table this field is connected with
     * checks if special field config variable "connectedTableName"
     * default is fieldname without "_id" ending.
     *
     * @return string
     */
    public function GetConnectedTableName()
    {
        $tblName = $this->oDefinition->GetFieldtypeConfigKey('connectedTableName');
        if (is_null($tblName) || empty($tblName)) {
            if ('_id' == mb_substr($this->name, -3)) {
                $tblName = mb_substr($this->name, 0, -3);
            } else {
                $tblName = $this->name;
            }
        }

        return $tblName;
    }

    /**
     * returns the ReloadOnChange parameter.
     *
     * @return string
     */
    public function GetReloadOnChangeParam()
    {
        $lRet = false;
        $sReloadOnChange = trim($this->oDefinition->GetFieldtypeConfigKey('ReloadOnChange'));
        if (!is_null($sReloadOnChange) && !empty($sReloadOnChange)) {
            if ((true == $sReloadOnChange) || ('true' === strtolower($sReloadOnChange)) || (1 == $sReloadOnChange)) {
                $lRet = true;
            }
        }

        return $lRet;
    }

    /**
     * tries to load the connected record, returns record object or false if no record is connected or record is missing.
     *
     * @return bool|TCMSRecord
     */
    protected function getConnectedRecordObject()
    {
        if (!empty($this->data)) {
            $sTableName = $this->GetConnectedTableName();
            $oRecord = call_user_func(array(TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $sTableName), 'GetNewInstance'));
            if ($oRecord->Load($this->data)) {
                return $oRecord;
            }
        }

        return false;
    }

    protected function GetFieldMethodName($sMethodPostString = '')
    {
        $sName = parent::GetFieldMethodName($sMethodPostString);
        // remove the 'Id' from the end
        if ('_id' == mb_substr($this->name, -3) && 'Id' == mb_substr($sName, -2)) {
            $sName = mb_substr($sName, 0, -2);
        }

        return $sName;
    }

    public function RenderFieldMethodsString()
    {
        $aMethodData = $this->GetFieldMethodBaseDataArray();
        $aMethodData['sMethodName'] = '&'.$this->GetFieldMethodName();
        $class = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $this->GetConnectedTableName());

        $sCode = '';

        if (!empty($class)) {
            $aMethodData['sClassName'] = $class;
            $aMethodData['sReturnType'] = 'null|'.$class;

            $query = "SELECT * FROM cms_tbl_conf where `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->GetConnectedTableName())."'";
            $aTargetTable = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query));
            $aMethodData['sClassType'] = $aTargetTable['dbobject_type'];

            $oViewParser = new TViewParser();
            $oViewParser->bShowTemplatePathAsHTMLHint = false;
            $oViewParser->AddVarArray($aMethodData);

            $sMethodCode = $oViewParser->RenderObjectView('getobject', 'TCMSFields/TCMSFieldLookup');
            $oViewParser->AddVar('sMethodCode', $sMethodCode);

            $sCode = $oViewParser->RenderObjectView('method', 'TCMSFields/TCMSField');
        }

        return $sCode;
    }

    /**
     * render any methods for the auto list class for this field.
     *
     * @return string
     */
    public function RenderFieldListMethodsString()
    {
        $query = "SELECT * FROM `cms_tbl_conf` WHERE `name` = '".$this->GetConnectedTableName()."'";
        $aTableConf = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query));

        $aMethodData = $this->GetFieldMethodBaseDataArray();
        $sInputName = 'i'.ucfirst(TCMSTableToClass::ConvertToClassString($this->name));
        $aParameters = array(
            $sInputName => array(
                'description' => 'ID for the record in: '.$aTableConf['translation'],
                'default' => '',
                'sType' => 'int',
            ),
            'iLanguageId' => array(
                'description' => 'set language id for list - if null, the default language will be used instead',
                'default' => 'null',
                'sType' => 'int',
            ),
        );
        $aMethodData['aParameters'] = $aParameters;

        $sMethodName = 'GetListFor'.TCMSTableToClass::ConvertToClassString($this->name);

        $aMethodData['sMethodName'] = '&'.$sMethodName;
        $aMethodData['sReturnType'] = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $this->sTableName).'List';

        $aMethodData['sClassName'] = $aMethodData['sReturnType'];
        $aMethodData['sClassSubType'] = 'CMSDataObjects';
        $aMethodData['sVisibility'] = 'static public';

        $aMethodData['sClassType'] = $aTableConf['dbobject_type'];

        $aMethodData['iLookupFieldName'] = $sInputName;
        $aMethodData['sTableDatabaseName'] = $this->sTableName;

        $aMethodData['aFieldData']['sFieldFullName'] = 'Return all records belonging to the '.$aTableConf['translation'];

        $oViewParser = new TViewParser();
        $oViewParser->bShowTemplatePathAsHTMLHint = false;
        $oViewParser->AddVarArray($aMethodData);

        $sMethodCode = $oViewParser->RenderObjectView('listmethodcode', 'TCMSFields/TCMSFieldLookupParentID');
        $oViewParser->AddVar('sMethodCode', $sMethodCode);

        return $oViewParser->RenderObjectView('method', 'TCMSFields/TCMSField');
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
     * {@inheritdoc}
     */
    public function HasContent()
    {
        $bHasContent = false;
        if ('' !== $this->data) {
            $bHasContent = true;
        }

        return $bHasContent;
    }

    /**
     * Check if field data is valid when we are in frontend context.
     *
     * @return bool
     */
    public function PkgCmsFormDataIsValid()
    {
        //call parent to skip TABLEEDITOR_RECORD_ID_NOT_VALID message
        return parent::DataIsValid();
    }

    /**
     * {@inheritdoc}
     */
    protected function GetAdditionalViewData()
    {
        $aAdditionalViewData = parent::GetAdditionalViewData();
        $this->GetOptions();
        $aAdditionalViewData['aOptions'] = $this->options;

        return $aAdditionalViewData;
    }

    /**
     * {@inheritdoc}
     */
    public function GetCMSHtmlHeadIncludes()
    {
        $includes = parent::GetCMSHtmlHeadIncludes();
        $includes[] = '<link href="'.TGlobal::GetStaticURLToWebLib('/components/select2.v4/css/select2.min.css').'" media="screen" rel="stylesheet" type="text/css" />';

        return $includes;
    }

    /**
     * {@inheritdoc}
     */
    public function GetCMSHtmlFooterIncludes()
    {
        $includes = parent::GetCMSHtmlFooterIncludes();
        $includes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/components/select2.v4/js/select2.full.min.js').'" type="text/javascript"></script>';

        return $includes;
    }

    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        if (!$this->data) {
            return '';
        }
        $tableName = $this->GetConnectedTableName();
        $nameColumn = 'name';

        $databaseConnection = $this->getDatabaseConnection();
        $quotedTableName = $databaseConnection->quoteIdentifier($tableName);
        $quotedNameColumn = $databaseConnection->quoteIdentifier($nameColumn);
        if (is_array($this->data)) {
            $idList = join(',', array_map(array($databaseConnection, 'quote'), $this->data));
        } else {
            $idList = $databaseConnection->quote($this->data);
        }
        $sQuery = "SELECT `id`, $quotedNameColumn FROM $quotedTableName WHERE `id` in ($idList) ORDER BY $quotedNameColumn";

        $result = $databaseConnection->query($sQuery);
        if (!$result) {
            return '';
        }

        $aRetValueArray = array();
        while ($row = $result->fetch(\PDO::FETCH_ASSOC)) {
            $aRetValueArray[] = $row['name'];
        }

        return implode(', ', $aRetValueArray);
    }

    private function getViewRenderer(): ViewRenderer
    {
        return ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
    }
}

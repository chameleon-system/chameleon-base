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
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\FieldTranslationUtil;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;

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
class TCMSFieldLookup extends TCMSField implements DoctrineTransformableInterface
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
    protected $options = [];

    public function getDoctrineDataModelParts(string $namespace, array $tableNamespaceMapping): DataModelParts
    {
        $propertyName = $this->name;
        if (str_ends_with($propertyName, '_id')) {
            $propertyName = substr($propertyName, 0, -3);
        }

        $parameters = [
            'source' => get_class($this),
            'type' => $this->snakeToPascalCase($this->GetConnectedTableName()),
            'description' => $this->oDefinition->sqlData['translation'],
            'propertyName' => $this->snakeToCamelCase($propertyName),
        ];
        $propertyCode = $this->getDoctrineRenderer('model/lookup.property.php.twig', $parameters)->render();
        $methodCode = $this->getDoctrineRenderer('model/lookup.methods.php.twig', $parameters)->render();

        return new DataModelParts(
            $propertyCode,
            $methodCode,
            $this->getDoctrineDataModelXml($namespace, $tableNamespaceMapping),
            [
                ltrim(
                    sprintf('%s\\%s', $tableNamespaceMapping[$this->GetConnectedTableName()], $this->snakeToPascalCase($this->GetConnectedTableName())),
                    '\\'
                ),
            ],
            true
        );
    }

    protected function getDoctrineDataModelXml(string $namespace, array $tableNamespaceMapping): string
    {
        $propertyName = $this->name;
        if (str_ends_with($propertyName, '_id')) {
            $propertyName = substr($propertyName, 0, -3);
        }

        $viewName = 'mapping/many-to-one.xml.twig';

        return $this->getDoctrineRenderer($viewName, [
            'fieldName' => $this->snakeToCamelCase($propertyName),
            'targetClass' => sprintf('%s\\%s', $tableNamespaceMapping[$this->GetConnectedTableName()], $this->snakeToPascalCase($this->GetConnectedTableName())),
            'column' => $this->name,
            'comment' => $this->oDefinition->sqlData['translation'],
        ])->render();
    }

    public function GetHTML()
    {
        $this->GetOptions();
        $connectedRecord = $this->getConnectedRecordObject();

        if (!empty($this->data) && (!isset($this->options[$this->data]) && false !== $connectedRecord)) {
            return $this->GetReadOnly();
        }

        $viewRenderer = $this->getViewRenderer();
        $this->addFieldRenderVariables($viewRenderer);

        // current ID is an orphan, show message
        if (!empty($this->data) && false === $connectedRecord) {
            $viewRenderer->AddSourceObject('showErrorMessage', true);
        }

        return $viewRenderer->Render('TCMSFieldLookup/fieldLookup.html.twig', null, false);
    }

    private function addFieldRenderVariables(ViewRenderer $viewRenderer): void
    {
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        $sClass = '';
        $sOnChangeAttr = '';
        if ($this->GetReloadOnChangeParam()) {
            $sOnChangeAttr = "OnChange=\"CHAMELEON.CORE.MTTableEditor.bCmsContentChanged=false;CHAMELEON.CORE.showProcessingModal();document.cmseditform.elements['module_fnc[contentmodule]'].value='Save';document.cmseditform.submit();\"";
            $sClass .= 'cmsdisablechangemessage';
        }

        $viewRenderer->AddSourceObject('fieldName', $this->name);
        $viewRenderer->AddSourceObject('fieldValue', $this->_GetHTMLValue());
        $viewRenderer->AddSourceObject('language', $securityHelper->getUser()?->getCurrentEditLanguageIsoCode());
        $viewRenderer->AddSourceObject('sClass', $sClass);
        $viewRenderer->AddSourceObject('onchangeAttr', $sOnChangeAttr);
        $viewRenderer->AddSourceObject('options', $this->options);
        $viewRenderer->AddSourceObject('allowEmptySelection', $this->allowEmptySelection);

        $foreignTableName = $this->GetConnectedTableName();
        $viewRenderer->AddSourceObject('foreignTableName', $foreignTableName);

        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        if ($securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT, $foreignTableName)) {
            $viewRenderer->AddSourceObject('buttonLink', $this->getSelectedEntryLink($this->data));
        }
        $viewRenderer->AddSourceObject('connectedRecordId', $this->data);
    }

    /**
     * comboBox is enabled on 30 elements or more
     * you may disable the combobox using "disableComboBox=true" in field config or by extending this method.
     *
     * @deprecated since 6.3.0 - no longer used
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
        if (in_array($this->GetDisplayType(), ['readonly', 'hidden'])) {
            $connectedRecord = $this->getConnectedRecordObject();
            if ($connectedRecord) {
                $this->options = [$this->data => $connectedRecord->GetName()];
            } else {
                $this->options = [];
            }

            return;
        }

        $tblName = $this->GetConnectedTableName();

        $this->options = [];
        $query = $this->GetOptionsQuery();
        $oList = call_user_func([TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $tblName).'List', 'GetList'], $query);
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
        $aData['sFieldType'] .= '|null';

        return $aData;
    }

    public function GetReadOnly()
    {
        $html = $this->_GetHiddenField();
        $html .= '<div class="row">';

        $this->GetOptions();
        if (array_key_exists($this->data, $this->options)) {
            $html .= '<div class="form-content-simple col-12 col-lg-8">'.$this->options[$this->data].'</div>';
        }

        if (false === empty($this->data)) {
            $html .= '<div class="col-12 pt-2 col-lg-4 pt-lg-0">';
            $html .= TCMSRender::DrawButton(
                ServiceLocator::get('translator')->trans('chameleon_system_core.field_lookup.switch_to'),
                $this->getSelectedEntryLink($this->data),
                'far fa-edit'
            );
            $html .= '</div>';
        }

        $html .= '</div>';

        return $html;
    }

    protected function getSelectedEntryLink(string $value): string
    {
        $foreignTableName = $this->GetConnectedTableName();
        $tableConf = TdbCmsTblConf::GetNewInstance();
        if (false === $tableConf->LoadFromField('name', $foreignTableName)) {
            return '';
        }

        $linkParams = [
            'pagedef' => 'tableeditor',
            'tableid' => $tableConf->id,
            'id' => urlencode($value),
        ];

        return PATH_CMS_CONTROLLER.$this->getUrlUtil()->getArrayAsUrl($linkParams, '?', '&');
    }

    /**
     * @deprecated not used anymore - replaced by normal links (also see getSelectedEntryLink() here)
     */
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
        /* @var $oTableConf TCMSTableConf */
        $oTableConf->LoadFromField('name', $tblName);

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
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        $portals = $securityHelper->getUser()?->getPortals();
        $sPortalList = implode(', ', array_map(fn (string $portalId) => $this->getDatabaseConnection()->quote($portalId), array_keys($portals)));

        if ('cms_portal' == $tblName) {
            if (!empty($sPortalList)) {
                $query .= ' AND `cms_portal`.`id` IN ('.$sPortalList.')';
            }
        } elseif (TTools::FieldExists($tblName, 'cms_portal_id')) {
            // if the table holds a portal id, then restrict the list
            if (!empty($sPortalList)) {
                $query .= ' AND '.$this->getDatabaseConnection()->quoteIdentifier($tblName).".`cms_portal_id` IN ('', ".$sPortalList.')';
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
            $nameField = $oTableConf->GetNameColumn();
            $editLanguageId = $this->getBackendSession()->getCurrentEditLanguageId();
            $language = TdbCmsLanguage::GetNewInstance($editLanguageId);
            $nameField = $this->getFieldTranslationUtil()->getTranslatedFieldName($tblName, $nameField, $language);
            $query .= ' ORDER BY '.MySqlLegacySupport::getInstance()->real_escape_string($nameField);
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
            $oRecord = call_user_func([TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $sTableName), 'GetNewInstance']);
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
        $aMethodData['sMethodName'] = $this->GetFieldMethodName();
        $class = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $this->GetConnectedTableName());

        $sCode = '';

        if (!empty($class)) {
            $aMethodData['sClassName'] = $class;
            $aMethodData['sReturnType'] = 'null|'.$class;

            $query = "SELECT * FROM cms_tbl_conf where `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->GetConnectedTableName())."'";
            $aTargetTable = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query));
            $aMethodData['sClassType'] = $aTargetTable['dbobject_type'] ?? '';

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
        $aParameters = [
            $sInputName => [
                'description' => 'ID for the record in: '.($aTableConf['translation'] ?? ' [not found]'),
                'default' => '',
                'sType' => 'string',
            ],
            'iLanguageId' => [
                'description' => 'set language id for list - if null, the default language will be used instead',
                'default' => 'null',
                'sType' => 'string|null',
            ],
        ];
        $aMethodData['aParameters'] = $aParameters;

        $sMethodName = 'GetListFor'.TCMSTableToClass::ConvertToClassString($this->name);

        $aMethodData['sMethodName'] = $sMethodName;
        $aMethodData['sReturnType'] = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $this->sTableName).'List';

        $aMethodData['sClassName'] = $aMethodData['sReturnType'];
        $aMethodData['sClassSubType'] = 'CMSDataObjects';
        $aMethodData['sVisibility'] = 'static public';

        $aMethodData['sClassType'] = $aTableConf['dbobject_type'] ?? '';

        $aMethodData['iLookupFieldName'] = $sInputName;
        $aMethodData['sTableDatabaseName'] = $this->sTableName;

        $aMethodData['aFieldData']['sFieldFullName'] = 'Return all records belonging to the '.($aTableConf['translation'] ?? ' [not found]');

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
        // call parent to skip TABLEEDITOR_RECORD_ID_NOT_VALID message
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
        $connectedTableConf = TdbCmsTblConf::GetNewInstance();
        $confLoadSuccess = $connectedTableConf->LoadFromField('name', $tableName);

        if (false === $confLoadSuccess) {
            return '';
        }

        $identifyingColumnName = $connectedTableConf->GetNameColumn();

        $databaseConnection = $this->getDatabaseConnection();
        $quotedTableName = $databaseConnection->quoteIdentifier($tableName);
        $quotedIdentifierColumn = $databaseConnection->quoteIdentifier($identifyingColumnName);
        if (is_array($this->data)) {
            $idList = join(',', array_map([$databaseConnection, 'quote'], $this->data));
        } else {
            $idList = $databaseConnection->quote($this->data);
        }
        $sQuery = "SELECT $quotedIdentifierColumn FROM $quotedTableName WHERE `id` in ($idList) ORDER BY $quotedIdentifierColumn";

        try {
            $result = $databaseConnection->executeQuery($sQuery);
        } catch (Doctrine\DBAL\Exception $e) {
            // If the query fails, handle it here
            return '';
        }

        $aRetValueArray = [];
        while ($row = $result->fetchAssociative()) {
            $aRetValueArray[] = $row[$identifyingColumnName];
        }

        return implode(', ', $aRetValueArray);
    }

    private function getFieldTranslationUtil(): FieldTranslationUtil
    {
        return ServiceLocator::get('chameleon_system_core.util.field_translation');
    }

    private function getViewRenderer(): ViewRenderer
    {
        return ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
    }

    private function getUrlUtil(): UrlUtil
    {
        return ServiceLocator::get('chameleon_system_core.util.url');
    }
}

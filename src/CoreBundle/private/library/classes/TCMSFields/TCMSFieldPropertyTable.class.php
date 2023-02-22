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
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\DatabaseMigration\DataModel\LogChangeDataModel;
use ChameleonSystem\DatabaseMigration\Query\MigrationQueryData;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;
use function PHPUnit\Framework\stringEndsWith;

/**
 * presents a 1:n table (ie n records for the current table)
 * in this case $this->data contains the name of the foreign table. the foreign table
 * must always contain an id of the form current_table_name_id
 * you can also define what field to use as a match in the target table by using: fieldNameInConnectedTable=fieldname
 * we can show the subtable as a standard liste..
 * you can open the field on load via bOpenOnLoad=true  (field config)
 * you can open the field as a 1:1 relation via bOnlyOneRecord=true (field config).
 */
class TCMSFieldPropertyTable extends TCMSFieldVarchar
{
    /**
     * view path for frontend.
     */
    protected $sViewPath = 'TCMSFields/views/TCMSFieldProperty';

    public function getDoctrineDataModelParts(string $namespace): DataModelParts
    {
        $parentFieldName = $this->GetMatchingParentFieldName();
        if (stringEndsWith($parentFieldName, '_id')) {
            $parentFieldName = substr($parentFieldName, 0, -3);
        }
        $parameters = [
            'source' => __CLASS__,
            'type' => $this->snakeToCamelCase($this->GetPropertyTableName(), false),
            'description' => $this->oDefinition->sqlData['translation'],
            'propertyName' => $this->snakeToCamelCase($this->name.'_collection'),
            'methodParameter' => $this->snakeToCamelCase($this->name),
            'parentFieldName' => $this->snakeToCamelCase($parentFieldName),
        ];
        $parameters['docCommentType'] = sprintf('Collection<int, %s>', $parameters['type']);

        $oneToOneRelation = $this->isOneToOneConnection();

        if (true === $oneToOneRelation) {
            $propertyCode = $this->getDoctrineRenderer('model/lookup.property.php.twig', $parameters)->render();
            $methodCode = $this->getDoctrineRenderer('model/lookup.methods.php.twig', $parameters)->render();
        } else {
            $propertyCode = $this->getDoctrineRenderer('model/property-list.property.php.twig', $parameters)->render();
            $methodCode = $this->getDoctrineRenderer('model/property-list.methods.php.twig', $parameters)->render();
        }

        return new DataModelParts(
            $propertyCode,
            $methodCode,
            [
                ltrim(sprintf('%s\\%s', $namespace, $this->snakeToCamelCase($this->GetPropertyTableName(), false)), '\\'),
                'Doctrine\\Common\\Collections\\Collection',
                'Doctrine\\Common\\Collections\\ArrayCollection',
            ],
            true
        );
    }


    public function getDoctrineDataModelXml(string $namespace): string
    {
        $preventReferenceDelete = $this->oDefinition->GetFieldtypeConfigKey('preventReferenceDeletion') ?? 'false';
        $enableCascadeRemove = 'false' === $preventReferenceDelete;

        $oneToOneRelation = $this->isOneToOneConnection();


        $parentFieldName = $this->GetMatchingParentFieldName();
        if (stringEndsWith($parentFieldName, '_id')) {
            $parentFieldName = substr($parentFieldName, 0, -3);
        }
        $parameters = [
            'fieldName' => $this->snakeToCamelCase($this->name.'_collection'),
            'targetClass' => sprintf(
                '%s\\%s',
                $namespace,
                $this->snakeToCamelCase($this->GetPropertyTableName(), false)
            ),
            'parentFieldName' => $this->snakeToCamelCase($parentFieldName),
            'enableCascadeRemove' => $enableCascadeRemove,
        ];

        $viewName = 'mapping/one-to-many-property-list.xml.twig';
        if (true === $oneToOneRelation) {
            $viewName = 'mapping/one-to-one-bidirectional.xml.twig';
        }

        return $this->getDoctrineRenderer($viewName, $parameters)->render();
    }

    public function __construct()
    {
        $this->isPropertyField = true;
    }

    public function GetHTML()
    {
        /** @var TTableEditorListFieldState $stateContainer */
        $stateContainer = \ChameleonSystem\CoreBundle\ServiceLocator::get('cmsPkgCore.tableEditorListFieldState');

        $inputFilterUtil = $this->getInputFilterUtil();

        $aStateURL = array(
            'pagedef' => $inputFilterUtil->getFilteredInput('pagedef'),
            'tableid' => $inputFilterUtil->getFilteredInput('tableid'),
            'id' => $inputFilterUtil->getFilteredInput('id'),
            'fieldname' => $this->name,
            'module_fnc' => array('contentmodule' => 'ExecuteAjaxCall'),
            '_fnc' => 'changeListFieldState',
        );
        $sStateURL = '?'.TTools::GetArrayAsURLForJavascript($aStateURL);

        $html = '';
        $sPropertyTableName = $this->GetPropertyTableName();
        if (empty($sPropertyTableName) || !TGlobal::TableExists($sPropertyTableName)) {
            $html = 'Die Property Tabelle ['.TGlobal::OutHTML($sPropertyTableName).'] ist entweder nicht angegeben, oder existiert nicht!';
            if (empty($sPropertyTableName)) {
                $html .= '<br />Das Feld '.TGlobal::OutHTML($this->name).' sollte den Namen der Property Tabelle enthalten';
            }
        } else {
            $sOnClickEvent = $this->getOnClickEvent();

            $sEscapedName = TGlobal::OutHTML($this->name);

            $html .= '<input type="hidden" id="'.TGlobal::OutHTML($this->name).'" name="'.TGlobal::OutHTML($this->name).'" value="'.TGlobal::OutHTML($sPropertyTableName).'" />';
            $html .= '<div class="card">
            <div class="card-header p-1">
                <div class="card-action" 
                data-fieldstate="'.TGlobal::OutHTML($stateContainer->getState($this->sTableName, $this->name)).'" 
                id="mltListControllButton'.$sEscapedName.'" 
                onClick="setTableEditorListFieldState(this, \''.$sStateURL.'\'); '.$sOnClickEvent.'">
                <i class="fas fa-eye"></i> '.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.field_property.open_or_close_list')).'
                </div>
            </div>
            <div class="card-body p-0">
                <div id="'.$sEscapedName.'_iframe_block">
                    <iframe id="'.$sEscapedName.'_iframe" class="d-none"></iframe>
                </div>
            </div>
            </div>';

            if ('true' == $this->oDefinition->GetFieldtypeConfigKey('bOpenOnLoad') || $stateContainer->getState($this->sTableName, $this->name) == $stateContainer::STATE_OPEN) {
                $html .= "
            <script type=\"text/javascript\">
            $(document).ready(function() {
              {$sOnClickEvent}
            }); </script>
          ";
            }
        }

        return $html;
    }

    /**
     * generates the URL for the MLT iFrame List and returns the javascript method to open the iframe.
     *
     * @return string
     */
    protected function getOnClickEvent()
    {
        $sPropertyTableName = $this->GetPropertyTableName();
        $sOwningField = $this->GetMatchingParentFieldName();
        $oForeignTableConf = new TCMSTableConf();
        /** @var $oForeignTableConf TCMSTableConf */
        $oForeignTableConf->LoadFromField('name', $sPropertyTableName);

        $aEditorRequest = array('pagedef' => 'tablemanagerframe', 'id' => $oForeignTableConf->id, 'sRestrictionField' => $sOwningField, 'sRestriction' => $this->recordId, 'bIsLoadedFromIFrame' => 1, 'field' => $this->name);
        if ('true' == $this->oDefinition->GetFieldtypeConfigKey('bOnlyOneRecord') || '1' == $oForeignTableConf->sqlData['only_one_record_tbl']) {
            $aEditorRequest['bOnlyOneRecord'] = 'true';
            $databaseConnection = $this->getDatabaseConnection();
            $quotedForeignName = $databaseConnection->quoteIdentifier($oForeignTableConf->sqlData['name']);
            $quotedOwningField = $databaseConnection->quoteIdentifier($sOwningField);
            $quotedRecordId = $databaseConnection->quote($this->recordId);
            $query = "SELECT * FROM $quotedForeignName WHERE $quotedOwningField = $quotedRecordId";
            if ($aTmRow = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
                $aEditorRequest['pagedef'] = 'tableeditorPopup';
                $aEditorRequest['tableid'] = $oForeignTableConf->id;
                $aEditorRequest['id'] = $aTmRow['id'];
            } else {
                $aEditorRequest['sTableEditorPagdef'] = 'tableeditorPopup';
            }
        }
        $sOnClickEvent = "CHAMELEON.CORE.MTTableEditor.switchMultiSelectListState('".TGlobal::OutJS($this->name)."_iframe','".PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript($aEditorRequest)."');";

        return $sOnClickEvent;
    }

    private function isOneToOneConnection(): bool
    {
        /** @var $oForeignTableConf TCMSTableConf */
        $oForeignTableConf = new TCMSTableConf();
        $sPropertyTableName = $this->GetPropertyTableName();
        $oForeignTableConf->LoadFromField('name', $sPropertyTableName);
        return 'true' === $this->oDefinition->GetFieldtypeConfigKey('bOnlyOneRecord') || '1' === $oForeignTableConf->sqlData['only_one_record_tbl'];
    }

    /**
     * returns the connected proprty table name.
     *
     * @return string
     */
    public function GetPropertyTableName()
    {
        $sTableName = $this->data;
        if (empty($this->data)) {
            $sTableName = $this->oDefinition->GetFieldtypeConfigKey('connectedTableName');
            if (empty($sTableName)) {
                if (!empty($this->oDefinition->sqlData['field_default_value'])) {
                    $sTableName = $this->oDefinition->sqlData['field_default_value'];
                }
            }

            if (empty($sTableName)) {
                $sTableName = $this->name;
            }
        }

        return $sTableName;
    }

    /**
     * overwrite to delete the related items in the target table
     * here we assume that $this->data is set!
     */
    public function DeleteFieldDefinition()
    {
        $tableName = $this->data;
        if (!empty($tableName)) {
            $targetField = $tableName.'_id';

            $query = 'DELETE FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($tableName).'`
                        WHERE `'.MySqlLegacySupport::getInstance()->real_escape_string($targetField)."` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->recordId)."'";
            MySqlLegacySupport::getInstance()->query($query);

            $editLanguage = $this->getLanguageService()->getActiveEditLanguage();
            $migrationQueryData = new MigrationQueryData($tableName, $editLanguage->fieldIso6391);
            $migrationQueryData
                ->setWhereEquals(array(
                    $targetField => $this->recordId,
                ));
            $aQuery = array(new LogChangeDataModel($migrationQueryData, LogChangeDataModel::TYPE_DELETE));
            TCMSLogChange::WriteTransaction($aQuery);
        }
        parent::DeleteFieldDefinition();
    }

    protected function GetFieldMethodName($sMethodPostString = '')
    {
        $sName = parent::GetFieldMethodName($sMethodPostString);
        $sName .= 'List';

        return $sName;
    }

    public function RenderFieldMethodsString()
    {
        $sHTML = '';

        $sOwningField = $this->GetMatchingParentFieldName();

        if (!empty($sOwningField)) {
            $aMethodData = $this->GetFieldMethodBaseDataArray();
            $aMethodData['sMethodName'] = $this->GetFieldMethodName();
            $aMethodData['sReturnType'] = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $this->GetPropertyTableName()).'List';

            $aMethodData['sCallMethodName'] = 'GetListFor'.TCMSTableToClass::GetClassName('', $sOwningField);

            $oViewParser = new TViewParser();
            /** @var $oViewParser TViewParser */
            $oViewParser->bShowTemplatePathAsHTMLHint = false;
            $oViewParser->AddVarArray($aMethodData);

            $sMethodCode = $oViewParser->RenderObjectView('getproperties', 'TCMSFields/TCMSFieldPropertyTable');
            $oViewParser->AddVar('sMethodCode', $sMethodCode);

            $sHTML = $oViewParser->RenderObjectView('method', 'TCMSFields/TCMSField');
        }

        return $sHTML;
    }

    /**
     * return the name of the matching parent field name in the target table.
     *
     * @return string
     */
    public function GetMatchingParentFieldName()
    {
        $sOwningField = $this->oDefinition->GetFieldtypeConfigKey('fieldNameInConnectedTable');
        if (null !== $sOwningField) {
            return $sOwningField;
        }

        $query = "SELECT `cms_field_conf`.*, `cms_field_type`.`constname` AS field_type_constname, `cms_field_type`.`fieldclass` AS field_type_fieldclass, `cms_field_type`.`class_type` AS field_type_class_type
                    FROM `cms_field_conf`
              INNER JOIN `cms_tbl_conf` ON `cms_field_conf`.`cms_tbl_conf_id` = `cms_tbl_conf`.`id`
              INNER JOIN `cms_field_type` ON `cms_field_conf`.`cms_field_type_id` = `cms_field_type`.`id`
                   WHERE `cms_tbl_conf`.`name` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->GetPropertyTableName())."'
                     AND  `cms_field_type`.`constname` IN ('CMSFIELD_PROPERTY_PARENT_ID','CMSFIELD_TABLELIST')
                 ";

        $rFieldConfsInTargetTable = MySqlLegacySupport::getInstance()->query($query);
        while ($aTargetFieldConf = MySqlLegacySupport::getInstance()->fetch_assoc($rFieldConfsInTargetTable)) {
            $oFieldDef = new TCMSFieldDefinition();
            $oFieldDef->LoadFromRow($aTargetFieldConf);
            $sTargetTable = $oFieldDef->GetFieldtypeConfigKey('connectedTableName');
            if (is_null($sTargetTable)) {
                $sTargetTable = substr($oFieldDef->sqlData['name'], 0, -3);
            }
            if ($sTargetTable == $this->sTableName) {
                $sOwningField = $oFieldDef->sqlData['name'];
            }
        }
        if (null === $sOwningField) {
            $sOwningField = $this->sTableName.'_id';
        }

        return $sOwningField;
    }

    public function GetReadOnly()
    {
        return $this->GetHTML();
    }

    /**
     * returns the modifier (none, hidden, readonly) of the field. if the field
     * is restricted, and the modifier is none, then we return readonly instead.
     *
     * @return string
     */
    public function GetDisplayType()
    {
        $modifier = parent::GetDisplayType();
        $oGlobal = TGlobal::instance();
        if ('1' == $this->oDefinition->sqlData['restrict_to_groups']) {
            // check if the user is in one of the connected groups

            /** @var SecurityHelperAccess $securityHelper */
            $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
            if (!$securityHelper->isGranted(CmsPermissionAttributeConstants::ACCESS, $this->oDefinition)) {
                $modifier = 'hidden';
            } else {
                if (!$this->hasViewRightToPropertyTable()) {
                    $modifier = 'hidden';
                }
            }
        } else {
            if (!$this->hasViewRightToPropertyTable()) {
                $modifier = 'hidden';
            }
        }

        return $modifier;
    }

    protected function hasViewRightToPropertyTable()
    {
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        return $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_ACCESS, $this->GetPropertyTableName());
    }

    /**
     * Get additional view data for the render method.
     *
     * @return array
     */
    protected function GetAdditionalViewData()
    {
        $aAdditionalViewData = parent::GetAdditionalViewData();
        $aAdditionalViewData['sForeignTableName'] = $this->GetPropertyTableNameFrontend();
        $aAdditionalViewData['oFieldsTargetTable'] = $this->GetFieldsTargetTableFrontend();
        $aAdditionalViewData['aRecordsConnected'] = $this->GetRecordsConnectedArrayFrontend();

        return $aAdditionalViewData;
    }

    public function GetFieldNameForFieldFrontend($oField)
    {
        return TGlobal::OutHTML($oField->fieldTranslation);
    }

    /**
     * Get an array of either posted data or data from db if nothings has been posted.
     *
     * @return array
     */
    protected function GetRecordsConnectedArrayFrontend()
    {
        $aData = array();
        $sForeignTableName = $this->GetPropertyTableNameFrontend();
        $iCounter = 0;
        if (is_array($this->data) && count($this->data) > 0) {
            //we assume data was already posted
            foreach ($this->data as $aRow) {
                if (is_array($aRow)) {
                    $aData[$iCounter] = $aRow;
                    ++$iCounter;
                }
            }
        } elseif (!empty($this->oTableRow->id)) {
            $oFields = $this->GetFieldsTargetTableFrontend();
            $sSql = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($sForeignTableName).'`
                  WHERE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'_id'."`='".MySqlLegacySupport::getInstance()->real_escape_string($this->oTableRow->id)."'";
            $rRes = MySqlLegacySupport::getInstance()->query($sSql);
            while ($aRow = MySqlLegacySupport::getInstance()->fetch_assoc($rRes)) {
                while ($oField = $oFields->Next()) {
                    if ($oField->fieldName != $this->sTableName.'_id') {
                        $aData[$iCounter][$oField->fieldName] = $aRow[$oField->fieldName];
                    }
                }
                $aData[$iCounter]['id'] = $aRow['id'];
                ++$iCounter;
                $oFields->GoToStart();
            }
        }

        return $aData;
    }

    /**
     * @return TIterator
     */
    protected function GetFieldsTargetTableFrontend()
    {
        static $oFields = null;
        if (is_null($oFields)) {
            $oFields = new TIterator();
            $sForeignTableName = $this->GetPropertyTableNameFrontend();
            $oTblConf = TdbCmsTblConf::GetNewInstance();
            $oTblConf->LoadFromField('name', $sForeignTableName);
            $oTmpFields = TdbCmsFieldConfList::GetList();
            $oTmpFields->AddFilterString("`cms_tbl_conf_id`='".MySqlLegacySupport::getInstance()->real_escape_string($oTblConf->id)."'");
            while ($oTmpField = $oTmpFields->Next()) {
                $oFields->AddItem($oTmpField);
            }
        }
        $oFields->GoToStart();

        return $oFields;
    }

    public function PkgCmsFormPostSaveHook($sId, $oForm)
    {
        // -----------------------------------------------------------------------
        if (is_array($this->data) && array_key_exists('x', $this->data)) {
            unset($this->data['x']);
        }
        $sForeignTableName = $this->GetPropertyTableNameFrontend();
        if (!empty($sForeignTableName) && TTools::FieldExists($sForeignTableName, $this->sTableName.'_id')) {
            $aConnectedRecordIdsToDelete = array();
            if (!empty($this->oTableRow->id)) {
                $sSql = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($sForeignTableName).'`
                      WHERE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'_id'."`='".MySqlLegacySupport::getInstance()->real_escape_string($this->oTableRow->id)."'";
                $rRes = MySqlLegacySupport::getInstance()->query($sSql);
                while ($aRow = MySqlLegacySupport::getInstance()->fetch_assoc($rRes)) {
                    $aConnectedRecordIdsToDelete[$aRow['id']] = $aRow['id'];
                }
            }
            if (is_array($this->data) && count($this->data) > 0) {
                foreach ($this->data as $aRow) {
                    $sRecordId = null;
                    if (array_key_exists('id', $aRow) && TTools::RecordExistsArray($sForeignTableName, array('id' => $aRow['id'], $this->sTableName.'_id' => $sId))) {
                        unset($aConnectedRecordIdsToDelete[$aRow['id']]);
                        $sRecordId = $aRow['id'];
                    } else {
                        unset($aRow['id']);
                    }
                    $oTableEditor = TTools::GetTableEditorManager($sForeignTableName, $sRecordId);
                    $aRow[$this->sTableName.'_id'] = $sId;
                    $oTableEditor->AllowEditByAll(true);
                    $oTableEditor->Save($aRow);
                    $oTableEditor->AllowEditByAll(false);
                }
            }
            if (is_array($aConnectedRecordIdsToDelete) && count($aConnectedRecordIdsToDelete) > 0) {
                foreach (array_keys($aConnectedRecordIdsToDelete) as $sDeleteId) {
                    $oTableEditor = TTools::GetTableEditorManager($sForeignTableName, $sDeleteId);
                    $oTableEditor->AllowDeleteByAll(true);
                    $oTableEditor->Delete($sDeleteId);
                    $oTableEditor->AllowDeleteByAll(false);
                }
            }
        }
    }

    /**
     * returns the connected property table name.
     *
     * @return string
     */
    public function GetPropertyTableNameFrontend()
    {
        $sTableName = $this->oDefinition->GetFieldtypeConfigKey('connectedTableName');
        if (empty($sTableName)) {
            if (!empty($this->oDefinition->sqlData['field_default_value'])) {
                $sTableName = $this->oDefinition->sqlData['field_default_value'];
            }
        }

        if (empty($sTableName)) {
            $sTableName = $this->name;
        }

        return $sTableName;
    }

    /**
     * Checks if its allowed to delete record references for property field.
     *
     * @return bool
     */
    public function allowDeleteRecordReferences()
    {
        $sPreventReferenceDeletion = $this->oDefinition->GetFieldtypeConfigKey('preventReferenceDeletion');
        $bAllowDeleteRecordReferences = true;
        if ('true' === $sPreventReferenceDeletion) {
            $bAllowDeleteRecordReferences = false;
        }

        return $bAllowDeleteRecordReferences;
    }

    /**
     * @return InputFilterUtilInterface
     */
    private function getInputFilterUtil()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.input_filter');
    }
}

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
use ChameleonSystem\DatabaseMigration\DataModel\LogChangeDataModel;
use ChameleonSystem\DatabaseMigration\Query\MigrationQueryData;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;
use Doctrine\Common\Collections\Expr\Comparison;

/**
 * Lookup to different tables.
 */
class TCMSFieldExtendedLookupMultiTable extends TCMSFieldExtendedLookup
{
    public const TABLE_NAME_FIELD_SUFFIX = '_table_name';

    public const FIELD_SYSTEM_NAME = 'CMSFIELD_EXTENDEDMULTITABLELIST';

    public function getDoctrineDataModelParts(string $namespace, array $tableNamespaceMapping): DataModelParts
    {
        // todo: we currently handle this field as two string fields - we may be able to replace this with single table inheritance from doctrine
        // https://www.doctrine-project.org/projects/doctrine-orm/en/2.14/reference/inheritance-mapping.html#entity-inheritance
        // alternatively we may need to use a relation that maps the type
        // item_link[source_id, target_id, type].
        $parameters = [
            'source' => get_class($this),
            'type' => 'string',
            'docCommentType' => 'string',
            'description' => $this->oDefinition->sqlData['translation'],
            'propertyName' => $this->snakeToCamelCase($this->name),
            'defaultValue' => sprintf("'%s'", addslashes($this->oDefinition->sqlData['field_default_value'])),
            'allowDefaultValue' => true,
            'getterName' => 'get'.$this->snakeToPascalCase($this->name),
            'setterName' => 'set'.$this->snakeToPascalCase($this->name),
        ];
        $propertyCode = $this->getDoctrineRenderer('model/default.property.php.twig', $parameters)->render();
        $methodCode = $this->getDoctrineRenderer('model/default.methods.php.twig', $parameters)->render();

        $idField = new DataModelParts(
            $propertyCode,
            $methodCode,
            $this->getDoctrineDataModelXml($namespace, $tableNamespaceMapping),
            [],
            true
        );

        $parameters = [
            'source' => get_class($this),
            'type' => 'string',
            'docCommentType' => 'string',
            'description' => $this->oDefinition->sqlData['translation'],
            'propertyName' => $this->snakeToCamelCase($this->getTableFieldName()),
            'defaultValue' => sprintf("'%s'", addslashes($this->oDefinition->sqlData['field_default_value'])),
            'allowDefaultValue' => true,
            'getterName' => 'get'.$this->snakeToPascalCase($this->getTableFieldName()),
            'setterName' => 'set'.$this->snakeToPascalCase($this->getTableFieldName()),
        ];
        $propertyCode = $this->getDoctrineRenderer('model/default.property.php.twig', $parameters)->render();
        $methodCode = $this->getDoctrineRenderer('model/default.methods.php.twig', $parameters)->render();

        $tableNameField = new DataModelParts(
            $propertyCode,
            $methodCode, '',
            [],
            true
        );

        return $idField->merge($tableNameField);
    }

    protected function getDoctrineDataModelXml(string $namespace, array $tableNamespaceMapping): string
    {
        $idMapping = $this->getDoctrineRenderer('mapping/string.xml.twig', [
            'fieldName' => $this->snakeToCamelCase($this->name),
            'type' => 'string',
            'column' => $this->name,
            'length' => '' === $this->oDefinition->sqlData['length_set'] ? 255 : $this->oDefinition->sqlData['length_set'],
            'comment' => $this->oDefinition->sqlData['translation'],
            'default' => $this->oDefinition->sqlData['field_default_value'],
        ])->render();

        $tableNameMapping = $this->getDoctrineRenderer('mapping/string.xml.twig', [
            'fieldName' => $this->snakeToCamelCase($this->getTableFieldName()),
            'type' => 'string',
            'column' => $this->getTableFieldName(),
            'length' => '255',
            'comment' => $this->oDefinition->sqlData['translation'],
            'default' => $this->oDefinition->sqlData['field_default_value'],
        ])->render();

        return $idMapping."\n".$tableNameMapping;
    }

    /**
     * returns the name of the table this field is connected with
     * depends on the hidden field fieldName_table_name that will be set after the save
     * if there is no record connected the hidden field fieldName_table_name isn't set as well
     * so we're fetching the list of allowed tables (set via field type config parameter) and using the first table in there
     * if there are no tables set we try to get the table name by calling the parent function this one will use the fieldName
     * or the field config parameter connectedTableName.
     *
     * @return string
     */
    public function GetConnectedTableName()
    {
        if (!is_null($this->oTableRow) && array_key_exists($this->getTableFieldName(), $this->oTableRow->sqlData)) {
            $sTableName = $this->oTableRow->sqlData[$this->getTableFieldName()];
        } else {
            $sTableName = '';
        }
        if (is_null($sTableName) || empty($sTableName)) {
            $aTables = $this->GetAllowedTables();
            if (count($aTables) > 0) {
                reset($aTables);
                $sTableName = current($aTables);
            } else {
                $sTableName = parent::GetConnectedTableName();
            }
        }

        return $sTableName;
    }

    protected function getTableFieldName(string $tableName = ''): string
    {
        if ('' === $tableName) {
            $tableName = $this->name;
        }

        return $tableName.self::TABLE_NAME_FIELD_SUFFIX;
    }

    /**
     * renders a input field of type "hidden"
     * will store the table name of the connected record on selection of the record.
     *
     * @return string
     */
    protected function _GetHiddenField()
    {
        $html = parent::_GetHiddenField();

        $tableFieldNameEscaped = TGlobal::OutHTML($this->getTableFieldName());
        $html .= '<input type="hidden" name="'.$tableFieldNameEscaped.'" id="'.$tableFieldNameEscaped.'" value="'.TGlobal::OutHTML($this->oTableRow->sqlData[$tableFieldNameEscaped]).'" />'."\n";

        return $html;
    }

    /**
     * generates HTML for the buttons that open the layover with list of records
     * generates n buttons for each table that is set via config parameter sTables.
     *
     * @return string
     */
    protected function GetExtendedListButtons()
    {
        $sHTML = '';
        $aTables = $this->GetAllowedTables();
        if (count($aTables) > 0) {
            $aTableDisplayNames = $this->GetTableDisplayNames();
            $sHTML .= '<div style="float:left;padding:2px 10px 0 0">'.ServiceLocator::get('translator')->trans('chameleon_system_core.field_lookup.select_item').': </div>';
            foreach ($aTables as $sTableName) {
                $oCmsTblConf = TdbCmsTblConf::GetNewInstance();
                $oCmsTblConf->LoadFromField('name', $sTableName);
                $sTableDisplayName = $oCmsTblConf->fieldTranslation;
                if (count($aTableDisplayNames) > 0 && array_key_exists($sTableName, $aTableDisplayNames)) {
                    $sTableDisplayName = $aTableDisplayNames[$sTableName];
                }
                /** @var SecurityHelperAccess $securityHelper */
                $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
                if (!$oCmsTblConf->fieldOnlyOneRecordTbl && $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_ACCESS, $sTableName)) {
                    $sHTML .= TCMSRender::DrawButton($sTableDisplayName, 'javascript:'.$this->_GetOpenWindowJS($oCmsTblConf).';', 'fas fa-th-list');
                    $sHTML .= '<input type="hidden" name="'.TGlobal::OutHTML('aTableNames['.$oCmsTblConf->id).']" id="'.TGlobal::OutHTML('aTableNames['.$oCmsTblConf->id).']" value="'.TGlobal::OutHTML($oCmsTblConf->fieldTranslation).'" />'."\n";
                }
            }
            $sHTML .= '<div>';
            $sHTML .= TCMSRender::DrawButton(ServiceLocator::get('translator')->trans('chameleon_system_core.action.reset'), "javascript:resetExtendedMultiTableListField('".TGlobal::OutJS($this->name)."','".TGlobal::OutJS($this->oDefinition->sqlData['field_default_value'])."','".TGlobal::OutHTML(ServiceLocator::get('translator')->trans('chameleon_system_core.field_lookup.nothing_selected'))."');", 'fas fa-undo');
            $sHTML .= '</div>';
        } else {
            $sHTML = parent::GetExtendedListButtons();
            $sTableName = $this->GetConnectedTableName();
            $oCmsTblConf = TdbCmsTblConf::GetNewInstance();
            $oCmsTblConf->LoadFromField('name', $sTableName);
            $sHTML .= '<input type="hidden" name="'.TGlobal::OutHTML('aTableNames['.$oCmsTblConf->id).']" id="'.TGlobal::OutHTML('aTableNames['.$oCmsTblConf->id).']" value="'.TGlobal::OutHTML($oCmsTblConf->fieldTranslation).'" />'."\n";
        }

        return $sHTML;
    }

    /**
     * generates the javascript for the extended list buttons.
     *
     * @param TCMSRecord $oPopupTableConf
     *
     * @return string
     */
    protected function _GetOpenWindowJS($oPopupTableConf)
    {
        $js = parent::_GetOpenWindowJS($oPopupTableConf);

        $aParams = [
            'pagedef' => 'extendedLookupList',
            'id' => $oPopupTableConf->id,
            'fieldName' => $this->name,
            'field' => $this->name,
            'sourceTblConfId' => $this->oDefinition->fieldCmsTblConfId,
        ];

        $sURL = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript($aParams);
        $sWindowTitle = ServiceLocator::get('translator')->trans('chameleon_system_core.form.select_box_nothing_selected');

        $js = "CreateModalIFrameDialogCloseButton('".TGlobal::OutHTML($sURL)."',0,0,'".$sWindowTitle."');return false;";

        return $js;
    }

    /**
     * returns a list of all configured / allowed tables set via field config parameter.
     *
     * @return array
     */
    public function GetAllowedTables()
    {
        $sTables = $this->oDefinition->GetFieldtypeConfigKey('sTables');
        if (!is_null($sTables)) {
            $aTables = explode(',', $sTables);
            foreach ($aTables as $sKey => $sTableName) {
                $aTables[$sKey] = trim($sTableName);
            }
            reset($aTables);
        } else {
            $aTables = [];
        }

        return $aTables;
    }

    /**
     * returns a list of all display names for the configured tables.
     *
     * @return array
     */
    protected function GetTableDisplayNames()
    {
        $sTableDisplayNames = $this->oDefinition->GetFieldtypeConfigKey('sTableDisplayNames');
        if (!is_null($sTableDisplayNames)) {
            $aTableDisplayNames = explode(',', $sTableDisplayNames);
            foreach ($aTableDisplayNames as $sKey => $sTableDisplayName) {
                $aTableDisplayNames[$sKey] = trim($sTableDisplayName);
            }
            $aTables = $this->GetAllowedTables();
            if (count($aTableDisplayNames) != count($aTables)) {
                $aTableDisplayNames = [];
            } else {
                $aTableMapping = [];
                foreach ($aTables as $sKey => $sTableName) {
                    $aTableMapping[$sTableName] = $aTableDisplayNames[$sKey];
                }
                $aTableDisplayNames = $aTableMapping;
                reset($aTableDisplayNames);
            }
        } else {
            $aTableDisplayNames = [];
        }

        return $aTableDisplayNames;
    }

    /**
     * returns the value of the field for the backend (Table Editor).
     *
     * @return string
     */
    public function _GetHTMLValue()
    {
        $sReturnValue = '';
        $sTableName = $this->GetConnectedTableName();
        if (!empty($this->data) && !empty($sTableName)) {
            $oRecord = call_user_func([TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $sTableName), 'GetNewInstance']);
            if ($oRecord->Load($this->data)) {
                $oCmsTblConf = TdbCmsTblConf::GetNewInstance();
                $oCmsTblConf->LoadFromField('name', $sTableName);
                $sReturnValue = $oCmsTblConf->fieldTranslation.' - '.$oRecord->GetDisplayValue();
            }
        } else {
            $sReturnValue = ServiceLocator::get('translator')->trans('chameleon_system_core.field_lookup.nothing_selected');
        }

        return $sReturnValue;
    }

    /**
     * @return string
     */
    public function RenderFieldMethodsString()
    {
        $aTables = $this->GetAllowedTables();
        $aMethodData = $this->GetFieldMethodBaseDataArray();
        $aMethodData['sMethodName'] = $this->GetFieldMethodName();
        $aMethodData['sReturnType'] = 'null|';
        $sTypes = '';
        $sTables = '';
        if (count($aTables) > 0) {
            foreach ($aTables as $sTableName) {
                $sTables .= $sTableName.',';
                $sTypes .= TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $sTableName).'|';
            }
            $sTypes = substr($sTypes, 0, -1);
            $sTables = substr($sTables, 0, -1);
            $aMethodData['sReturnType'] .= $sTypes;
        } else {
            $aMethodData['sReturnType'] = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $this->GetConnectedTableName());
        }

        $sCode = '';
        if (!empty($aMethodData['sReturnType'])) {
            $aMethodData['sClassName'] = $aMethodData['sReturnType'];
            $aMethodData['sClassSubType'] = 'CMSDataObjects';

            /** @var $oViewParser TViewParser */
            $oViewParser = new TViewParser();
            $oViewParser->bShowTemplatePathAsHTMLHint = false;
            $oViewParser->AddVarArray($aMethodData);
            $sMethodCode = $oViewParser->RenderObjectView('getobject', 'TCMSFields/TCMSFieldExtendedLookupMultiTable');
            $oViewParser->AddVar('sMethodCode', $sMethodCode);
            $sCode = $oViewParser->RenderObjectView('method', 'TCMSFields/TCMSField');

            if (!empty($sTypes)) {
                $sTypes = '('.$sTypes.') ';
            }
            if (!empty($sTypes)) {
                $sTables = '('.$sTables.') ';
            }
            $aParameters = ['sExpectedObject' => ['description' => 'can be a TdbClassName '.$sTypes.'or table name '.$sTables.'of the expected returned object ', 'default' => '', 'sType' => 'string']];
            $aMethodData['aParameters'] = $aParameters;
            $aMethodData['sOriginalMethodName'] = $aMethodData['sMethodName'];
            $aMethodData['sMethodName'] = $this->GetFieldMethodName().'ForObjectType';
            $aMethodData['sReturnType'] .= ' - returns null if the connected record is not type of the expected object given in $sExpectedObject';
            $oViewParser->AddVarArray($aMethodData);
            $sMethodCode = $oViewParser->RenderObjectView('getobjectforobjecttype', 'TCMSFields/TCMSFieldExtendedLookupMultiTable');
            $oViewParser->AddVar('sMethodCode', $sMethodCode);
            $sCode .= $oViewParser->RenderObjectView('method', 'TCMSFields/TCMSField');

            $aMethodData['aParameters'] = [];
            $aMethodData['aFieldData']['sFieldFullName'] = 'Zugehöriger Tabellenname für das Feld "'.$aMethodData['aFieldData']['sFieldFullName'].'" ('.MySqlLegacySupport::getInstance()->real_escape_string($this->name).')';
            $aMethodData['aMethodDescription'] = [];
            $aMethodData['sMethodName'] = $this->GetFieldMethodName().'ObjectType';
            $aMethodData['sReturnType'] = 'string - the Tdb classname for the object of the connected record';
            $oViewParser->AddVarArray($aMethodData);
            $sMethodCode = $oViewParser->RenderObjectView('getobjectType', 'TCMSFields/TCMSFieldExtendedLookupMultiTable');
            $oViewParser->AddVar('sMethodCode', $sMethodCode);
            $sCode .= $oViewParser->RenderObjectView('method', 'TCMSFields/TCMSField');
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
        $aTables = $this->GetAllowedTables();
        $sTableForMethodParameterDocumentation = '';
        if (count($aTables) > 0) {
            foreach ($aTables as $sTableName) {
                $query = "SELECT * FROM cms_tbl_conf where `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($sTableName)."'";
                $aTargetTable = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query));
                $sTableForMethodParameterDocumentation .= $aTargetTable['translation'].' ('.$aTargetTable['name'].') or ';
            }
            $sTableForMethodParameterDocumentation = substr($sTableForMethodParameterDocumentation, 0, -4);
        } else {
            $query = "SELECT * FROM cms_tbl_conf where `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->GetConnectedTableName())."'";
            $aTargetTable = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query));
            $sTableForMethodParameterDocumentation = $aTargetTable['translation'];
        }

        $aMethodData = $this->GetFieldMethodBaseDataArray();
        $sInputName = 'i'.ucfirst(TCMSTableToClass::ConvertToClassString($this->name));
        $aParameters = [$sInputName => ['description' => 'ID for the record in: '.$sTableForMethodParameterDocumentation, 'default' => '', 'sType' => 'int'], 'iLanguageId' => ['description' => 'set language id for list - if null, the default language will be used instead', 'default' => 'null', 'sType' => 'int']];
        $aMethodData['aParameters'] = $aParameters;

        $sMethodName = 'GetListFor'.TCMSTableToClass::ConvertToClassString($this->name);

        $aMethodData['sMethodName'] = $sMethodName;
        $aMethodData['sReturnType'] = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $this->sTableName).'List';

        $aMethodData['sClassName'] = $aMethodData['sReturnType'];
        $aMethodData['sClassSubType'] = 'CMSDataObjects';
        $aMethodData['sVisibility'] = 'static public';

        $aMethodData['iLookupFieldName'] = $sInputName;
        $aMethodData['sTableDatabaseName'] = $this->sTableName;

        $aMethodData['aFieldData']['sFieldFullName'] = 'Return all records belonging to the '.$sTableForMethodParameterDocumentation;

        $oViewParser = new TViewParser();
        /* @var $oViewParser TViewParser */
        $oViewParser->bShowTemplatePathAsHTMLHint = false;
        $oViewParser->AddVarArray($aMethodData);

        $sMethodCode = $oViewParser->RenderObjectView('listmethodcode', 'TCMSFields/TCMSFieldExtendedLookupMultiTable');
        $oViewParser->AddVar('sMethodCode', $sMethodCode);
        $sCode = $oViewParser->RenderObjectView('method', 'TCMSFields/TCMSField');

        return $sCode;
    }

    /**
     * changes an existing field definition (alter table)
     * changes the hidden field fieldName_table_name that stores the table name of the connected record.
     *
     * @param string $sOldName
     * @param string $sNewName
     * @param array|null $postData
     */
    public function ChangeFieldDefinition($sOldName, $sNewName, $postData = null)
    {
        parent::ChangeFieldDefinition($sOldName, $sNewName, $postData);

        $sQuery = 'ALTER TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`
                      CHANGE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->getTableFieldName($sOldName)).'`
                             `'.MySqlLegacySupport::getInstance()->real_escape_string($this->getTableFieldName($sNewName))."` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Zugehöriger Tabellenname für das Feld \"".MySqlLegacySupport::getInstance()->real_escape_string($sNewName)."\":'";
        MySqlLegacySupport::getInstance()->query($sQuery);
        $aQuery = [new LogChangeDataModel($sQuery)];

        TCMSLogChange::WriteTransaction($aQuery);
    }

    /**
     * called on each field after the record is saved (NOT on insert, only on save)
     * saves the hidden field fieldName_table_name with the table name of the connected record.
     *
     * @param string $iRecordId - the id of the record
     */
    public function PostSaveHook($iRecordId)
    {
        $sTableName = '';
        $oCmsTblConf = TdbCmsTblConf::GetNewInstance();
        // may hold id or name
        if ($oCmsTblConf->Load($this->oTableRow->sqlData[$this->getTableFieldName()])) {
            $sTableName = $oCmsTblConf->fieldName;
        } else {
            $sTableName = $this->oTableRow->sqlData[$this->getTableFieldName()];
        }

        if (!empty($sTableName)) {
            $sQuery = 'UPDATE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`
                      SET `'.MySqlLegacySupport::getInstance()->real_escape_string($this->getTableFieldName())."` = '".MySqlLegacySupport::getInstance()->real_escape_string($sTableName)."'
                    WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($iRecordId)."'
                 ";
            MySqlLegacySupport::getInstance()->query($sQuery);

            $editLanguageIsoCode = $this->getBackendSession()->getCurrentEditLanguageIso6391();
            $migrationQueryData = new MigrationQueryData($this->sTableName, $editLanguageIsoCode);
            $migrationQueryData
                ->setFields([
                    $this->getTableFieldName() => $sTableName,
                ])
                ->setWhereEquals([
                    'id' => $this->oTableRow->id,
                ])
            ;
            $aQuery = [new LogChangeDataModel($migrationQueryData, LogChangeDataModel::TYPE_UPDATE)];

            TCMSLogChange::WriteTransaction($aQuery);
        }
    }

    /**
     * drop a field definition (alter table).
     * drops the hidden field fieldName_table_name that stores the table name of the connected record.
     */
    public function DeleteFieldDefinition()
    {
        parent::DeleteFieldDefinition();

        $this->RemoveFieldIndex();
        $query = 'ALTER TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`
                        DROP `'.MySqlLegacySupport::getInstance()->real_escape_string($this->getTableFieldName()).'` ';

        MySqlLegacySupport::getInstance()->query($query);
        $aQuery = [new LogChangeDataModel($query)];
        TCMSLogChange::WriteTransaction($aQuery);
    }

    /**
     * called on the OLD field if the field type is changed (before deleting related tables or droping the index)
     * drops the hidden field fieldName_table_name that stores the table name of the connected record.
     */
    public function ChangeFieldTypePreHook()
    {
        // get the table name and clear the records that doesn't match the new target table
        $sTableName = $this->GetConnectedTableName();
        $sQuery = 'UPDATE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`
                    SET `'.MySqlLegacySupport::getInstance()->real_escape_string($this->name)."` = ''
                  WHERE `".MySqlLegacySupport::getInstance()->real_escape_string($this->getTableFieldName())."` != '".MySqlLegacySupport::getInstance()->real_escape_string($sTableName)."'";
        MySqlLegacySupport::getInstance()->query($sQuery);

        $editLanguageIsoCode = $this->getBackendSession()->getCurrentEditLanguageIso6391();
        $migrationQueryData = new MigrationQueryData($this->sTableName, $editLanguageIsoCode);
        $migrationQueryData
            ->setFields([$this->name => ''])
            ->setWhereExpressions([new Comparison($this->getTableFieldName().'', Comparison::NEQ, $sTableName)]);

        TCMSLogChange::WriteTransaction([new LogChangeDataModel($migrationQueryData, LogChangeDataModel::TYPE_UPDATE)]);

        $sQuery = 'ALTER TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`
                        DROP `'.MySqlLegacySupport::getInstance()->real_escape_string($this->getTableFieldName().'').'` ';

        MySqlLegacySupport::getInstance()->query($sQuery);
        $aQuery = [new LogChangeDataModel($sQuery)];
        TCMSLogChange::WriteTransaction($aQuery);
    }

    /**
     * called on the NEW field if the field type is changed (BEFORE anything else is done)
     * creates the hidden field fieldName_table_name that stores the table name of the connected record.
     */
    public function ChangeFieldTypePostHook()
    {
        $sQuery = 'ALTER TABLE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName).'`
                         ADD `'.MySqlLegacySupport::getInstance()->real_escape_string($this->getTableFieldName())."` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Zugehöriger Tabellenname für das Feld \"".MySqlLegacySupport::getInstance()->real_escape_string($this->name)."\":'";

        MySqlLegacySupport::getInstance()->query($sQuery);
        $aQuery = [new LogChangeDataModel($sQuery)];
        TCMSLogChange::WriteTransaction($aQuery);
    }

    /**
     * drops the field index.
     */
    public function RemoveFieldIndex()
    {
        parent::RemoveFieldIndex();

        $this->dropIndexByName($this->name.'_combined');
    }

    /**
     * sets field index if the field type is indexable.
     *
     * @param bool $returnDDL - if true the SQL alter statement will be returned
     *
     * @return string
     */
    public function CreateFieldIndex($returnDDL = false)
    {
        $queryDump = parent::CreateFieldIndex($returnDDL);

        $indexFields = [$this->getTableFieldName(), $this->name];

        if (true === $returnDDL) {
            $queryDump .= $this->getIndexQuery($this->name.'_combined', 'INDEX', $indexFields).";\n";

            return $queryDump;
        }

        $this->createIndex($this->name.'_combined', 'INDEX', $indexFields);

        return $queryDump;
    }
}

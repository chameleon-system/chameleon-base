<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Interfaces\FlashMessageServiceInterface;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use ChameleonSystem\DatabaseMigration\DataModel\LogChangeDataModel;
use Doctrine\DBAL\Connection;
use Symfony\Contracts\Translation\TranslatorInterface;

class TCMSField implements TCMSFieldVisitableInterface
{
    /**
     * view path for frontend.
     */
    protected $sViewPath = 'TCMSFields/views';

    /**
     * value of the field.
     *
     * @var array|string
     */
    public $data;

    /**
     * the field name.
     *
     * @var string
     */
    public $name;

    /**
     * the table name.
     *
     * @var string
     */
    public $sTableName;

    /**
     * record id.
     *
     * @var string
     */
    public $recordId;

    /**
     * length of the field (maxlength).
     *
     * @var int
     */
    public $fieldWidth;

    /**
     * the width in pixels of the field for CSS styling.
     *
     * @var int
     */
    public $fieldCSSwidth;

    /**
     * indicates that this field has no title column
     * and uses colcount=2 in field edit list.
     */
    public $completeRow = false;

    /**
     * pointer to the Table row.
     *
     * @var TCMSRecord
     */
    public $oTableRow;

    /**
     * the definition class of the field.
     *
     * @var TCMSFieldDefinition
     */
    public $oDefinition;

    /**
     * indicates that this is a MLT field.
     *
     * @var bool
     */
    public $isMLTField = false;

    /**
     * indicates that this is a Property field.
     *
     * @var bool
     */
    public $isPropertyField = false;

    /**
     * indicates if the button for switching to the connected record shows up.
     *
     * @var bool default true
     */
    protected $bShowSwitchToRecord = true;

    /**
     * array of methods that are allowed to be called via URL (ajax call)
     * /cms?pagedef=tableeditor&id=30&tableid=239&sRestriction=28&sRestrictionField=module_matrix_id&_rmhist=false&module_fnc[contentmodule]=ExecuteAjaxCall&_fnc=Test&_fieldName=cms_tpl_module_instance_id.
     *
     * @var array
     */
    protected $methodCallAllowed = array();

    /**
     * the current record data (includes workflow data).
     *
     * @var TCMSRecord
     */
    protected $oRecordFromDB;

    /**
     * is set if ReadOnly method is called.
     *
     * @var bool
     */
    protected $bReadOnlyMode = false;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $databaseConnection;

    /**
     * is set if the contained data are encrypted.
     *
     * @var bool
     */
    protected $bEncryptedData = false;

    /**
     * Sets methods that are allowed to be called via URL (ajax calls).
     */
    protected function DefineInterface()
    {
    }

    /**
     * Checks if method is listed in $this->methodCallAllowed array.
     *
     * @param string $sMethodName
     *
     * @return bool
     */
    public function isMethodCallAllowed($sMethodName)
    {
        $this->DefineInterface();
        $returnVal = false;
        if (in_array($sMethodName, $this->methodCallAllowed)) {
            $returnVal = true;
        }

        return $returnVal;
    }

    /**
     * Renders an input field of type "hidden", used in readonly mode.
     *
     * @return string
     */
    protected function _GetHiddenField()
    {
        return sprintf('<input type="hidden" name="%1$s" id="%1$s" value="%2$s" />'."\n",
            TGlobal::OutHTML($this->name),
            TGlobal::OutHTML($this->data)
        );
    }

    /**
     * Renders the read only view of the field.
     *
     * @return string
     */
    public function GetReadOnly()
    {
        $this->bReadOnlyMode = true;

        $html = $this->_GetHiddenField();
        $html .= '<div class="input-group input-group-sm">';
        $html .= '<div class="form-control form-control-sm" readonly>'.TGlobal::OutHTML($this->data).'</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Returns the modifier (none, hidden, readonly) of the field.
     * If the field is restricted, and the modifier is none, then we return readonly instead.
     *
     * @return string
     */
    public function GetDisplayType()
    {
        $modifier = $this->oDefinition->sqlData['modifier'];
        if ('hidden' !== $modifier && '1' == $this->oDefinition->sqlData['restrict_to_groups']) {
            // check if the user is in one of the connected groups
            $global = $this->getGlobal();
            $fieldGroups = $this->oDefinition->GetPermissionGroups();
            if ($global->oUser
                && $global->oUser->oAccessManager
                && $global->oUser->oAccessManager->user
                && !$global->oUser->oAccessManager->user->IsInGroups($fieldGroups)
            ) {
                $modifier = 'readonly';
            }
        }

        return $modifier;
    }

    /**
     * renders the html of the field (overwrite this to write your own field type).
     *
     * @return string
     */
    public function GetHTML()
    {
        $this->_GetFieldWidth();

        $html = $this->_GetHiddenField();
        $html .= 'FIELD TYPE NOT DEFINED';

        return $html;
    }

    /**
     * renders the html for the edit-on-click mode that opens a window with a field editor or
     * renders the html for editing mode after klick on the edit button.
     *
     * @return string
     */
    public function GetEditOnClick()
    {
        if ($this->IsEditOnClickOnEditMode()) {
            $sHtml = $this->GetHTML();
        } else {
            $sHtml = $this->GetEditOnClickHMTL();
        }

        return $sHtml;
    }

    /**
     * renders the html for the edit-on-click mode that opens a window with a field editor.
     *
     * @return string
     */
    protected function GetEditOnClickHMTL()
    {
        $urlUtil = $this->getUrlUtil();
        $translator = $this->getTranslator();

        $tableConf = &$this->oTableRow->GetTableConf();
        $this->_GetFieldWidth();
        $url = PATH_CMS_CONTROLLER.'?'.$urlUtil->getArrayAsUrl(array(
            'id' => $this->recordId,
            'tableid' => $tableConf->id,
            'pagedef' => 'tableeditorfield',
            '_fnc' => 'editfield',
            '_fieldName' => $this->name,
        ));
        $openWindow = sprintf("CreateModalIFrameDialog('%s',%s,%s,'%s');",
            $url,
            $this->GetEditOnClickDialogWidth(),
            $this->GetEditOnClickDialogHeight(),
            $translator->trans('chameleon_system_core.link.edit')
        );
        $edititem = TCMSRender::DrawButton(
            $translator->trans('chameleon_system_core.link.edit'),
            "javascript:{$openWindow}",
            'far fa-edit'
        );
        $edititem .= '<div class="cleardiv" style="margin-bottom: 10px;">&nbsp;</div>';

        return sprintf('<div id="fieldcontainer_%1$s"><div style="width:%2$s;position:relative;">%3$s<div id=\"%1$s_contentdiv\">%4$s</div></div></div>',
            TGlobal::OutHTML($this->name),
            $this->fieldCSSwidth,
            $edititem,
            $this->GetReadOnly()
        );
    }

    /**
     * Check if field is in edit-on-click mode.
     *
     * @return bool
     */
    protected function IsEditOnClickOnEditMode()
    {
        $inputFilterUtil = $this->getInputFilterUtil();

        $functionName = $inputFilterUtil->getFilteredInput('_fnc');
        $functionFieldName = $inputFilterUtil->getFilteredInput('_fieldName');

        return 'editfield' === $functionName && $functionFieldName === $this->name;
    }

    /**
     * sets the inner width of the dialog window in editOnClick mode.
     *
     * @return string expression that will evaluate to a number when injected into JavaScript
     */
    protected function GetEditOnClickDialogWidth()
    {
        return '(window.innerWidth-40)';
    }

    /**
     * sets the inner height of the dialog window in editOnClick mode.
     *
     * @return string expression that will evaluate to a number when injected into JavaScript
     */
    protected function GetEditOnClickDialogHeight()
    {
        return '(window.innerHeight-100)';
    }

    /**
     * returns an array of all js, css, or other header includes that are required
     * in the cms for this field. each include should be in one line, and they
     * should always be typed the same way so that no includes are included more than once.
     *
     * @return array
     */
    public function GetCMSHtmlHeadIncludes()
    {
        return array();
    }

    /**
     * returns an array of all js includes and html snippets includes that are required
     * in the cms for this field. each include should be in one line, and they
     * should always be typed the same way so that no includes are included more than once.
     *
     * @return array
     */
    public function GetCMSHtmlFooterIncludes()
    {
        return array();
    }

    /**
     * return the fields value for HTML output.
     *
     * @return string
     */
    public function _GetHTMLValue()
    {
        return $this->_GetFieldValue();
    }

    /**
     * returns the field value for database storage
     * overwrite this method to modify data before save.
     *
     * @return mixed
     */
    public function GetSQL()
    {
        return $this->ConvertDataToFieldBasedData($this->ConvertPostDataToSQL());
    }

    /**
     * returns the field value for database storage
     * overwrite this method to modify data on save.
     *
     * @return mixed
     */
    public function GetSQLOnCopy()
    {
        return $this->data;
    }

    /**
     * @param string $sData
     *
     * @return string
     */
    public function ConvertDataToFieldBasedData($sData)
    {
        return $sData;
    }

    /**
     * returns the field value for database storage on database copy state
     * use this method to handle copy without post data
     * overwrite this method to modify data before save.
     *
     * @return mixed
     */
    public function GetDatabaseCopySQL()
    {
        return $this->GetSQL();
    }

    /**
     * this method converts post data like datetime (3 fields with date, hours, minutes in human readable format)
     * to sql format.
     *
     * @return mixed
     */
    public function ConvertPostDataToSQL()
    {
        return $this->data;
    }

    /**
     * gets called in TCMSTableEditor::_WriteDataToDatabase before GetSQL
     * the field will only be written to the database if this function returned true.
     *
     *
     * @return bool
     */
    public function PreGetSQLHook()
    {
        return true;
    }

    /**
     * called on each field after the record is saved (NOT on insert, only on save).
     *
     * @param string $iRecordId - the id of the record
     *
     * @return void
     */
    public function PostSaveHook($iRecordId)
    {
    }

    /**
     * called on each field when a record is inserted.
     *
     * @param string $iRecordId
     *
     * @return void
     */
    public function PostInsertHook($iRecordId)
    {
    }

    /**
     * changes an existing field definition (alter table).
     *
     * @param string     $oldName
     * @param string     $newName
     * @param array|null $postData
     *
     * @return void
     */
    public function ChangeFieldDefinition($oldName, $newName, &$postData = null)
    {
        if (true === $this->oDefinition->isVirtualField()) {
            return;
        }

        $connection = $this->getDatabaseConnection();
        $inputFilterUtil = $this->getInputFilterUtil();

        $comment = '';
        if (is_array($postData)) {
            $comment = $postData['translation'].': ';
            if (array_key_exists('049_helptext', $postData)) {
                $comment .= $postData['049_helptext'];
            }
            $comment = substr($comment, 0, 255);
        }
        $quotedTableName = $connection->quoteIdentifier($this->sTableName);
        $quotedOldName = $connection->quoteIdentifier($oldName);
        $quotedNewName = $connection->quoteIdentifier($newName);
        $quotedComment = $connection->quote($comment);

        $query = 'ALTER TABLE '.$quotedTableName.'
                     CHANGE '.$quotedOldName.'
                            '.$quotedNewName.' '.$this->_GetSQLDefinition($postData).
            ' COMMENT '.$quotedComment;

        $connection->query($query);

        if (null !== $postData) {
            $field_default_value = '';
            $updateExistingRecords = false;
            if (isset($postData['field_default_value'])) {
                $field_default_value = $postData['field_default_value'];
            }
            if (isset($postData['UpdateRecordsWithOldDefaultValue'])) {
                $updateExistingRecords = (bool) $postData['UpdateRecordsWithOldDefaultValue'];
            }
        } else {
            $updateExistingRecords = (bool) $inputFilterUtil->getFilteredInput('UpdateRecordsWithOldDefaultValue');
            $field_default_value = $inputFilterUtil->getFilteredInput('field_default_value');
        }

        if ('' !== $field_default_value) {
            $this->UpdateFieldDefaultValue($field_default_value, $newName, $updateExistingRecords);
        }

        $transaction = array(new LogChangeDataModel($query));
        TCMSLogChange::WriteTransaction($transaction);
    }

    /**
     * update default value of the field.
     *
     * @param string $fieldDefaultValue
     * @param string $fieldName
     * @param bool   $updateExistingRecords
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function UpdateFieldDefaultValue($fieldDefaultValue, $fieldName, $updateExistingRecords = false)
    {
        if (true === $this->oDefinition->isVirtualField()) {
            return;
        }

        $connection = $this->getDatabaseConnection();

        $quotedTableName = $connection->quoteIdentifier($this->sTableName);
        $quotedFieldName = $connection->quoteIdentifier($fieldName);
        $quotedFieldDefaultValue = $connection->quote($fieldDefaultValue);

        $updateQuery = "UPDATE $quotedTableName
                        SET $quotedFieldName = $quotedFieldDefaultValue
                        WHERE $quotedFieldName = ''";
        if ($updateExistingRecords) {
            $quotedFieldDefaultValueFromSqlData = $connection->quote($this->oDefinition->sqlData['field_default_value']);
            $updateQuery .= " OR $quotedFieldName = $quotedFieldDefaultValueFromSqlData";
        }

        $connection->query($updateQuery);
    }

    /**
     * update default value of a field with associated workflow.
     *
     * @param string $fieldDefaultValue
     * @param string $fieldName
     * @param bool   $updateExistingRecords
     */
    protected function UpdateWorkflowFieldDefaultValue($fieldDefaultValue, $fieldName, $updateExistingRecords = false)
    {
        $connection = $this->getDatabaseConnection();

        $query = 'SELECT `'.$connection->quoteIdentifier($this->sTableName).'`.*
                   FROM `'.$connection->quoteIdentifier($this->sTableName).'`
                  WHERE `'.$connection->quoteIdentifier($fieldName)."` = ''";
        if ($updateExistingRecords) {
            $query .= ' OR `'.$connection->quoteIdentifier($fieldName)."` = '".$connection->quote($this->oDefinition->sqlData['field_default_value'])."'";
        }

        $stmt = $connection->query($query);

        while ($row = $stmt->fetch()) {
            $oEditor = TTools::GetTableEditorManager($this->sTableName, $row['id']);
            $oEditor->AllowEditByAll(true);
            $oEditor->SaveField($fieldName, $fieldDefaultValue);
        }
    }

    /**
     * create a new field definition (alter table).
     *
     * @param bool      $returnDDL
     * @param TCMSField $oField
     *
     * @return string
     */
    public function CreateFieldDefinition($returnDDL = false, $oField = null)
    {
        if (true === $this->oDefinition->isVirtualField()) {
            return '';
        }

        $connection = $this->getDatabaseConnection();

        $fieldData = null;
        if (null !== $oField) {
            $fieldData = $oField->oDefinition->sqlData;
        }

        $sql = $this->CreateRelatedTables($returnDDL);

        // the default action is to create a plain varchar field
        $quotedTableName = $connection->quoteIdentifier($this->sTableName);
        $quotedName = $connection->quoteIdentifier($this->name);
        $query = 'ALTER TABLE '.$quotedTableName.'
                        ADD '.$quotedName.' '.$this->_GetSQLDefinition($fieldData);
        if (!$returnDDL) {
            $aQuery = array(new LogChangeDataModel($query));
            TCMSLogChange::WriteTransaction($aQuery);
            $connection->query($query);
        }
        $sql .= $query.";\n";

        if ($returnDDL) {
            return $sql;
        }

        return '';
    }

    /**
     * called on the OLD field if the field type is changed (before deleting related tables or droping the index).
     */
    public function ChangeFieldTypePreHook()
    {
    }

    /**
     * called on the NEW field if the field type is changed (BEFORE anything else is done).
     */
    public function ChangeFieldTypePostHook()
    {
    }

    /**
     * drops the field index.
     */
    public function RemoveFieldIndex()
    {
        $fieldType = $this->oDefinition->GetFieldType();

        if ('index' !== $fieldType->sqlData['indextype'] && 'unique' !== $fieldType->sqlData['indextype']) {
            return;
        }

        $this->dropIndexByName($this->name);
    }

    protected function dropIndexByName(string $indexName): bool
    {
        $connection = $this->getDatabaseConnection();

        $quotedTableName = $connection->quoteIdentifier($this->sTableName);

        if (false === $this->indexExists($indexName)) {
            return false;
        }

        $dropIndexQuery = 'ALTER TABLE '.$quotedTableName.' DROP INDEX '.$connection->quoteIdentifier($indexName);
        $connection->query($dropIndexQuery);

        $transaction = array(new LogChangeDataModel($dropIndexQuery));
        TCMSLogChange::WriteTransaction($transaction);

        return true;
    }

    protected function indexExists(string $indexName): bool
    {
        $connection = $this->getDatabaseConnection();
        $quotedTableName = $connection->quoteIdentifier($this->sTableName);

        $indexExistsQuery = 'SHOW INDEX FROM '.$quotedTableName.' WHERE KEY_NAME = '.$connection->quote($indexName);
        $indexExistsResult = $connection->query($indexExistsQuery);

        return 0 !== $indexExistsResult->rowCount();
    }

    /**
     * sets field index if the field type is indexable.
     *
     * @param bool $returnDDL - if true the SQL alter statement will be returned
     *
     * @return string|null
     */
    public function CreateFieldIndex($returnDDL = false)
    {
        $inputFilterUtil = $this->getInputFilterUtil();

        $oFieldType = null;
        $cmsFieldTypeId = $inputFilterUtil->getFilteredInput('cms_field_type_id');

        if (empty($cmsFieldTypeId)) {
            $oFieldType = $this->oDefinition->GetFieldType();
        } else {
            $oFieldType = new TCMSRecord('cms_field_type', $cmsFieldTypeId);
        }

        $indexType = strtoupper($oFieldType->sqlData['indextype']);

        if ('INDEX' !== $indexType && 'UNIQUE' !== $indexType) {
            return $returnDDL ? '' : null;
        }

        if (true === $returnDDL) {
            $query = $this->getIndexQuery($this->name, $indexType);

            return $query.";\n";
        } else {
            $this->createIndex($this->name, $indexType);
        }

        return null;
    }

    protected function getIndexQuery(string $indexName, string $indexType = 'INDEX', array $fields = []): string
    {
        $connection = $this->getDatabaseConnection();
        $quotedTableName = $connection->quoteIdentifier($this->sTableName);
        $quotedIndexName = $connection->quoteIdentifier($indexName);

        $quotedFields = $quotedIndexName;

        if (0 !== count($fields)) {
            $quotedFields = implode(',',array_map(array($connection,'quoteIdentifier'), $fields));
        }

        return 'ALTER TABLE '.$quotedTableName.' ADD '.$indexType.' '.$quotedIndexName.' ('.$quotedFields.')';
    }

    protected function createIndex(string $indexName, string $indexType = 'INDEX', array $fields = [])
    {
        $connection = $this->getDatabaseConnection();

        if (true === $this->indexExists($indexName)) {
            return;
        }

        $query = $this->getIndexQuery($indexName, $indexType, $fields);

        $connection->query($query);
        $transaction = array(new LogChangeDataModel($query));
        TCMSLogChange::WriteTransaction($transaction);
    }

    /**
     * generate the field definition part of the sql statement
     * we assume that $oFieldDefinition holds the correct default value.
     *
     * @param array $fieldDefinition
     *
     * @return string
     */
    public function _GetSQLDefinition(&$fieldDefinition = null)
    {
        $connection = $this->getDatabaseConnection();
        $inputFilterUtil = $this->getInputFilterUtil();

        $lengthSet = '';
        $cmsFieldTypeId = '';
        $fieldDefaultValue = '';

        if (null !== $fieldDefinition) {
            if (isset($fieldDefinition['field_default_value'])) {
                $fieldDefaultValue = $fieldDefinition['field_default_value'];
            }
            $cmsFieldTypeId = $fieldDefinition['cms_field_type_id'];
            if (!empty($fieldDefinition['length_set'])) {
                $lengthSet = $fieldDefinition['length_set'];
            }
        } else {
            $fieldDefaultValue = $inputFilterUtil->getFilteredInput('field_default_value');
            $cmsFieldTypeId = $inputFilterUtil->getFilteredInput('cms_field_type_id');
            $lengthSet = $inputFilterUtil->getFilteredInput('length_set');
        }

        $fieldType = null;
        if (!empty($cmsFieldTypeId)) {
            $fieldType = new TCMSRecord('cms_field_type', $cmsFieldTypeId);
        } else {
            $fieldType = $this->oDefinition->GetFieldType();
        }

        $sqlDefinition = $fieldType->sqlData['mysql_type'];
        if (!empty($lengthSet)) {
            $sqlDefinition .= "({$lengthSet})";
        } else {
            $sqlDefinition .= $this->GetMySQLLengthSet($fieldType);
        }
        $sqlDefinition .= $this->_GetSQLCharset();
        if (!empty($fieldDefaultValue) || '0' === $fieldDefaultValue) {
            $sqlDefinition .= ' DEFAULT '.$connection->quote($fieldDefaultValue);
        } elseif (!empty($fieldType->sqlData['mysql_standard_value'])) {
            $sqlDefinition .= ' DEFAULT '.$connection->quote($fieldType->sqlData['mysql_standard_value']);
        }

        $sqlDefinition .= ' NOT NULL';
        if ('1' == $fieldType->sqlData['force_auto_increment']) {
            $sqlDefinition .= ' AUTO_INCREMENT';
        }

        return $sqlDefinition;
    }

    /**
     * tries to fetch the length_set value of the field type.
     *
     * @param TCMSRecord $oFieldType
     * @param array|null $aPostData
     *
     * @return string
     */
    protected function GetMySQLLengthSet($oFieldType, &$aPostData = null)
    {
        $lengthSet = '';
        if (!empty($oFieldType->sqlData['length_set'])) {
            $lengthSet = "({$oFieldType->sqlData['length_set']})";
        }

        return $lengthSet;
    }

    /**
     * by default we return an empty string - for each field that contains an Id
     * that is Char(36) we return the new charset latin1 so that we get more memory
     * size for a record.
     *
     * @return string
     */
    public function _GetSQLCharset()
    {
        return '';
    }

    /**
     * drop a field definition (alter table).
     */
    public function DeleteFieldDefinition()
    {
        if (true === $this->oDefinition->isVirtualField()) {
            return;
        }

        $connection = $this->getDatabaseConnection();

        $this->RemoveFieldIndex();
        $query = 'ALTER TABLE '.$connection->quoteIdentifier($this->sTableName).'
                       DROP '.$connection->quoteIdentifier($this->name).' ';

        $connection->query($query);
        $transaction = array(new LogChangeDataModel($query));
        TCMSLogChange::WriteTransaction($transaction);
    }

    /**
     * removes any related tables (like mlt tables). the function will be called
     * from the outside whenever there is a change of type FROM this type of field.
     */
    public function DeleteRelatedTables()
    {
    }

    /**
     * Checks if is allowed to create related tables for the field after the field was saved.
     * Overwrite this for your field if your field has own CreateRelatedTables function.
     *
     * @param array $aOldFieldData    contains old field data
     * @param array $aOldFieldTypeRow contains old field definition
     * @param array $aNewFieldTypeRow contains new field definition
     *
     * @return bool
     */
    public function AllowCreateRelatedTablesAfterFieldSave($aOldFieldData, $aOldFieldTypeRow, $aNewFieldTypeRow)
    {
        return true;
    }

    /**
     * Checks if is allowed to delete related tables for the field before the field will be saved.
     * Overwrite this for your field if your field has own CreateRelatedTables function.
     *
     * @param array $aNewFieldData    contains old field data
     * @param array $aOldFieldTypeRow contains old field definition
     * @param array $aNewFieldTypeRow contains new field definition
     *
     * @return bool
     */
    public function AllowDeleteRelatedTablesBeforeFieldSave($aNewFieldData, $aOldFieldTypeRow, $aNewFieldTypeRow)
    {
        return true;
    }

    /**
     * Checks if is allowed to rename related tables for the field before the field will be saved.
     * Overwrite this for your field if your field has own CreateRelatedTables function.
     *
     * @param array $aNewFieldData    contains old field data
     * @param array $aOldFieldTypeRow contains old field definition
     * @param array $aNewFieldTypeRow contains new field definition
     *
     * @return bool
     */
    public function AllowRenameRelatedTablesBeforeFieldSave($aNewFieldData, $aOldFieldTypeRow, $aNewFieldTypeRow)
    {
        return true;
    }

    /**
     * Renames existing related table.
     *
     * @param array $newFieldData
     * @param bool  $returnDDL
     *
     * @return string|null
     */
    public function RenameRelatedTables($newFieldData, $returnDDL = false)
    {
        if ($returnDDL) {
            return '';
        }

        return null;
    }

    /**
     * creates any related tables (like mlt tables). the function will be called
     * from the outside whenever there is a change of type TO this type of field.
     *
     * @param bool $returnDDL
     *
     * @return string|null
     */
    public function CreateRelatedTables($returnDDL = false)
    {
        if ($returnDDL) {
            return '';
        }

        return null;
    }

    /**
     * returns the value of the field
     * if the value is empty it returns the default value.
     *
     * @return string
     */
    public function _GetFieldValue()
    {
        $value = '';
        if (false !== $this->data && null !== $this->data) {
            $value = $this->data;
        }
        if (empty($this->data) && '0' != $this->data) {
            // check for default value
            if (!empty($this->oDefinition->sqlData['field_default_value'])) {
                $value = $this->oDefinition->sqlData['field_default_value'];
            }
        } else {
            $value = $this->data;
        }

        return $value;
    }

    /**
     * returns the length of a field
     * sets field max-width and field CSS width.
     *
     * @return int
     */
    public function _GetFieldWidth()
    {
        // max length
        if ('0' != $this->oDefinition->sqlData['field_width']) {
            if ($this->oDefinition->sqlData['field_width'] > 60) {
                $this->fieldCSSwidth = '100%';
            } else {
                $this->fieldCSSwidth = ($this->oDefinition->sqlData['field_width'] * 6).'px';
            }
            $this->fieldWidth = $this->oDefinition->sqlData['field_width'];
        } else { // the real length of the field
            $lengthSet = $this->oDefinition->sqlData['length_set'];
            if (empty($lengthSet) || false === is_numeric($lengthSet)) {
                $this->fieldWidth = 255;
            } else {
                $this->fieldWidth = $lengthSet;
            }
            $this->fieldCSSwidth = '100%';
        }

        return $this->fieldWidth;
    }

    public function GetHTMLExport()
    {
        return $this->data;
    }

    /**
     * returns the field content in RTF format (experimental).
     *
     * @return string
     */
    public function GetRTFExport()
    {
        return $this->GetHTMLExport();
    }

    public function RenderFieldPropertyString()
    {
        $viewParser = new TViewParser();
        $viewParser->bShowTemplatePathAsHTMLHint = false;
        $data = $this->GetFieldWriterData();
        $viewParser->AddVarArray($data);

        return $viewParser->RenderObjectView('property', 'TCMSFields/TCMSField');
    }

    public function RenderFieldPostLoadString()
    {
        $viewParser = new TViewParser();
        $viewParser->bShowTemplatePathAsHTMLHint = false;
        $data = $this->GetFieldWriterData();
        $viewParser->AddVarArray($data);

        return $viewParser->RenderObjectView('postload', 'TCMSFields/TCMSField');
    }

    /**
     * injected into the PostWakeupHook in the auto class.
     *
     * @return string
     */
    public function RenderFieldPostWakeupString()
    {
        return '';
    }

    public function RenderFieldMethodsString()
    {
        return '';
    }

    /**
     * render any methods for the auto list class for this field.
     *
     * @return string
     */
    public function RenderFieldListMethodsString()
    {
        return '';
    }

    /**
     * @return array
     */
    protected function GetFieldWriterData()
    {
        $fieldNotesDesc = array();
        $fieldNotes = trim($this->oDefinition->sqlData['049_helptext']);
        if (!empty($fieldNotes)) {
            $fieldNotes = wordwrap($fieldNotes, 80);
            $fieldNotesDesc = explode("\n", $fieldNotes);
        }

        $aData = array(
            'sFieldFullName' => $this->oDefinition->sqlData['translation'],
            'aFieldDesc' => $fieldNotesDesc,
            'sFieldType' => 'string',
            'sFieldVisibility' => 'public',
            'sFieldName' => TCMSTableToClass::PREFIX_PROPERTY.TCMSTableToClass::ConvertToClassString($this->name),
            'sFieldDefaultValue' => 'null',
            'sFieldDatabaseName' => $this->name,
            'oDefinition' => $this->oDefinition,
            'sTableName' => $this->sTableName,
            'databaseConnection' => $this->getDatabaseConnection(),
        );

        return $aData;
    }

    /**
     * @param string $type
     * @param string $default
     * @param string $description
     *
     * @return array
     */
    public static function GetMethodParameterArray($type, $default, $description)
    {
        return array(
            'sType' => $type,
            'description' => $description,
            'default' => $default,
        );
    }

    /**
     * @param string $sMethodPostString
     *
     * @return string
     */
    protected function GetFieldMethodName($sMethodPostString = '')
    {
        $sPrefix = TCMSTableToClass::PREFIX_PROPERTY;
        $sPrefix = ucfirst($sPrefix);

        return 'Get'.$sPrefix.TCMSTableToClass::ConvertToClassString($this->name).$sMethodPostString;
    }

    /**
     * @return array
     */
    protected function GetFieldMethodBaseDataArray()
    {
        $aFieldData = $this->GetFieldWriterData();
        $aMethodData = array(
            'aMethodDescription' => $aFieldData['aFieldDesc'],
            'aParameters' => array(),
            'sReturnType' => '',
            'sVisibility' => 'public',
            'sMethodName' => '',
            'sMethodCode' => '',
            'aFieldData' => $aFieldData,
        );

        return $aMethodData;
    }

    /**
     * generates the url may be used to call a method in TCMSFields class via ajax.
     *
     * @param array|null $aParams - array of additional parameters to attach to the url
     *
     * @return string
     */
    protected function GenerateAjaxURL($aParams = null)
    {
        $inputFilterUtil = $this->getInputFilterUtil();
        $urlUtil = $this->getUrlUtil();
        $oTableConf = &$this->oTableRow->GetTableConf();

        if (!is_array($aParams)) {
            $aParams = array();
        }

        $aParams['pagedef'] = 'tableeditor';
        $aParams['id'] = $this->recordId;
        $aParams['tableid'] = $oTableConf->id;
        $aParams['sRestriction'] = $inputFilterUtil->getFilteredInput('sRestriction');
        $aParams['sRestrictionField'] = $inputFilterUtil->getFilteredInput('sRestrictionField');
        $aParams['_rmhist'] = 'false';
        $aParams['module_fnc'] = array('contentmodule' => 'ExecuteAjaxCall');
        $aParams['callFieldMethod'] = '1';

        return PATH_CMS_CONTROLLER.'?'.$urlUtil->getArrayAsUrl($aParams, '', '&');
    }

    /**
     * checks if field is mandatory and if field content is valid
     * overwrite this method to add your field based validation
     * you need to add a message to TCMSMessageManager for handling error messages
     * <code>
     * <?php
     *   $oMessageManager = TCMSMessageManager::GetInstance();
     *   $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
     *   $oMessageManager->AddMessage($sConsumerName,'TABLEEDITOR_FIELD_IS_MANDATORY');
     * ?>
     * </code>.
     *
     * @return bool - returns false if field is mandatory and field content is empty or data is not valid
     */
    public function DataIsValid()
    {
        $dataIsValid = $this->CheckMandatoryField();
        // check for custom regex rule
        $regexRule = $this->oDefinition->sqlData['validation_regex'];
        if (!empty($regexRule) && $dataIsValid && $this->HasContent()) {
            $sqlData = $this->ConvertPostDataToSQL();
            if (!preg_match('/'.$regexRule.'/u', $sqlData)) {
                $dataIsValid = false;
                $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
                $sFieldTitle = $this->oDefinition->GetName();
                $this->getFlashMessageService()->addMessage(
                    $sConsumerName,
                    'TABLEEDITOR_FIELD_NOT_VALID',
                    array('sFieldName' => $this->name, 'sFieldTitle' => $sFieldTitle)
                );
            }
        }

        return $dataIsValid;
    }

    /**
     * checks if field is mandatory and if field content is not empty.
     *
     * @return bool - returns false if field is mandatory and field content is empty
     */
    protected function CheckMandatoryField()
    {
        $dataIsValid = true;
        if ($this->IsMandatoryField() && !$this->HasContent()) {
            $dataIsValid = false;
            $consumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
            $fieldTitle = $this->oDefinition->GetName();
            $this->getFlashMessageService()->AddMessage(
                $consumerName,
                'TABLEEDITOR_FIELD_IS_MANDATORY',
                array('sFieldName' => $this->name, 'sFieldTitle' => $fieldTitle)
            );
        }

        return $dataIsValid;
    }

    /**
     * returns true if field data is not empty
     * overwrite this method for mlt and property fields.
     *
     * @return bool
     */
    public function HasContent()
    {
        $hasContent = false;
        $fieldDisplayType = $this->GetDisplayType();
        if ('readonly-if-filled' === $fieldDisplayType) {
            $this->LoadCurrentDataFromDatabase();
            if (!empty($this->oRecordFromDB->sqlData[$this->name])) {
                $hasContent = true;
            }
        } else {
            if ('' !== $this->data) {
                $hasContent = true;
            }
        }

        return $hasContent;
    }

    /**
     * returns tre if field db data is not empty.
     *
     * @return bool
     */
    public function HasDBContent()
    {
        $hasContent = false;
        $this->LoadCurrentDataFromDatabase();
        if (!empty($this->oRecordFromDB->sqlData[$this->name])) {
            $hasContent = true;
        }

        return $hasContent;
    }

    /**
     * check if field is mandatory and data not empty.
     *
     * @return bool
     */
    public function IsMandatoryField()
    {
        $isMandatoryField = false;
        if ('1' == $this->oDefinition->sqlData['isrequired']) {
            $isMandatoryField = true;
        }

        return $isMandatoryField;
    }

    /**
     * Get rendered html for the field. Function returns different html according to the
     * field display type (readonly, edit-on-click, readonly-if-filled or the default: 'none').
     *
     * @param string $displayType get HTML code for display type readonly or edit-on-click
     *
     * @return string $sContent
     */
    public function GetContent($displayType = '')
    {
        $fieldDisplayType = $this->GetDisplayType();
        if ('readonly' === $fieldDisplayType || 'readonly' === $displayType) {
            $content = $this->GetReadOnly();
        } elseif ('edit-on-click' === $fieldDisplayType || 'edit-on-click' === $displayType) {
            $content = $this->GetEditOnClick();
        } elseif (('readonly-if-filled' === $fieldDisplayType || 'readonly-if-filled' === $displayType) && $this->HasContent()) {
            $content = $this->GetReadOnly();
        } elseif ('hidden' === $fieldDisplayType || 'hidden' === $displayType) {
            $content = $this->_GetHiddenField();
        } else {
            $content = $this->GetHTML();
        }

        return $content;
    }

    protected function LoadCurrentDataFromDatabase()
    {
        $className = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $this->sTableName);
        $record = call_user_func_array(array($className, 'GetNewInstance'), array());
        $record->Load($this->recordId);
        $this->oRecordFromDB = $record;
    }

    /**
     * Check if field data is valid when we are in frontend context.
     *
     * @return bool
     */
    public function PkgCmsFormDataIsValid()
    {
        return $this->DataIsValid();
    }

    /**
     * called before GetSQL().
     */
    public function PkgCmsFormPreGetSQLHook()
    {
    }

    /**
     * called after record is saved in frontend.
     *
     * @param string      $id   - ID of the record saved
     * @param TPkgCmsForm $form
     */
    public function PkgCmsFormPostSaveHook($id, $form)
    {
    }

    /**
     * called before save of form, the returned value is set
     * to form data of owning form for this field
     * important: this is called weather data is valid or not!
     *
     * @param TPkgCmsForm $form
     *
     * @return mixed
     */
    public function PkgCmsFormTransformFormDataBeforeSave($form)
    {
        return $this->data;
    }

    /**
     * Renders the form fields input for frontend.
     *
     * @param bool        $bFieldHasError
     * @param bool        $bWrapFieldClassDiv
     * @param string      $sViewName
     * @param string      $sViewType
     * @param array       $aAdditionalVars
     * @param string|null $sViewSubType
     *
     * @return string
     */
    public function Render(
        $bFieldHasError = false,
        $bWrapFieldClassDiv = false,
        $sViewName = 'standard',
        $sViewType = 'Core',
        $aAdditionalVars = array(),
        $sViewSubType = null
    ) {
        $oView = new TViewParser();
        $oView->AddVar('oField', $this);
        $oView->AddVar('bFieldHasError', $bFieldHasError);
        $aAdditionalViewData = $this->GetAdditionalViewData();
        if (is_array($aAdditionalViewData) && count($aAdditionalViewData) > 0) {
            $aAdditionalVars = array_merge($aAdditionalViewData, $aAdditionalVars);
        }
        if (is_array($aAdditionalVars) && count($aAdditionalVars) > 0) {
            $oView->AddVarArray($aAdditionalVars);
        }
        if (null === $sViewSubType) {
            $sViewSubType = $this->sViewPath;
        }
        $sHTML = $oView->RenderObjectPackageView($sViewName, $sViewSubType, $sViewType);
        if ($bWrapFieldClassDiv) {
            $sHTML = '<div class="'.get_class($this).' '.$sViewName.'">'.$sHTML.'</div>';
        }

        return $sHTML;
    }

    /**
     * Get additional view data for the render method.
     *
     * @return array
     */
    protected function GetAdditionalViewData()
    {
        return array();
    }

    /**
     * return array of head includes for frontend rendering.
     *
     * @return array
     */
    public function getHtmlHeadIncludes()
    {
        return array();
    }

    /**
     * returns a javascript method call that will be called in TCMSFieldPropery AddNewXXX calls
     * e.g. if you add a new sub record to a form
     * the method must NOT be enclosed by <script></script>.
     *
     * @return string
     */
    public function getFrontendJavascriptInitMethodOnSubRecordLoad()
    {
        return '';
    }

    /**
     * returns the field config value for a key
     * shortcut to "$this->oDefinition->GetFieldtypeConfigKey()".
     *
     * @param string $sKey
     *
     * @return string|null
     */
    protected function getFieldTypeConfigKey($sKey)
    {
        return $this->oDefinition->GetFieldtypeConfigKey($sKey);
    }

    /**
     * return javascript method names with parameters and trailing ; character
     * for example myFunction(param1, param2);andAnotherFunction();
     * these methods will be executed BEFORE SaveViaAjax is called.
     *
     * @return string
     */
    public function getOnSaveViaAjaxHookMethod()
    {
    }

    /**
     * @param \Doctrine\DBAL\Connection $connection
     */
    public function setDatabaseConnection(Connection $connection)
    {
        $this->databaseConnection = $connection;
    }

    /**
     * @return Connection
     */
    protected function getDatabaseConnection()
    {
        if (null !== $this->databaseConnection) {
            return $this->databaseConnection;
        }

        return ServiceLocator::get('database_connection');
    }

    /**
     * @param TCMSFieldVisitorInterface $visitor
     *
     * @return mixed
     */
    public function accept($visitor)
    {
        return $visitor->visit($this);
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->data;
    }

    /**
     * @param bool $bEncryptedData
     */
    public function setBEncryptedData($bEncryptedData)
    {
        $this->bEncryptedData = $bEncryptedData;
    }

    /**
     * @return bool
     */
    public function getBEncryptedData()
    {
        return $this->bEncryptedData;
    }

    /**
     * @return TIterator
     */
    public function getMenuButtonsForFieldEditor()
    {
        $menuButtonItems = new TIterator();

        $saveButton = $this->getSaveButton();
        if (null !== $saveButton) {
            $menuButtonItems->AddItem($saveButton);
        }

        $saveAndCloseButton = $this->getSaveAndCloseButton();
        if (null !== $saveAndCloseButton) {
            $menuButtonItems->AddItem($saveAndCloseButton);
        }

        $cancelButton = $this->getCancelButton();
        if (null !== $cancelButton) {
            $menuButtonItems->AddItem($cancelButton);
        }

        return $menuButtonItems;
    }

    /**
     * @return TCMSTableEditorMenuItem|null
     */
    protected function getSaveButton()
    {
        $translator = $this->getTranslator();

        $buttonItem = new TCMSTableEditorMenuItem();
        $buttonItem->sItemKey = 'save';
        $buttonItem->setTitle($translator->trans('chameleon_system_core.action.save'));
        $buttonItem->sIcon = 'fas fa-save';
        $buttonItem->setButtonStyle('btn-success');

        $onClickMethod = 'SaveFieldViaAjaxCustomCallback(ShowAjaxSaveResult); return false;';

        $onSaveMethod = $this->getOnSaveViaAjaxHookMethod();
        if (false === empty($onSaveMethod)) {
            if (';' !== substr($onSaveMethod, 0, -1)) {
                $onSaveMethod .= ';';
            }
            $onClickMethod = $onSaveMethod.' '.$onClickMethod;
        }

        $buttonItem->sOnClick = $onClickMethod;

        return $buttonItem;
    }

    /**
     * @return TCMSTableEditorMenuItem|null
     */
    protected function getSaveAndCloseButton()
    {
        $translator = $this->getTranslator();

        $buttonItem = new TCMSTableEditorMenuItem();
        $buttonItem->sItemKey = 'saveandclose';
        $buttonItem->setTitle($translator->trans('chameleon_system_core.action.save_and_return'));
        $buttonItem->sIcon = 'far fa-save';
        $buttonItem->setButtonStyle('btn-success');

        $onClickMethod = 'SaveFieldViaAjaxCustomCallback(ShowAjaxSaveResultAndClose); return false;';

        $onSaveMethod = $this->getOnSaveViaAjaxHookMethod();
        if (false === empty($onSaveMethod)) {
            if (';' !== substr($onSaveMethod, 0, -1)) {
                $onSaveMethod .= ';';
            }
            $onClickMethod = $onSaveMethod.' '.$onClickMethod;
        }

        $buttonItem->sOnClick = $onClickMethod;

        return $buttonItem;
    }

    /**
     * @return TCMSTableEditorMenuItem|null
     */
    protected function getCancelButton()
    {
        $translator = $this->getTranslator();

        $buttonItem = new TCMSTableEditorMenuItem();
        $buttonItem->sItemKey = 'cancel';
        $buttonItem->setTitle($translator->trans('chameleon_system_core.action.abort'));
        $buttonItem->sIcon = 'fas fa-times-circle';
        $buttonItem->setButtonStyle('btn-warning');
        $buttonItem->sOnClick = 'parent.CloseModalIFrameDialog();';

        return $buttonItem;
    }

    /**
     * @return FlashMessageServiceInterface
     */
    private function getFlashMessageService()
    {
        return ServiceLocator::get('chameleon_system_core.flash_messages');
    }

    /**
     * @return TGlobal
     */
    private function getGlobal()
    {
        return ServiceLocator::get('chameleon_system_core.global');
    }

    /**
     * @return InputFilterUtilInterface
     */
    private function getInputFilterUtil()
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }

    /**
     * @return LanguageServiceInterface
     */
    protected function getLanguageService()
    {
        return ServiceLocator::get('chameleon_system_core.language_service');
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslator()
    {
        return ServiceLocator::get('translator');
    }

    /**
     * @return UrlUtil
     */
    private function getUrlUtil()
    {
        return ServiceLocator::get('chameleon_system_core.util.url');
    }
}

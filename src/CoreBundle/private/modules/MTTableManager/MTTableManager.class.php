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
use ChameleonSystem\CoreBundle\Service\BackendBreadcrumbServiceInterface;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use Doctrine\DBAL\Connection;

/**
 * list a table
 * it is possible to overwrite the list object used by parameter "listClass" via pagedef.
 */
class MTTableManager extends TCMSModelBase
{
    /**
     * Table conf.
     *
     * @var TdbCmsTblConf
     */
    protected $oTableConf;

    /**
     * Table list object.
     *
     * @var TCMSListManager
     */
    protected $oTableList;

    /**
     * Messages from TCMSMessageManager to show as toaster message.
     *
     * @var array
     */
    protected $aMessages = [];

    /**
     * Called before any external functions get called, but after the constructor.
     */
    public function Init()
    {
        $inputFilterUtil = $this->getInputFilterUtil();

        $this->oTableConf = TdbCmsTblConf::GetNewInstance();

        $tableConfId = $inputFilterUtil->getFilteredInput('id');
        $fieldName = $inputFilterUtil->getFilteredInput('field');

        if (!$this->oTableConf->Load($tableConfId)) {
            throw new InvalidArgumentException("Record not found: '$tableConfId'.");
        }

        $this->data['sTableName'] = $this->oTableConf->sqlData['name'];
        $this->data['field'] = $fieldName;
        $this->LoadList();
        if (true === $this->IsOnlyOneRecordTableRequest()) {
            $this->HandleOneRecordTables();
        }
        $this->AddURLHistory();

        if (false === $this->oTableList->CheckTableRights()) {
            $oCMSUser = &TCMSUser::GetActiveUser();
            $oCMSUser->Logout();
            $this->getRedirectService()->redirect(PATH_CMS_CONTROLLER);
        }
    }

    public function &Execute()
    {
        $inputFilterUtil = $this->getInputFilterUtil();

        $this->data = parent::Execute();

        // get list cache key for export popup
        $this->data['listCacheKey'] = $this->oTableList->GetListCacheKey();

        $this->data['id'] = $inputFilterUtil->getFilteredInput('id');
        $this->data['permission_new'] = $this->global->oUser->oAccessManager->HasNewPermission($this->oTableConf->sqlData['name']) && $this->oTableList->ShowNewEntryButton();

        // load menu
        $this->data['oMenuItems'] = $this->oTableList->GetMenuItems();
        $this->data['aHiddenFields'] = $this->GetHiddenFieldsHook();

        // required for backward compatibility - may views still access the three fields directly via $data
        if (is_array($this->data['aHiddenFields']) && array_key_exists('sRestriction', $this->data['aHiddenFields'])) {
            $this->data['sRestriction'] = $this->data['aHiddenFields']['sRestriction'];
        }
        if (is_array($this->data['aHiddenFields']) && array_key_exists('sRestrictionField', $this->data['aHiddenFields'])) {
            $this->data['sRestrictionField'] = $this->data['aHiddenFields']['sRestrictionField'];
        }
        if (is_array($this->data['aHiddenFields']) && array_key_exists('bIsLoadedFromIFrame', $this->data['aHiddenFields'])) {
            $this->data['bIsLoadedFromIFrame'] = $this->data['aHiddenFields']['bIsLoadedFromIFrame'];
        }
        // end compatibility fix

        return $this->data;
    }

    /**
     * Loads the table list object.
     */
    protected function LoadList()
    {
        $inputFilterUtil = $this->getInputFilterUtil();

        if (null !== $inputFilterUtil->getFilteredInput('sourceRecordId')) {
            $this->data['sourceRecordID'] = $inputFilterUtil->getFilteredInput('sourceRecordId');
        }

        $listClass = null;
        // fetch listClass and listClassLocation first using the definition in the tableconf...
        if (!empty($this->oTableConf->sqlData['cms_tbl_list_class_id'])) {
            $oListDef = new TCMSRecord();
            $oListDef->table = 'cms_tbl_list_class';
            if ($oListDef->Load($this->oTableConf->sqlData['cms_tbl_list_class_id'])) {
                $listClass = $oListDef->sqlData['classname'];
            }
        }

        // allow custom list class overwriting (defined in pagedef)
        if (array_key_exists('listClass', $this->aModuleConfig)) {
            $listClass = $this->aModuleConfig['listClass'];
        }

        // list class as request parameter
        $targetListClass = $inputFilterUtil->getFilteredInput('targetListClass');
        if (null !== $targetListClass) {
            $listClass = $targetListClass;
        }

        $this->data['listClass'] = $listClass;

        $this->oTableList = &$this->oTableConf->GetListObject($listClass);
        if ('TCMSListManagerMLT' === $listClass) {
            $this->data['bShowCustomSort'] = $this->oTableList->IsCustomSort();
        } else {
            $this->data['bShowCustomSort'] = false;
        }

        $this->data['sTable'] = $this->oTableList->GetList();
    }

    public function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = [
            'ClearNaviCache',
            'getAutocompleteRecordList',
            'getAutocompleteRecords',
            'DeleteSelected',
        ];
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    /**
     * Run a method within the module, but as an ajax call (no module will be used
     * and the function will output json encoded data). The method assumes that
     * the name of the function that you want to execute is in the parameter _fnc.
     * Also note that the function being called needs to be included in $this->methodCallAllowed.
     * You can control how the data will be encoded using the sOutputMode.
     *
     * Use callListManagerMethod as url parameter for passing the ajax call forward to the list-manager.
     */
    public function ExecuteAjaxCall()
    {
        $inputFilterUtil = $this->getInputFilterUtil();

        $methodName = $inputFilterUtil->getFilteredInput('_fnc');

        if (empty($methodName)) {
            trigger_error('Ajax call made, but no function passed via _fnc', E_USER_ERROR);
        } else {
            if (null !== $inputFilterUtil->getFilteredInput('callListManagerMethod')) {
                // if the module is a tableEditor and a callListManagerMethod was send the ajax call will be redirected to the TCMSListManager class
                if (method_exists($this, 'ExecuteAjaxCallInListManager')) {
                    $functionResult = $this->ExecuteAjaxCallInListManager();
                    $this->OutPutAjaxCallResult($functionResult);
                } else {
                    // call the _fnc method in the current module
                    parent::ExecuteAjaxCall();
                }
            } else {
                parent::ExecuteAjaxCall();
            }
        }
    }

    /**
     * Forwards the ajax call to the list manager if the called method exists.
     *
     * @return string|bool
     */
    protected function ExecuteAjaxCallInListManager()
    {
        $inputFilterUtil = $this->getInputFilterUtil();

        $response = false;
        $methodName = $inputFilterUtil->getFilteredInput('_fnc');

        if (null !== $methodName) {
            if ($this->oTableList->IsMethodCallAllowed($methodName)) {
                // execute the method in list-manager object
                $response = $this->oTableList->$methodName();
            }
        }

        return $response;
    }

    public function DeleteSelected()
    {
        $oGlobal = TGlobal::instance();
        $sInput = $oGlobal->GetUserData('items');
        $aInput = explode(',', $sInput);

        foreach ($aInput as $id) {
            /** @var $oEditor TCMSTableEditorManager */
            $oEditor = new TCMSTableEditorManager();
            $oEditor->Init($this->oTableConf->id, $id);
            $oEditor->Delete($id);
        }

        $isInIFrame = $this->global->GetUserData('_isiniframe');

        // redirect back to list
        if ($isInIFrame && 'true' == $isInIFrame) {
            // get redirect parameter
            $parameter = [];
            $parameter['_isiniframe'] = $isInIFrame;
            $parameter['id'] = $this->global->GetUserData('id');
            $parameter['pagedef'] = $this->global->GetUserData('pagedef');

            $aAdditionalParams = $this->GetHiddenFieldsHook();
            if (is_array($aAdditionalParams) && count($aAdditionalParams) > 0) {
                $parameter = array_merge($parameter, $aAdditionalParams);
            }

            if (isset($parameter['sRestrictionField']) && '_mlt' == substr($parameter['sRestrictionField'], -4)) {
                /** @var $oRestrictionTableConf TCMSTableConf */
                $oRestrictionTableConf = new TCMSTableConf();
                $sTableName = substr($parameter['sRestrictionField'], 0, -3);
                $oRestrictionTableConf->LoadFromField('name', $sTableName);

                if ($this->global->UserDataExists('sourceRecordID')) {
                    $parameter['sourceRecordID'] = $this->global->GetUserData('sourceRecordID');
                }

                $this->controller->HeaderRedirect($parameter);
            } else {
                /** @var $oRestrictionTableConf TCMSTableConf */
                $oRestrictionTableConf = new TCMSTableConf();
                $sTableName = substr($oEditor->sRestrictionField, 0, -3);
                $oRestrictionTableConf->LoadFromField('name', $sTableName);

                if ($this->global->UserDataExists('sourceRecordID')) {
                    $parameter['sourceRecordID'] = $this->global->GetUserData('sourceRecordID');
                }

                $this->controller->HeaderRedirect($parameter);
            }
        } else {
            $breadcrumb = $this->getBreadcrumbService()->getBreadcrumb();

            $parentURL = $breadcrumb->GetURL().'&_histid='.($breadcrumb->getHistoryCount() - 1);
        }

        $this->controller->HeaderURLRedirect($parentURL);
    }

    /**
     * Checks if a record exists. if it does, redirect to it, if it does not create one and then redirect.
     */
    protected function HandleOneRecordTables()
    {
        $connection = $this->getDatabaseConnection();
        $inputFilterUtil = $this->getInputFilterUtil();

        $tableId = $inputFilterUtil->getFilteredInput('id');
        $restrictionValue = $inputFilterUtil->getFilteredInput('sRestriction');
        $restrictionField = $inputFilterUtil->getFilteredInput('sRestrictionField');

        $quotedTableName = $connection->quoteIdentifier($this->oTableConf->sqlData['name']);
        $query = "SELECT `id` FROM $quotedTableName";
        if (!empty($restrictionValue) && !empty($restrictionField)) {
            $quotedRestrictionField = $connection->quoteIdentifier($restrictionField);
            $quotedRestrictionValue = $connection->quote($restrictionValue);
            $query .= " WHERE $quotedRestrictionField = $quotedRestrictionValue";
        }

        $parameters = [
            'tableid' => $tableId,
            'pagedef' => 'tableeditor',
        ];

        if ('true' === $inputFilterUtil->getFilteredInput('bOnlyOneRecord')) {
            $parameters['bOnlyOneRecord'] = 'true';
        }

        if ($this->global->UserDataExists('sTableEditorPagdef')) {
            $parameters['pagedef'] = $this->global->GetUserData('sTableEditorPagdef');
        }

        $sClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $this->oTableConf->sqlData['name']).'List';
        $oRecordList = call_user_func([$sClassName, 'GetList'], $query, null, false, true, true);

        if ($oRecordList->Length() > 0) {
            // If record exists, redirect to its editing view.
            $oRecord = $oRecordList->Current();
            $parameters['id'] = $oRecord->id;
        } else {
            // If record does not exist, it needs to be created.
            $parameters['module_fnc['.$this->sModuleSpotName.']'] = 'Insert';
        }

        $additionalParameters = $this->GetHiddenFieldsHook();
        if (is_array($additionalParameters) && count($additionalParameters) > 0) {
            $parameters = array_merge($parameters, $additionalParameters);
        }

        $this->getRedirectService()->redirectToActivePage($parameters);
    }

    protected function AddURLHistory()
    {
        if (false === $this->AllowAddingURLToHistory()) {
            return;
        }

        $inputFilterUtil = $this->getInputFilterUtil();

        $params = [];
        $params['pagedef'] = $inputFilterUtil->getFilteredInput('pagedef');
        $params['id'] = $inputFilterUtil->getFilteredInput('id');

        $additionalParameters = $this->GetHiddenFieldsHook();
        if (is_array($additionalParameters) && count($additionalParameters) > 0) {
            $params = array_merge($params, $additionalParameters);
        }

        // If list was opened within a frame, url should not be added to history. The parameter field "sRestrictionField" can be used as an indicator.
        if (false === $this->isInFrame()) {
            $tableName = $this->oTableConf->GetName();

            $iconCssClass = trim($this->getIconCssClassForTable($this->oTableConf->id));

            if ('' !== $iconCssClass) {
                $tableName = '<i class="'.TGlobal::OutHTML($iconCssClass).'"></i> '.$tableName;
            }

            $breadcrumb = $this->getBreadcrumbService()->getBreadcrumb();
            $breadcrumb->AddItem($params, $tableName);
        }
    }

    private function getIconCssClassForTable(string $tableId): string
    {
        $menuItem = TdbCmsMenuItem::GetNewInstance();
        if (false === $menuItem->LoadFromFields([
            'target' => $tableId,
            'target_table_name' => 'cms_tbl_conf', ]
        )) {
            return '';
        }

        return $menuItem->fieldIconFontCssClass;
    }

    protected function isInFrame(): bool
    {
        $inputFilterUtil = $this->getInputFilterUtil();

        $isInIFrame = $inputFilterUtil->getFilteredInput('isInIFrame');

        if (null !== $isInIFrame) {
            return true;
        }

        $isInIFrame = $inputFilterUtil->getFilteredInput('_isiniframe');

        if (null !== $isInIFrame) {
            return true;
        }

        $pageDefinition = $inputFilterUtil->getFilteredInput('pagedef');

        if (false !== strpos($pageDefinition, 'plain')) {
            return true;
        }

        return false;
    }

    /**
     * @deprecated since 6.3.0 - use getAutocompleteRecords() instead
     *
     * @return array
     */
    public function GetAutoCompleteAjaxList()
    {
        $inputFilterUtil = $this->getInputFilterUtil();
        $recordID = $inputFilterUtil->getFilteredInput('recordID');
        $autoClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $this->oTableConf->fieldName);

        /** @var $recordList TCMSRecordList */
        $recordList = call_user_func([$autoClassName.'List', 'GetList'], $this->getAutocompleteListQuery());

        $returnVal = [];

        /** @var $record TCMSRecord */
        while ($record = $recordList->Next()) {
            $name = $record->GetName();
            if (!empty($name)) {
                $tmp = new stdClass();
                if ($record->id == $recordID) {
                    $name = $name.' <---';
                }
                $tmp->label = $name;
                $tmp->value = $record->id;
                $returnVal[] = $tmp;
            }
        }

        return $returnVal;
    }

    /**
     * Generates the record list for the ajax autocomplete select boxes in table editor and record lists.
     *
     * @return string|false JSON with id, text, html elements
     *
     * @deprecated since 6.3.6 - use getAutocompleteRecords() which uses the correct return type for ajax
     */
    public function getAutocompleteRecordList()
    {
        return json_encode($this->getAutocompleteRecords());
    }

    /**
     * Generates the record list for the ajax autocomplete for search in table editor and record lists.
     */
    protected function getAutocompleteRecords(): array
    {
        $inputFilterUtil = $this->getInputFilterUtil();
        $recordID = $inputFilterUtil->getFilteredInput('recordID');
        $autoClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $this->oTableConf->fieldName);

        $listQuery = $this->getAutocompleteListQuery();
        /** @var $recordList TCMSRecordList */
        $recordList = call_user_func([$autoClassName.'List', 'GetList'], $listQuery);

        $returnVal = [];

        $editLanguageId = $this->getLanguageService()->getActiveEditLanguage()->id;
        $recordList->SetLanguage($editLanguageId);
        /** @var $record TCMSRecord */
        while ($record = $recordList->Next()) {
            $name = $record->GetName();
            if (!empty($name)) {
                // highlight active record
                $cssClass = '';
                if ($record->id === $recordID) {
                    $cssClass = 'active';
                }
                $returnVal[] = ['id' => $record->id, 'name' => $name, 'cssClass' => $cssClass];
            }
        }

        return $returnVal;
    }

    private function getAutocompleteListQuery(): string
    {
        $inputFilterUtil = $this->getInputFilterUtil();
        $searchKey = $inputFilterUtil->getFilteredInput('term');

        $listClass = null;
        // allow custom list class overwriting (defined in pagedef)
        if (array_key_exists('listClass', $this->aModuleConfig)) {
            $listClass = $this->aModuleConfig['listClass'];
        }

        // list class as request parameter
        $targetListClass = $inputFilterUtil->getFilteredInput('targetListClass');
        if (null !== $targetListClass) {
            $listClass = $targetListClass;
        }

        $oTableList = &$this->oTableConf->GetListObject($listClass);

        $query = $oTableList->FilterQuery();
        $parentTableAlias = $oTableList->GetTableAlias($query);

        $nameColumn = $this->oTableConf->GetNameColumn();
        $autoClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $this->oTableConf->fieldName);
        $fieldIsTranslatable = call_user_func([$autoClassName, 'CMSFieldIsTranslated'], $nameColumn);

        if ($fieldIsTranslatable) {
            $currentEditLanguageId = TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID();
            $languageSuffix = TGlobal::GetLanguagePrefix($currentEditLanguageId);
            if ('' !== $languageSuffix) {
                $nameColumn .= '__'.$languageSuffix;
            }
        }
        $databaseConnection = $this->getDatabaseConnection();
        $quotedNameColumn = $databaseConnection->quoteIdentifier($nameColumn);

        $quoteCharacter = $databaseConnection->getDatabasePlatform()->getIdentifierQuoteCharacter();
        if (false === strpos($parentTableAlias, $quoteCharacter)) {
            $quotedParentTableAlias = $databaseConnection->quoteIdentifier($parentTableAlias);
        } else {
            $quotedParentTableAlias = $parentTableAlias;
        }

        if (stristr($query, 'WHERE')) {
            $query = str_replace('WHERE', "WHERE $quotedParentTableAlias.$quotedNameColumn LIKE '%".MySqlLegacySupport::getInstance()->real_escape_string($searchKey)."%' AND ", $query);
        } else {
            $query .= "WHERE $quotedParentTableAlias.$quotedNameColumn LIKE '%".MySqlLegacySupport::getInstance()->real_escape_string($searchKey)."%' ";
        }

        if ('%' !== $searchKey) {
            $query .= " ORDER BY $quotedParentTableAlias.$quotedNameColumn";
        }

        $query .= ' LIMIT 0,50';

        return $query;
    }

    /**
     *  Updates tree caches in cms_tpl_page.
     */
    public function ClearNaviCache()
    {
        TCMSTableEditorPage::UpdateCmsListNaviCache();
        $consumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
        $this->getFlashMessageService()->addMessage($consumerName, 'LISTMANAGER_NAVIGATIONPATHS_UPDATED');
    }

    public function GetHtmlHeadIncludes()
    {
        $includeLines = parent::GetHtmlHeadIncludes();
        $includeLines[] = '<link href="'.TGlobal::GetPathTheme().'/css/table.css" rel="stylesheet" type="text/css" />';
        $includeLines[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/table.js').'" type="text/javascript"></script>';
        $includeLines[] = '<link href="'.TGlobal::GetStaticURLToWebLib('/components/select2.v4/css/select2.min.css').'" media="screen" rel="stylesheet" type="text/css" />';

        $this->LoadMessages();

        if (count($this->aMessages) > 0) {
            $messageBlock = '';
            foreach ($this->aMessages as $message) {
                $messageBlock .= "toasterMessage('".$message['sMessage']."', '".TGlobal::OutJS($message['sMessageType'])."');\n";
            }

            $messageHandlerJS = '
            <script type="text/javascript">
            $(document).ready(function() {
                '.$messageBlock.'
            });
            </script>
            ';

            $includeLines[] = $messageHandlerJS;
        }

        $tableListIncludeLines = [];
        if (!is_null($this->oTableList)) {
            $tableListIncludeLines = $this->oTableList->GetHtmlHeadIncludes();
        }
        $includeLines = array_merge($includeLines, $tableListIncludeLines);

        return $includeLines;
    }

    public function GetHtmlFooterIncludes()
    {
        $includes = parent::GetHtmlFooterIncludes();
        $includes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/components/select2.v4/js/select2.full.min.js').'" type="text/javascript"></script>';

        return $includes;
    }

    /**
     * Loads the MessageManager messages for the table list manager consumer and table editor consumer.
     */
    protected function LoadMessages()
    {
        $constructedMessages = [];
        if (is_array($this->aMessages) && count($this->aMessages) > 0) {
            $constructedMessages = $this->aMessages;
        }

        $consumedMessages = $this->getFlashMessageService()->consumeMessages(TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER);

        if (null !== $consumedMessages) {
            while ($consumedMessage = $consumedMessages->Next()) {
                /** @var $consumedMessage TCMSMessageManagerMessage */
                $params = $consumedMessage->GetMessageParameters();
                $messageString = $consumedMessage->GetMessageString();

                if ('2' === $consumedMessage->fieldCmsMessageManagerMessageTypeId) {
                    $messageType = 'MESSAGE';
                } elseif ('3' === $consumedMessage->fieldCmsMessageManagerMessageTypeId) {
                    $messageType = 'WARNING';
                } elseif ('4' === $consumedMessage->fieldCmsMessageManagerMessageTypeId) {
                    $messageType = 'ERROR';
                } else {
                    $messageType = 'MESSAGE';
                }

                $messageConstruct = [
                    'sMessage' => $messageString,
                    'sMessageType' => $messageType,
                ];

                if (is_array($params) && array_key_exists('sFieldName', $params)) {
                    $messageConstruct['sMessageRefersToField'] = $params['sFieldName'];
                }

                $constructedMessages[] = $messageConstruct;
            }
        }

        $this->aMessages = $constructedMessages;
    }

    /**
     * Return true if this is a only-one-record-edit window request OR the table was set to only-one-record.
     *
     * @return bool
     */
    protected function IsOnlyOneRecordTableRequest()
    {
        $inputFilterUtil = $this->getInputFilterUtil();

        $isOnlyOneRecord = false;
        if ('1' === $this->oTableConf->sqlData['only_one_record_tbl'] || 'true' === $inputFilterUtil->getFilteredInput('bOnlyOneRecord')) {
            $isOnlyOneRecord = true;
        }

        return $isOnlyOneRecord;
    }

    /**
     * Returns array of whitelisted additional hidden fields (key=>value).
     *
     * @return array
     */
    protected function GetHiddenFieldsHook()
    {
        return $this->oTableList->GetHiddenFieldsHook();
    }

    /**
     * @return ICmsCoreRedirect
     */
    private function getRedirectService()
    {
        return ServiceLocator::get('chameleon_system_core.redirect');
    }

    /**
     * @return InputFilterUtilInterface
     */
    private function getInputFilterUtil()
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }

    /**
     * @return Connection
     */
    private function getDatabaseConnection()
    {
        return ServiceLocator::get('database_connection');
    }

    /**
     * @return FlashMessageServiceInterface
     */
    private function getFlashMessageService()
    {
        return ServiceLocator::get('chameleon_system_core.flash_messages');
    }

    private function getBreadcrumbService(): BackendBreadcrumbServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.service.backend_breadcrumb');
    }

    private function getLanguageService(): LanguageServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.language_service');
    }
}

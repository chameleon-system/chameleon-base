<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\BackendBreadcrumbServiceInterface;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

/**
 * edit a table record
 * Note: you can put the editor in "only one record mode" by passing bOnlyOneRecord via get or post.
 * /**/
class MTTableEditor extends TCMSModelBase
{
    /**
     * the table manager.
     *
     * @var TCMSTableEditorManager
     */
    protected $oTableManager;

    /**
     * record ID.
     *
     * @var string
     */
    protected $sId;

    /**
     * cms_tbl_conf ID.
     *
     * @var string
     */
    protected $sTableId;

    /**
     * definition of the table.
     *
     * @var TCMSTableConf
     */
    protected $oTableConf;

    /**
     * target pagedef to redirect after method execution.
     *
     * @var string
     */
    protected $redirectPagedef;

    /**
     * array of methods where a call is allowed without edit rights to the table data.
     *
     * @var array
     */
    protected $readOnlyMethods = [];

    /**
     * indicates if the record is rendered in readonly mode.
     *
     * @var bool
     */
    protected $bIsReadOnlyMode = false;

    /**
     * array of messages from TCMSMessageManager
     * format: array[$sFieldName] = array('sMessage'=>'foo baa message','sMessageType'=>'WARNING')).
     *
     * @var array
     */
    protected $aMessages = [];

    /**
     * base language for translations.
     *
     * @var TdbCmsLanguage
     */
    protected $oBaseLanguage;

    private $fillEmptyFromLanguageId;

    /**
     * @param string|null $languageId
     */
    public function setFillEmptyFromLanguageId($languageId = null)
    {
        if (null === $languageId) {
            $inputFilterUtil = $this->getInputFilterUtil();
            $languageId = $inputFilterUtil->getFilteredInput('languageId', null);
        }
        // todo validate

        // all good -set
        $this->fillEmptyFromLanguageId = $languageId;
    }

    private function fillEmptyFieldsWithTranslationFrom(TCMSRecord $targetObject, $languageId)
    {
        $translatedObject = clone $targetObject;
        $translatedObject->SetLanguage($languageId);
        $translatedObject->Load($targetObject->id);
        $translatedFields = TdbCmsConfig::GetInstance()->GetListOfTranslatableFields($targetObject->table);
        $new = $targetObject->sqlData;
        $activeLanguagePrefix = TGlobal::GetLanguagePrefix($this->getLanguageService()->getActiveLanguageId());
        if (!empty($activeLanguagePrefix)) {
            $activeLanguagePrefix = '__'.$activeLanguagePrefix;
        }
        foreach ($translatedFields as $field) {
            $new[$field.$activeLanguagePrefix] = $translatedObject->sqlData[$field];
        }

        $targetObject->LoadFromRow($new);

        return $targetObject;
    }

    public function __construct()
    {
        parent::__construct();

        $this->DefineReadOnlyMethods();
    }

    /**
     * called before any external functions gets called, but after the constructor.
     */
    public function Init()
    {
        if ($this->global->UserDataExists('showLangCopy')) {
            $this->data['showLangCopy'] = true;
        }
        $oGlobal = TGlobal::instance();
        $this->oTableManager = new TCMSTableEditorManager();

        $sId = $oGlobal->GetUserData('id');
        if (!empty($sId)) {
            $this->sId = $sId;
        }
        $this->redirectPagedef = $oGlobal->GetUserData('redirectPagedef');

        $this->data['sForeignField'] = null;
        if ($oGlobal->UserDataExists('field')) {
            // tableeditor called in iframe from other field
            $this->data['sForeignField'] = $oGlobal->GetUserData('field');
        }

        // these properties need to be set BEFORE calling the init function so that they can be passed on
        // to its internal table handler object
        if ($oGlobal->UserDataExists('sRestriction')) {
            $this->data['sRestriction'] = $oGlobal->GetUserData('sRestriction');
            $this->oTableManager->sRestriction = $oGlobal->GetUserData('sRestriction');
        }
        if ($oGlobal->UserDataExists('sRestrictionField')) {
            $this->data['sRestrictionField'] = $oGlobal->GetUserData('sRestrictionField');
            $this->oTableManager->sRestrictionField = $oGlobal->GetUserData('sRestrictionField');
        }
        $sId = $this->sId;
        if ($this->oTableManager->Init($oGlobal->GetUserData('tableid'), $sId)) {
            /** @var $oConfig TdbCmsConfig */
            $oConfig = TCMSConfig::GetInstance();
            $oBaseLanguage = $oConfig->GetFieldTranslationBaseLanguage();
            $this->oBaseLanguage = $oBaseLanguage;

            $this->data['only_one_record_tbl'] = '0';
            if (true === $this->IsOnlyOneRecordTableRequest()) {
                $this->data['only_one_record_tbl'] = '1';
                $this->oTableManager->oTableConf->sqlData['only_one_record_tbl'] = '1';
                $this->oTableManager->oTableEditor->oTableConf->sqlData['only_one_record_tbl'] = '1';
            }
            $this->bIsReadOnlyMode = $this->oTableManager->oTableEditor->IsRecordInReadOnlyMode();
            $this->handleUserRights();

            $this->data['oTabs'] = $this->GetTabsForTable();

            $this->AddURLHistory();
        } else { // record is missing - show error template
            $this->handleMissingRecord();
        }
    }

    protected function handleUserRights(): void
    {
        $userHasReadOnlyRight = $this->oTableManager->oTableEditor->AllowReadOnly();

        // check rights
        $isReadOnlyRequest = $this->IsReadOnlyRequest();
        $isInsert = empty($this->sId);

        $userHasEditRight = $this->oTableManager->oTableEditor->AllowEdit();

        if (false === $isReadOnlyRequest && (false === $userHasEditRight && false === $isInsert && false === $this->bIsReadOnlyMode || true === $this->bIsReadOnlyMode && false === $userHasReadOnlyRight)) {
            /** @var RouterInterface $router */
            $router = ServiceLocator::get('router');
            $logout = $router->generate('app_logout');
            $this->getRedirectService()->redirect($logout);
        }
    }

    protected function isCopy(): bool
    {
        $inputFilterUtil = $this->getInputFilterUtil();
        $moduleFunctions = $inputFilterUtil->getFilteredInput('module_fnc');

        return 'Copy' === ($moduleFunctions['contentmodule'] ?? null);
    }

    protected function handleMissingRecord(): void
    {
        if (false === $this->isCopy() || true === empty($this->sId)) {
            $moduleName = get_class($this);
            $tableName = $this->oTableManager->oTableConf->GetName();
            $id = $this->sId;
            if (true === empty($id)) {
                $id = ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_table_editor.no_id_set');
            }
            $this->data['errorMessage'] = ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_table_editor.error_record_missing', ['%id%' => $id, '%tableName%' => $tableName]);
            $this->SetTemplate($moduleName, 'error');
        }
    }

    /**
     * sets an array of methods where calling is allowed without edit rights to the table.
     */
    protected function DefineReadOnlyMethods()
    {
        $readOnlyMethods = ['GetName', 'GetDisplayValue', 'IsRecordLockedAjax'];
        $this->readOnlyMethods = array_merge($this->readOnlyMethods, $readOnlyMethods);
    }

    /**
     * checks if method called is a read only method where no edit rights are necessery
     * (calls like GetName, GetDisplayValue).
     *
     * @return bool
     */
    protected function IsReadOnlyRequest()
    {
        $isAllowed = false;
        $sMethodName = $this->global->GetUserData('_fnc');
        if (!empty($sMethodName)) {
            if (in_array($sMethodName, $this->readOnlyMethods)) {
                $isAllowed = true;
            }
        }

        return $isAllowed;
    }

    /**
     * adds the current step to the breadcrumb object.
     */
    protected function AddURLHistory()
    {
        if ($this->AllowAddingURLToHistory()) {
            $breadcrumb = $this->getBreadcrumbService()->getBreadcrumb();

            if ($this->global->UserDataExists('popLastURL')) {
                $breadcrumb->PopURL();
            }

            $params = [];
            $params['pagedef'] = $this->global->GetUserData('pagedef');
            $params['id'] = $this->oTableManager->sId;
            $params['tableid'] = $this->oTableManager->sTableId;

            $aAdditionalParams = $this->GetHiddenFieldsHook();
            if (is_array($aAdditionalParams) && count($aAdditionalParams) > 0) {
                $params = array_merge($params, $aAdditionalParams);
            }

            $sRecordName = $this->oTableManager->oTableEditor->oTable?->GetDisplayValue() ?? '';
            $breadcrumb->AddItem($params, $sRecordName);
        }
    }

    public function Execute()
    {
        $this->data = parent::Execute();
        if ('error' !== $this->aModuleConfig['view']) {
            $this->data['isReadOnly'] = $this->bIsReadOnlyMode;

            $tableName = $this->oTableManager->oTableConf->sqlData['name'];
            $sTableTitle = $this->oTableManager->oTableConf->GetName();
            $this->data['sTableTitle'] = $sTableTitle;
            $sRecordName = '';
            $oTable = null;
            $this->data['cmsident'] = '';
            if (is_object($this->oTableManager->oTableEditor->oTable)) {
                $oTable = $this->oTableManager->oTableEditor->oTable;
                $sRecordName = $oTable->GetName();
                $this->data['cmsident'] = $oTable->sqlData['cmsident'];
            }
            $this->data['sRecordName'] = $sRecordName;

            $this->data['oTableDefinition'] = $this->oTableManager->oTableConf;
            $this->data['sTableName'] = $tableName;
            $this->data['id'] = $this->oTableManager->sId;
            $this->data['tableid'] = $this->oTableManager->sTableId;
            $this->data['referer_id'] = $this->global->GetUserData('referer_id');
            $this->data['referer_table'] = $this->global->GetUserData('referer_table');
            $this->data['oTable'] = $oTable;
            $this->data['bIsLoadedFromIFrame'] = false;
            if ($this->global->UserDataExists('bIsLoadedFromIFrame')) {
                $this->data['bIsLoadedFromIFrame'] = $this->global->GetUserData('bIsLoadedFromIFrame');
            }
            $this->data['copyState'] = 'notCopied';
            if ($this->global->UserDataExists('copyState')) {
                $this->data['copyState'] = $this->global->GetUserData('copyState');
            }

            $this->GetPermissionSettings();
            $this->IsRecordLocked();

            $this->data['oBaseLanguage'] = $this->oBaseLanguage;

            $breadcrumb = $this->getBreadcrumbService()->getBreadcrumb();
            $this->data['breadcrumb'] = $breadcrumb->GetBreadcrumb(true);
            if ($this->isEditFieldMode()) {
                $editFieldName = $this->global->GetUserData('_fieldName');
                $this->oTableManager->oTableEditor->setActiveEditField($editFieldName);
                $this->data['oField'] = $this->oTableManager->oTableConf->GetField($editFieldName, $oTable);
                $this->data['_fieldName'] = $editFieldName;
            } else {
                $aPostData = $this->global->GetUserData(null);
                $oPostTable = $this->oTableManager->oTableEditor->GetNewTableObjectForEditor();
                $oPostTable->SetLanguage($this->oTableManager->oTableConf->GetLanguage());
                $oPostTable->Load($this->oTableManager->sId);

                if (null !== $this->fillEmptyFromLanguageId) {
                    $oPostTable = $this->fillEmptyFieldsWithTranslationFrom($oPostTable, $this->fillEmptyFromLanguageId);
                }
                if (array_key_exists('module_fnc', $aPostData) && array_key_exists('contentmodule', $aPostData['module_fnc']) && 'Save' === $aPostData['module_fnc']['contentmodule']) {
                    $aPostBlackList = ['pagedef', 'tableid', 'id', 'referer_id', 'referer_table', '_fnc', 'module_fnc', '_noModuleFunction'];
                    foreach ($aPostBlackList as $forbiddenPostKey) {
                        unset($aPostData[$forbiddenPostKey]);
                    }

                    $oPostTable->sqlData = array_merge($oPostTable->sqlData, $aPostData);

                    $oFields = $this->oTableManager->oTableEditor->oTableConf->GetFields($oPostTable);
                    while ($oField = $oFields->Next()) {
                        $convertedData = $oField->ConvertPostDataToSQL();
                        if (false === $convertedData || null === $convertedData) {
                            $convertedData = '';
                        }
                        $oField->data = $convertedData;
                    }
                } else {
                    $oFields = $this->oTableManager->oTableEditor->oTableConf->GetFields($oPostTable);
                }

                $this->data['oFields'] = $oFields;
                $this->oTableManager->oTableEditor->ProcessFieldsBeforeDisplay($this->data['oFields']);
            }
            $this->data['oMenuItems'] = $this->oTableManager->oTableEditor->GetMenuItems();
            $this->data['aHiddenFields'] = $this->GetHiddenFieldsHook();
        }
        $this->LoadMessages();

        return $this->data;
    }

    /**
     * @return bool
     */
    protected function isEditFieldMode()
    {
        $request = $this->getCurrentRequest();
        if ('editfield' === $request->request->get('_fnc') || 'editfield' === $request->query->get('_fnc')) {
            return true;
        }

        return false;
    }

    /**
     * @return Request
     */
    protected function getCurrentRequest()
    {
        return ServiceLocator::get('request_stack')->getCurrentRequest();
    }

    /**
     * loads the MessageManager messages for the table editor consumer.
     */
    protected function LoadMessages($bConsumeMessages = true)
    {
        /** @var $oMessageManager TCMSMessageManager */
        $oMessageManager = TCMSMessageManager::GetInstance();
        $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;

        if (is_array($this->aMessages) && count($this->aMessages) > 0) {
            $aMessages = $this->aMessages;
        } else {
            $aMessages = [];
        }

        $oMessages = $oMessageManager->ConsumeMessages($sConsumerName, $bConsumeMessages);

        if (null !== $oMessages) {
            while ($oMessage = $oMessages->Next()) {
                /** @var $oMessage TCMSMessageManagerMessage */
                $aParams = $oMessage->GetMessageParameters();
                $sMessage = $oMessage->GetMessageString();

                if ('2' == $oMessage->fieldCmsMessageManagerMessageTypeId) { // Notice
                    $sMessageType = 'MESSAGE';
                } elseif ('3' == $oMessage->fieldCmsMessageManagerMessageTypeId) {
                    $sMessageType = 'WARNING';
                } elseif ('4' == $oMessage->fieldCmsMessageManagerMessageTypeId) {
                    $sMessageType = 'ERROR';
                } else {
                    $sMessageType = 'MESSAGE';
                }

                $aMessage = [
                    'sMessage' => $sMessage,
                    'sMessageType' => $sMessageType,
                ];

                if (is_array($aParams) && array_key_exists('sFieldName', $aParams)) {
                    $aMessage['sMessageRefersToField'] = $aParams['sFieldName'];
                }

                $aMessages[] = $aMessage;
            }
        }

        $this->aMessages = $aMessages;
        $this->data['aMessages'] = $aMessages;
    }

    /**
     * returns the Tablist for the current Table.
     *
     * @return TdbCmsTblFieldTabList
     */
    protected function GetTabsForTable()
    {
        $oTabs = TdbCmsTblFieldTabList::GetList();
        $oTabs->AddFilterString("`cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->oTableManager->sTableId)."'");

        return $oTabs;
    }

    /**
     * returns array of additional hidden fields (key=>value).
     *
     * @return array
     */
    protected function GetHiddenFieldsHook()
    {
        return $this->oTableManager->oTableEditor->GetHiddenFieldsHook();
    }

    /**
     * loads the permissions for new, edit, delete, showlist in data['aPermission'].
     */
    protected function GetPermissionSettings()
    {
        $permissions = ['new' => false, 'edit' => false, 'delete' => false, 'showlist' => false];
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        $permissions['edit'] = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT, $this->oTableManager->oTableConf->sqlData['name']);
        $tableInUserGroup = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_ACCESS, $this->oTableManager->oTableConf->sqlData['name']);
        if ($tableInUserGroup) {
            $permissions['showlist'] = true;
            if ($this->IsOnlyOneRecordTableRequest()) {
                $permissions['new'] = false;
                $permissions['delete'] = false;
            } else {
                $permissions['new'] = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_NEW, $this->oTableManager->oTableConf->sqlData['name']);
                $permissions['delete'] = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_DELETE, $this->oTableManager->oTableConf->sqlData['name']);
            }
        }
        $this->data['aPermission'] = $permissions;
    }

    public function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = ['Save', 'Insert', 'Delete', 'AjaxDelete', 'Copy', 'DatabaseCopy', 'AjaxSave', 'AjaxSaveField', 'AjaxGetFieldEditBox', 'AjaxGetPreviewURL', 'PublishViaAjax', 'RefreshLock', 'AddNewRevision', 'ActivateRevision', 'IsRecordLockedAjax'];
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
        $this->methodCallAllowed[] = 'changeListFieldState';
        $this->methodCallAllowed[] = 'setFillEmptyFromLanguageId';
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();

        // right click contextmenu
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery/contextmenu/contextmenu.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<link href="'.TGlobal::GetPathTheme().'/css/contextmenu.css" rel="stylesheet" type="text/css" />';
        $aIncludes[] = '<link href="'.TGlobal::GetPathTheme().'/css/tableeditcontainer.css" rel="stylesheet" type="text/css" />';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/components/bootstrap3-typeahead/bootstrap3-typeahead.min.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/tableEditor.js?v1').'" type="text/javascript"></script>';
        $aIncludes[] = '<link href="'.TGlobal::GetStaticURLToWebLib('/components/select2.v4/css/select2.min.css').'" media="screen" rel="stylesheet" type="text/css" />';

        if (!$this->IsRecordLocked() && array_key_exists('locking_active', $this->oTableManager->oTableConf->sqlData) && '1' == $this->oTableManager->oTableConf->sqlData['locking_active'] && !$this->bIsReadOnlyMode && CHAMELEON_ENABLE_RECORD_LOCK) {
            $aIncludes[] = '<script type="text/javascript">
        $(document).ready(function(){
           RefreshRecordEditLock();
        });
        </script>';
        }

        // onbeforeunload message
        $aIncludes[] = '<script type="text/javascript">
        window.onbeforeunload = function () {
          if (CHAMELEON.CORE.MTTableEditor.bCmsContentChanged) {
            CHAMELEON.CORE.hideProcessingModal();
            return \''.ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_table_editor.confirm_discard_changes').'\';
          }
        }

      </script>';

        // get the tableEditor specific head includes
        $aTableEditIncludes = $this->oTableManager->oTableEditor->GetHtmlHeadIncludes();
        $aIncludes = array_merge($aIncludes, $aTableEditIncludes);

        // get the head includes for all the fields...
        if ($this->global->UserDataExists('_fnc') && 'editfield' === $this->global->GetUserData('_fnc')) {
            $editFieldName = $this->global->GetUserData('_fieldName');
            $oField = $this->oTableManager->oTableConf->GetField($editFieldName, $this->oTableManager->oTableEditor->oTable);
            if (null !== $oField) {
                $aFieldIncludes = $oField->GetCMSHtmlHeadIncludes();
                $aIncludes = array_merge($aIncludes, $aFieldIncludes);
            }
        } else {
            $oFields = $this->oTableManager->oTableConf->GetFields($this->oTableManager->oTableEditor->oTable);
            while ($oField = $oFields->Next()) {
                /* @var $oField TCMSField */
                $aFieldIncludes = $oField->GetCMSHtmlHeadIncludes();
                $aIncludes = array_merge($aIncludes, $aFieldIncludes);
            }
        }

        return $aIncludes;
    }

    public function GetHtmlFooterIncludes()
    {
        $aIncludes = [];
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/components/select2.v4/js/select2.full.min.js').'" type="text/javascript"></script>';

        // get the tableEditor specific footer includes
        $aTableEditIncludes = $this->oTableManager->oTableEditor->GetHtmlFooterIncludes();
        $aIncludes = array_merge($aIncludes, $aTableEditIncludes);

        if ($this->global->UserDataExists('_fnc') && 'editfield' === $this->global->GetUserData('_fnc')) {
            $editFieldName = $this->global->GetUserData('_fieldName');
            $oField = $this->oTableManager->oTableConf->GetField($editFieldName, $this->oTableManager->oTableEditor->oTable);
            if (null !== $oField) {
                $aFieldIncludes = $oField->GetCMSHtmlFooterIncludes();
                $aIncludes = array_merge($aIncludes, $aFieldIncludes);
            }
        } else {
            $oFields = $this->oTableManager->oTableConf->GetFields($this->oTableManager->oTableEditor->oTable);
            while ($oField = $oFields->Next()) {
                /* @var $oField TCMSField */
                $aFieldIncludes = $oField->GetCMSHtmlFooterIncludes();
                $aIncludes = array_merge($aIncludes, $aFieldIncludes);
            }
        }

        return $aIncludes;
    }

    /**
     * allows to call a method in a TCMSFields class via Ajax.
     *
     * @param string $sFieldName
     *
     * @return string
     */
    protected function ExecuteAjaxCallInField($sFieldName)
    {
        $returnData = false;
        if (!empty($sFieldName) && $this->global->UserDataExists('_fnc')) {
            $sMethodName = $this->global->GetUserData('_fnc');
            $oField = $this->oTableManager->oTableConf->GetField($sFieldName, $this->oTableManager->oTableEditor->oTable);
            if (null !== $oField) {
                if ($oField->isMethodCallAllowed($sMethodName)) {
                    // execute the method in field object
                    $returnData = $oField->$sMethodName();
                }
            }
        }

        return $returnData;
    }

    /**
     * overwrite the callmethod method, so that we pass the call on the the TCMSTableEditorManager.
     *
     * @param string $sFunctionName - method name
     * @param array $aMethodParameter - parameters to pass to the method
     */
    public function _CallMethod($sFunctionName, $aMethodParameter = [])
    {
        $tmp = null;
        $isNotAModuleFunction = $this->global->GetUserData('_noModuleFunction');
        if ('true' === $isNotAModuleFunction && 'ExecuteAjaxCall' !== $sFunctionName) {
            $tmp = $this->oTableManager->HandleExternalFunctionCall($sFunctionName);
        } else {
            $tmp = parent::_CallMethod($sFunctionName);
        }

        return $tmp;
    }

    /**
     * saves record via post (not ajax).
     *
     * @param bool $bPreventRedirect - if true the editor won´t be reloaded (which leads to empty postdata)
     *
     * @return bool
     */
    public function Save($bPreventRedirect = false)
    {
        $bSaveSuccessfull = false;
        $postData = $this->global->GetUserData(null);
        if ($this->oTableManager->oTableEditor->AllowEdit()) {
            $oRecordData = $this->oTableManager->Save($postData);

            $oMessageManager = TCMSMessageManager::GetInstance();
            $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;

            if (null !== $oRecordData && !empty($oRecordData->id)) {
                $oMessageManager->AddMessage($sConsumerName, 'TABLEEDITOR_SAVE_SUCCESS', ['sRecordName' => $oRecordData->name]);
                $bSaveSuccessfull = true;
            } else {
                $bSaveSuccessfull = false;
            }

            if ($bSaveSuccessfull && !$bPreventRedirect) {
                $parameter = ['pagedef' => $this->global->GetUserData('pagedef'), 'tableid' => $this->oTableManager->sTableId, 'id' => $this->oTableManager->sId];

                $aAdditionalParams = $this->GetHiddenFieldsHook();
                if (is_array($aAdditionalParams) && count($aAdditionalParams) > 0) {
                    $parameter = array_merge($parameter, $aAdditionalParams);
                }
                $this->getRedirectService()->redirectToActivePage($parameter);
            } else {
                $this->LoadMessages(true);
            }
        } else {
            $this->LoadMessages(true);
        }

        return $bSaveSuccessfull;
    }

    /**
     * returns the url for the preview button.
     *
     * @return string
     */
    public function AjaxGetPreviewURL()
    {
        return $this->oTableManager->oTableEditor->GetPreviewURL();
    }

    /**
     * loads a table editor object for one field only
     * field name needs to be set via url parameter "_fieldName".
     *
     * @return array - array('field'=>'','content'=>'')
     */
    public function AjaxGetFieldEditBox()
    {
        $editFieldName = $this->global->GetUserData('_fieldName');
        $oField = $this->oTableManager->oTableConf->GetField($editFieldName, $this->oTableManager->oTableEditor->oTable);
        $result = ['field' => $editFieldName, 'content' => $oField->GetContent()];

        return $result;
    }

    /**
     * saves table via ajax.
     *
     * @return TCMSstdClass
     */
    public function AjaxSave()
    {
        $request = $this->getCurrentRequest();

        $oRecordData = false;
        if ($this->oTableManager->oTableEditor->AllowEdit() && true === $request?->isMethod('POST')) {
            $postData = $this->global->GetUserData(null);
            $oRecordData = $this->oTableManager->Save($postData);

            $oMessageManager = TCMSMessageManager::GetInstance();
            $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;

            if (false !== $oRecordData) {
                $table = true === isset($this->oTableManager->oTableEditor->oTable) ? $this->oTableManager->oTableEditor->oTable : null;
                if (null !== $table) {
                    $sName = $table->GetName();
                    $oRecordData->breadcrumbName = $table->GetDisplayValue();
                } elseif (isset($postData['name'])) {
                    $sName = $postData['name'];
                }
                $oMessageManager->AddMessage($sConsumerName, 'TABLEEDITOR_SAVE_SUCCESS', ['sRecordName' => $sName]);
            }

            $this->LoadMessages();
            if (false === $oRecordData || null === $oRecordData) {
                $oRecordData = $this->oTableManager->oTableEditor->GetObjectShortInfo($postData);
            }

            $oRecordData->aMessages = $this->aMessages;
        }

        return $oRecordData;
    }

    /**
     * saves table field via ajax.
     *
     * @return array
     */
    public function AjaxSaveField()
    {
        $request = $this->getCurrentRequest();

        if ($this->oTableManager->oTableEditor->AllowEdit() && true === $request?->isMethod('POST')) {
            $editFieldName = $this->global->GetUserData('_fieldName');
            $fieldValue = $this->global->GetUserData($editFieldName);
            $this->oTableManager->SaveField($editFieldName, $fieldValue);
            $contentFormatted = $this->getFormattedFieldContent($editFieldName, $fieldValue);
            $this->LoadMessages();

            $result = [
                'success' => false === $this->hasErrorMessages(),
                'fieldname' => $editFieldName,
                'content' => $fieldValue,
                'contentFormatted' => $contentFormatted,
                'messages' => $this->aMessages,
            ];
        } else {
            $result = ['success' => false];
        }

        return $result;
    }

    private function hasErrorMessages(): bool
    {
        if (is_array($this->aMessages) && count($this->aMessages) > 0) {
            foreach ($this->aMessages as $message) {
                if ('ERROR' === $message['sMessageType']) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $editFieldName
     * @param string $editFieldValue
     *
     * @return string
     */
    private function getFormattedFieldContent($editFieldName, $editFieldValue)
    {
        $tableEditor = $this->oTableManager->oTableEditor;
        $oPostTable = $tableEditor->GetNewTableObjectForEditor();
        $postData = [
            'id' => $this->sId,
            $editFieldName => $editFieldValue,
        ];
        $oPostTable->DisablePostLoadHook(true);
        $oPostTable->LoadFromRow($postData);
        $field = $tableEditor->oTableConf->GetField($editFieldName, $oPostTable);

        return $field->GetReadOnly();
    }

    /**
     * creates a new record.
     */
    public function Insert()
    {
        $inputFilterUtil = $this->getInputFilterUtil();

        $insertObject = $this->oTableManager->Insert();

        if (($insertObject && null !== $insertObject->id) || (!$insertObject && isset($this->oTableManager) && isset($this->oTableManager->oTableEditor) && isset($this->oTableManager->oTableEditor->oTable))) {
            // Remove last history stamp.
            $breadcrumb = $this->getBreadcrumbService()->getBreadcrumb();
            $breadcrumb->PopURL();

            $parameters = [
                'pagedef' => $inputFilterUtil->getFilteredInput('pagedef'),
                'tableid' => $this->oTableManager->sTableId,
                'id' => $this->oTableManager->sId,
            ];
            if ('true' === $inputFilterUtil->getFilteredInput('bOnlyOneRecord')) {
                $parameters['bOnlyOneRecord'] = 'true';
            }

            $additionalParams = $this->GetHiddenFieldsHook();
            if (is_array($additionalParams) && count($additionalParams) > 0) {
                $parameters = array_merge($parameters, $additionalParams);
            }
            $this->getRedirectService()->redirectToActivePage($parameters);
        } else {
            $sModuleName = get_class($this);
            $sTableName = $this->oTableManager->oTableConf->GetName();
            $this->data['errorMessage'] = ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_table_editor.error_unable_to_create_new_record', ['%id%' => $this->sId, '%tableName%' => $sTableName]);
            $this->SetTemplate($sModuleName, 'error');
        }
    }

    /**
     * deletes a record.
     *
     * @param bool $bPreventRedirect - set true to disable redirects (e.g. ajax)
     */
    public function Delete($bPreventRedirect = false)
    {
        $this->oTableManager->Delete();
        // note: the symfony event listener `CleanupBreadcrumbAfterDeleteListener` removes all affected history entries, especially the current one at the top of the history stack

        if (!$bPreventRedirect) {
            $inputFilterUtil = $this->getInputFilterUtil();
            $isInIFrame = $inputFilterUtil->getFilteredInput('_isiniframe');
            $parentURL = '';

            $parameter = [];
            $parameter['_isiniframe'] = $isInIFrame;
            $parameter['id'] = $this->oTableManager->sTableId;

            $breadcrumb = $this->getBreadcrumbService()->getBreadcrumb();

            if ('true' === $isInIFrame) {
                $aAdditionalParams = $this->GetHiddenFieldsHook();
                if (is_array($aAdditionalParams) && count($aAdditionalParams) > 0) {
                    $parameter = array_merge($parameter, $aAdditionalParams);
                }

                if ('_mlt' === substr($this->oTableManager->sRestrictionField, -4)) {
                    /** @var $oRestrictionTableConf TCMSTableConf */
                    $oRestrictionTableConf = new TCMSTableConf();
                    $sTableName = substr($this->oTableManager->sRestrictionField, 0, -3);
                    $oRestrictionTableConf->LoadFromField('name', $sTableName);

                    $parameter['pagedef'] = 'mltfield';

                    if (null !== $this->redirectPagedef && !empty($this->redirectPagedef)) {
                        $parameter['pagedef'] = $this->redirectPagedef;
                    }

                    if ($this->global->UserDataExists('sourceRecordID')) {
                        $parameter['sourceRecordID'] = $this->global->GetUserData('sourceRecordID');
                    }

                    $this->getRedirectService()->redirectToActivePage($parameter);
                } else {
                    /** @var $oRestrictionTableConf TCMSTableConf */
                    $oRestrictionTableConf = new TCMSTableConf();
                    $sTableName = substr($this->oTableManager->sRestrictionField, 0, -3);
                    $oRestrictionTableConf->LoadFromField('name', $sTableName);

                    $parameter['pagedef'] = 'tablemanagerframe';

                    if (null !== $this->redirectPagedef && !empty($this->redirectPagedef)) {
                        $parameter['pagedef'] = $this->redirectPagedef;
                    }

                    $sourceRecordId = $inputFilterUtil->getFilteredInput('sourceRecordID');
                    if (null !== $sourceRecordId) {
                        $parameter['sourceRecordID'] = $sourceRecordId;
                    }

                    $this->getRedirectService()->redirectToActivePage($parameter);
                }
            } else {
                $parentURL = $breadcrumb->GetURL();
                if (false === $parentURL) {
                    $parentURL = URL_CMS_CONTROLLER;
                } else {
                    $parentURL .= '&_histid='.($breadcrumb->getHistoryCount() - 1);
                }
            }

            if (!empty($parentURL)) {
                $this->getRedirectService()->redirect($parentURL);
            }
        }
    }

    /**
     * deletes a record and prevents redirection
     * note: always returns true at the moment.
     *
     * @return bool
     */
    public function AjaxDelete()
    {
        $this->Delete(true);

        return true;
    }

    /**
     * copy a record using the posted data.
     */
    public function Copy()
    {
        $postData = $this->global->GetUserData(null);
        $this->oTableManager->Copy($postData);

        $breadcrumb = $this->getBreadcrumbService()->getBreadcrumb();
        $breadcrumb->PopURL();

        if ('_mlt' === substr($this->oTableManager->sRestrictionField, -4)) {
            $targetTable = $this->oTableManager->oTableConf->sqlData['name'];
            $sourceTable = substr($this->oTableManager->sRestrictionField, 0, -4);
            $MLTTable = $sourceTable.'_'.$targetTable.'_mlt';
            $mltQuery = 'INSERT INTO `'.MySqlLegacySupport::getInstance()->real_escape_string($MLTTable)."` SET `source_id` ='".MySqlLegacySupport::getInstance()->real_escape_string($this->oTableManager->sRestriction)."', `target_id` = '{$this->oTableManager->sId}'";
            MySqlLegacySupport::getInstance()->query($mltQuery);
        }

        $parameter = ['pagedef' => $this->global->GetUserData('pagedef'), 'tableid' => $this->oTableManager->sTableId, 'id' => $this->oTableManager->sId];

        $aAdditionalParams = $this->GetHiddenFieldsHook();
        if (is_array($aAdditionalParams) && count($aAdditionalParams) > 0) {
            $parameter = array_merge($parameter, $aAdditionalParams);
        }

        $this->getRedirectService()->redirectToActivePage($parameter);
    }

    /**
     * copy a record using data from database.
     */
    public function DatabaseCopy()
    {
        $this->oTableManager->DatabaseCopy(false, [], true);

        $breadcrumb = $this->getBreadcrumbService()->getBreadcrumb();
        $breadcrumb->PopURL();

        if ('_mlt' === substr($this->oTableManager->sRestrictionField, -4)) {
            $targetTable = $this->oTableManager->oTableConf->sqlData['name'];
            $sourceTable = substr($this->oTableManager->sRestrictionField, 0, -4);
            $MLTTable = $sourceTable.'_'.$targetTable.'_mlt';
            $mltQuery = 'INSERT INTO `'.MySqlLegacySupport::getInstance()->real_escape_string($MLTTable)."` SET `source_id` ='".MySqlLegacySupport::getInstance()->real_escape_string($this->oTableManager->sRestriction)."', `target_id` = '{$this->oTableManager->sId}'";
            MySqlLegacySupport::getInstance()->query($mltQuery);
        }

        $parameters = [
            'pagedef' => $this->global->GetUserData('pagedef'),
            'tableid' => $this->oTableManager->sTableId,
            'id' => $this->oTableManager->sId,
            'copyState' => 'copied',
        ];

        $additionalParameters = $this->GetHiddenFieldsHook();
        if (is_array($additionalParameters) && count($additionalParameters) > 0) {
            $parameters = array_merge($parameters, $additionalParameters);
        }

        $this->getRedirectService()->redirectToActivePage($parameters);
    }

    /**
     * inserts or refreshes the lock for the current record.
     *
     * @return bool - returns always true
     */
    public function RefreshLock()
    {
        if (false === $this->IsRecordLocked() && array_key_exists('locking_active', $this->oTableManager->oTableConf->sqlData) && '1' == $this->oTableManager->oTableConf->sqlData['locking_active'] && CHAMELEON_ENABLE_RECORD_LOCK) {
            if ($this->oTableManager->oTableEditor->AllowEdit() || 'cms_tpl_page' === $this->oTableManager->oTableConf->fieldName) {
                $this->oTableManager->RefreshLock();
            }
        }

        return true;
    }

    /**
     * checks if the record is currently locked by another editor.
     *
     * @return TdbCmsLock - returns mixed - false if no lock was found
     *                    and lock record if found
     */
    protected function IsRecordLocked()
    {
        if (!array_key_exists('oCmsLock', $this->data)) {
            $this->data['oCmsLock'] = $this->oTableManager->IsRecordLocked();
            if (false !== $this->data['oCmsLock']) {
                $this->data['isReadOnly'] = true;
            }
        }

        return $this->data['oCmsLock'];
    }

    /**
     * ajax call to check if a record is locked by user locking or a workflow transaction.
     *
     * usage:
     * <code>
     * GetAjaxCallTransparent('<?=PATH_CMS_CONTROLLER?>?pagedef=tableeditor&tableid=<?=$tableID?>&id='+id+'&_rmhist=false&module_fnc[contentmodule]=ExecuteAjaxCall&_fnc=IsRecordLockedAjax',MyCallback);
     *
     * function MyCallback(data) {
     *   if(data == true) alert('locked!');
     * }
     * </code>
     *
     * @return bool
     */
    public function IsRecordLockedAjax()
    {
        return false !== $this->IsRecordLocked();
    }

    /**
     * return true if this is a only-one-record-edit window request OR the table
     * was set to only-one-record.
     *
     * @return bool
     */
    protected function IsOnlyOneRecordTableRequest()
    {
        $bOnlyOneRecord = ('true' === $this->global->GetUserData('bOnlyOneRecord'));
        $bOnlyOneRecord = ($bOnlyOneRecord || ('1' == $this->oTableManager->oTableConf->sqlData['only_one_record_tbl']));

        return $bOnlyOneRecord;
    }

    /**
     * @param string|null $fieldName
     * @param string|null $state
     */
    public function changeListFieldState($fieldName = null, $state = null)
    {
        $inputFilterUtil = $this->getInputFilterUtil();
        if (null === $fieldName) {
            $fieldName = $inputFilterUtil->getFilteredInput('fieldname');
        }
        if (null === $state) {
            $state = $inputFilterUtil->getFilteredInput('state');
        }
        /** @var TTableEditorListFieldState $stateContainer */
        $stateContainer = ServiceLocator::get('cmsPkgCore.tableEditorListFieldState');
        $stateContainer->setState($this->oTableManager->oTableConf->sqlData['name'], $fieldName, $state);
    }

    /**
     * @return InputFilterUtilInterface
     */
    private function getInputFilterUtil()
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }

    private function getBreadcrumbService(): BackendBreadcrumbServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.service.backend_breadcrumb');
    }

    private function getLanguageService(): LanguageServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.language_service');
    }

    private function getRedirectService(): ICmsCoreRedirect
    {
        return ServiceLocator::get('chameleon_system_core.redirect');
    }
}

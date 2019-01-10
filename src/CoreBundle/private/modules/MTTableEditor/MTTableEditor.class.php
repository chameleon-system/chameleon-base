<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Request;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;

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
    protected $oTableManager = null;

    /**
     * record ID.
     *
     * @var string
     */
    protected $sId = null;

    /**
     * cms_tbl_conf ID.
     *
     * @var string
     */
    protected $sTableId = null;

    /**
     * definition of the table.
     *
     * @var TCMSTableConf
     */
    protected $oTableConf = null;

    /**
     * target pagedef to redirect after method execution.
     *
     * @var string
     */
    protected $redirectPagedef = null;

    /**
     * array of methods where a call is allowed without edit rights to the table data.
     *
     * @var array
     */
    protected $readOnlyMethods = array();

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
    protected $aMessages = array();

    /**
     * base language for translations.
     *
     * @var TdbCmsLanguage
     */
    protected $oBaseLanguage = null;

    private $fillEmptyFromLanguageId = null;

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
        $activeLanguagePrefix = TGlobal::GetLanguagePrefix(TGlobal::GetActiveLanguageId());
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
        $bIsCopy = false;
        $aModuleFunctions = $oGlobal->GetUserData('module_fnc');
        if (is_array($aModuleFunctions)
            && array_key_exists('contentmodule', $aModuleFunctions)
            && 'Copy' === $aModuleFunctions['contentmodule']) {
            $bIsCopy = true;
        }
        if ($this->oTableManager->Init($oGlobal->GetUserData('tableid'), $sId)) {
            /** @var $oConfig TdbCmsConfig */
            $oConfig = TCMSConfig::GetInstance();
            $oBaseLanguage = $oConfig->GetFieldTranslationBaseLanguage();
            $this->oBaseLanguage = $oBaseLanguage;

            if ($this->IsOnlyOneRecordTableRequest()) {
                $this->data['only_one_record_tbl'] = '1';
                $this->oTableManager->oTableConf->sqlData['only_one_record_tbl'] = '1';
                $this->oTableManager->oTableEditor->oTableConf->sqlData['only_one_record_tbl'] = '1';
            } else {
                $this->data['only_one_record_tbl'] = '0';
            }
            $this->bIsReadOnlyMode = $this->oTableManager->oTableEditor->IsRecordInReadOnlyMode();
            $bUserHasReadOnlyRight = $this->oTableManager->oTableEditor->AllowReadOnly();

            // check rights
            $bIsReadOnlyRequest = $this->IsReadOnlyRequest();
            if (empty($this->sId)) {
                $bIsInsert = true;
            } else {
                $bIsInsert = false;
            }
            $bUserHasEditRight = $this->oTableManager->oTableEditor->AllowEdit();

            if (!$bIsReadOnlyRequest && ((!$bUserHasEditRight && !$bIsInsert && !$this->bIsReadOnlyMode) || ($this->bIsReadOnlyMode && !$bUserHasReadOnlyRight))) {
                $oCMSUser = &TCMSUser::GetActiveUser();
                $oCMSUser->Logout();
                $this->controller->HeaderURLRedirect(PATH_CMS_CONTROLLER);
            }

            $this->data['oTabs'] = $this->GetTabsForTable();
        } else { // record is missing - redirect to home
            if (!$bIsCopy && !empty($this->sId) || empty($this->sId)) {
                $sModuleName = get_class($this);
                $sTableName = $this->oTableManager->oTableConf->GetName();
                $sID = $this->sId;
                if (empty($sID)) {
                    $sID = TGlobal::Translate('chameleon_system_core.cms_module_table_editor.no_id_set');
                }
                $this->data['errorMessage'] = TGlobal::Translate('chameleon_system_core.cms_module_table_editor.error_record_missing', array('%id%' => $sID, '%tableName%' => $sTableName));
                $this->SetTemplate($sModuleName, 'error');
            }
        }

        $this->AddURLHistory();
    }

    /**
     * loads workflow relevant data.
     *
     * @deprecated since 6.2.0 - workflow is not supported anymore
     */
    protected function LoadWorkflowData()
    {
    }

    /**
     * sets an array of methods where calling is allowed without edit rights to the table.
     */
    protected function DefineReadOnlyMethods()
    {
        $readOnlyMethods = array('GetName', 'GetDisplayValue', 'IsRecordLockedAjax');
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
            if ($this->global->UserDataExists('popLastURL')) {
                $this->global->GetURLHistory()->PopURL();
            }

            $params = array();
            $params['pagedef'] = $this->global->GetUserData('pagedef');
            $params['id'] = $this->oTableManager->sId;
            $params['tableid'] = $this->oTableManager->sTableId;

            $aAdditionalParams = $this->GetHiddenFieldsHook();
            if (is_array($aAdditionalParams) && count($aAdditionalParams) > 0) {
                $params = array_merge($params, $aAdditionalParams);
            }

            $sRecordName = '';
            if (null !== $this->oTableManager->oTableEditor->oTable) {
                $sRecordName = $this->oTableManager->oTableEditor->oTable->GetName();
            }
            $this->global->GetURLHistory()->AddItem($params, $sRecordName);
        }
    }

    public function &Execute()
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
            $this->LoadRevisionData();

            $this->data['oBaseLanguage'] = $this->oBaseLanguage;

            $this->data['breadcrumb'] = $this->global->GetURLHistory()->GetBreadcrumb(true);
            if ($this->isEditFieldMode()) {
                $editFieldName = $this->global->GetUserData('_fieldName');
                $this->oTableManager->oTableEditor->setActiveEditField($editFieldName);
                $this->data['oField'] = &$this->oTableManager->oTableConf->GetField($editFieldName, $oTable);
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
                    $aPostBlackList = array('pagedef', 'tableid', 'id', 'referer_id', 'referer_table', '_fnc', 'module_fnc', '_noModuleFunction');
                    foreach ($aPostBlackList as $forbiddenPostKey) {
                        unset($aPostData[$forbiddenPostKey]);
                    }

                    $oPostTable->sqlData = array_merge($oPostTable->sqlData, $aPostData);

                    $oFields = $this->oTableManager->oTableEditor->oTableConf->GetFields($oPostTable);
                    while ($oField = &$oFields->Next()) {
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
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack')->getCurrentRequest();
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
            $aMessages = array();
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

                $aMessage = array(
                    'sMessage' => $sMessage,
                    'sMessageType' => $sMessageType,
                );

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
     * loads revision management relevant data if active.
     */
    protected function LoadRevisionData()
    {
        $this->data['bRevisionManagementActive'] = false;
        $bRevisionManagementActive = $this->oTableManager->IsRevisionManagementActive();
        if ($bRevisionManagementActive) {
            $this->data['bRevisionManagementActive'] = $bRevisionManagementActive;
            $sLastRevisionNumber = $this->GetLastRevisionNumber();
            $this->data['iLastRevisionNumber'] = $sLastRevisionNumber;
            $iBaseRevisionNumber = $this->oTableManager->oTableEditor->GetLastActivatedRevision();
            $this->data['iBaseRevisionNumber'] = $iBaseRevisionNumber;
            $this->data['oLastRevision'] = $this->oTableManager->oTableEditor->GetLastActivatedRevisionObject();
        }
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
        $permissions = array('new' => false, 'edit' => false, 'delete' => false, 'showlist' => false);

        $permissions['edit'] = $this->global->oUser->oAccessManager->HasEditPermission($this->oTableManager->oTableConf->sqlData['name']);
        $tableInUserGroup = $this->global->oUser->oAccessManager->user->IsInGroups($this->oTableManager->oTableConf->sqlData['cms_usergroup_id']);
        if ($tableInUserGroup) {
            $permissions['showlist'] = true;
            if ($this->IsOnlyOneRecordTableRequest()) {
                $permissions['new'] = false;
                $permissions['delete'] = false;
            } else {
                $permissions['new'] = $this->global->oUser->oAccessManager->HasNewPermission($this->oTableManager->oTableConf->sqlData['name']);
                $permissions['delete'] = $this->global->oUser->oAccessManager->HasDeletePermission($this->oTableManager->oTableConf->sqlData['name']);
            }
        }
        $this->data['aPermission'] = $permissions;
    }

    public function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = array('Save', 'Insert', 'Delete', 'AjaxDelete', 'Copy', 'DatabaseCopy', 'AjaxSave', 'AjaxSaveField', 'AjaxGetFieldEditBox', 'AjaxGetPreviewURL', 'PublishViaAjax', 'RefreshLock', 'AddNewRevision', 'ActivateRevision', 'IsRecordLockedAjax');
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
        $this->methodCallAllowed[] = 'changeListFieldState';
        $this->methodCallAllowed[] = 'setFillEmptyFromLanguageId';
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        // first the includes that are needed for all fields
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery/jQueryUI/ui.core.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery-form-4.2.2/jquery.form.min.js').'" type="text/javascript"></script>'; // ajax form plugin
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery/flash/flash.js').'" type="text/javascript"></script>';

        // right click contextmenu
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery/contextmenu/contextmenu.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<link href="'.TGlobal::GetPathTheme().'/css/contextmenu.css" rel="stylesheet" type="text/css" />';

        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/cms.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<link href="'.TGlobal::GetPathTheme().'/css/tableeditcontainer.css" rel="stylesheet" type="text/css" />';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/tableEditor.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery/WayfarerTooltip/WayfarerTooltip.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<link href="'.TGlobal::GetPathTheme().'/css/tooltip.css" rel="stylesheet" type="text/css" />';
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
            CHAMELEON.CORE.hideProcessingDialog();
            return \''.TGlobal::Translate('chameleon_system_core.cms_module_table_editor.confirm_discard_changes').'\';
          }
        }

      </script>';

        // get the tableEditor specific head includes
        $aTableEditIncludes = $this->oTableManager->oTableEditor->GetHtmlHeadIncludes();
        $aIncludes = array_merge($aIncludes, $aTableEditIncludes);

        // get the head includes for all the fields...
        if ($this->global->UserDataExists('_fnc') && 'editfield' === $this->global->GetUserData('_fnc')) {
            $editFieldName = $this->global->GetUserData('_fieldName');
            $oField = &$this->oTableManager->oTableConf->GetField($editFieldName, $this->oTableManager->oTableEditor->oTable);
            if (null !== $oField) {
                $aFieldIncludes = $oField->GetCMSHtmlHeadIncludes();
                $aIncludes = array_merge($aIncludes, $aFieldIncludes);
            }
        } else {
            $oFields = &$this->oTableManager->oTableConf->GetFields($this->oTableManager->oTableEditor->oTable);
            while ($oField = $oFields->Next()) {
                /* @var $oField TCMSField */
                $aFieldIncludes = $oField->GetCMSHtmlHeadIncludes();
                $aIncludes = array_merge($aIncludes, $aFieldIncludes);
            }
        }

        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib().'/javascript/jquery/jQueryUI/ui.dialog.js" type="text/javascript"></script>';
        $aIncludes[] = '<link href="'.TGlobal::GetStaticURLToWebLib().'/javascript/jquery/jQueryUI/themes/cupertino/cupertino.css" media="screen" rel="stylesheet" type="text/css" />';

        return $aIncludes;
    }

    public function GetHtmlFooterIncludes()
    {
        $aIncludes = array();
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/components/select2.v4/js/select2.full.min.js').'" type="text/javascript"></script>';

        // get the tableEditor specific footer includes
        $aTableEditIncludes = $this->oTableManager->oTableEditor->GetHtmlFooterIncludes();
        $aIncludes = array_merge($aIncludes, $aTableEditIncludes);

        if ($this->global->UserDataExists('_fnc') && 'editfield' === $this->global->GetUserData('_fnc')) {
            $editFieldName = $this->global->GetUserData('_fieldName');
            $oField = &$this->oTableManager->oTableConf->GetField($editFieldName, $this->oTableManager->oTableEditor->oTable);
            if (null !== $oField) {
                $aFieldIncludes = $oField->GetCMSHtmlFooterIncludes();
                $aIncludes = array_merge($aIncludes, $aFieldIncludes);
            }
        } else {
            $oFields = &$this->oTableManager->oTableConf->GetFields($this->oTableManager->oTableEditor->oTable);
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
            $oField = &$this->oTableManager->oTableConf->GetField($sFieldName, $this->oTableManager->oTableEditor->oTable);
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
     * @param string $sFunctionName    - method name
     * @param array  $aMethodParameter - parameters to pass to the method
     *
     * @return mixed
     */
    public function &_CallMethod($sFunctionName, $aMethodParameter = array())
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
                $oMessageManager->AddMessage($sConsumerName, 'TABLEEDITOR_SAVE_SUCCESS', array('sRecordName' => $oRecordData->name));
                $bSaveSuccessfull = true;
            } else {
                $bSaveSuccessfull = false;
            }

            if ($bSaveSuccessfull && !$bPreventRedirect) {
                $parameter = array('pagedef' => $this->global->GetUserData('pagedef'), 'tableid' => $this->oTableManager->sTableId, 'id' => $this->oTableManager->sId);

                $aAdditionalParams = $this->GetHiddenFieldsHook();
                if (is_array($aAdditionalParams) && count($aAdditionalParams) > 0) {
                    $parameter = array_merge($parameter, $aAdditionalParams);
                }
                $this->controller->HeaderRedirect($parameter);
            } else {
                $this->LoadMessages(true);
            }
        } else {
            $this->LoadMessages(true);
        }

        return $bSaveSuccessfull;
    }

    /**
     * add new record revision using the postdata
     * executes Save() before saving the revision.
     */
    public function AddNewRevision()
    {
        $postData = $this->global->GetUserData(null);
        $this->oTableManager->AddNewRevision($postData);
    }

    public function ActivateRevision()
    {
        $sRecordRevisionId = $this->global->GetUserData('sRecordRevisionId');
        if (!empty($sRecordRevisionId) && $this->oTableManager->oTableEditor->AllowEdit()) {
            $this->oTableManager->ActivateRecordRevision($sRecordRevisionId);
        }

        $parameter = array('pagedef' => $this->global->GetUserData('pagedef'), 'tableid' => $this->oTableManager->sTableId, 'id' => $this->oTableManager->sId);

        $aAdditionalParams = $this->GetHiddenFieldsHook();
        if (is_array($aAdditionalParams) && count($aAdditionalParams) > 0) {
            $parameter = array_merge($parameter, $aAdditionalParams);
        }

        $this->controller->HeaderRedirect($parameter);
    }

    /**
     * publishes workflow changes.
     *
     * @deprecated since 6.2.0 - workflow is not supported anymore
     */
    public function PublishViaAjax()
    {
        return false;
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
        $oField = &$this->oTableManager->oTableConf->GetField($editFieldName, $this->oTableManager->oTableEditor->oTable);
        $result = array('field' => $editFieldName, 'content' => $oField->GetContent());

        return $result;
    }

    /**
     * saves table via ajax.
     *
     * @return TCMSstdClass
     */
    public function AjaxSave()
    {
        $oRecordData = false;
        if ($this->oTableManager->oTableEditor->AllowEdit()) {
            $postData = $this->global->GetUserData(null);
            $oRecordData = $this->oTableManager->Save($postData);

            $oMessageManager = TCMSMessageManager::GetInstance();
            $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;

            if (false !== $oRecordData) {
                $sName = '';
                if (isset($this->oTableManager->oTableEditor->oTable) && null !== $this->oTableManager->oTableEditor->oTable) {
                    $sName = $this->oTableManager->oTableEditor->oTable->GetName();
                } elseif (isset($postData['name'])) {
                    $sName = $postData['name'];
                }
                $oMessageManager->AddMessage($sConsumerName, 'TABLEEDITOR_SAVE_SUCCESS', array('sRecordName' => $sName));
            }

            $this->LoadMessages();
            if (false == $oRecordData || null === $oRecordData) {
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
        if ($this->oTableManager->oTableEditor->AllowEdit()) {
            $editFieldName = $this->global->GetUserData('_fieldName');
            $fieldValue = $this->global->GetUserData($editFieldName);
            $this->oTableManager->SaveField($editFieldName, $fieldValue);
            $contentFormatted = $this->getFormattedFieldContent($editFieldName, $fieldValue);

            $result = array('success' => true, 'fieldname' => $editFieldName, 'content' => $fieldValue, 'contentFormatted' => $contentFormatted);
        } else {
            $result = array('success' => false);
        }

        return $result;
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
        $postData = array(
            'id' => $this->sId,
            $editFieldName => $editFieldValue,
        );
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
            $this->global->GetURLHistory()->PopURL();

            $parameters = array(
                'pagedef' => $inputFilterUtil->getFilteredInput('pagedef'),
                'tableid' => $this->oTableManager->sTableId,
                'id' => $this->oTableManager->sId,
            );
            if ('true' === $inputFilterUtil->getFilteredInput('bOnlyOneRecord')) {
                $parameters['bOnlyOneRecord'] = 'true';
            }

            $additionalParams = $this->GetHiddenFieldsHook();
            if (is_array($additionalParams) && count($additionalParams) > 0) {
                $parameters = array_merge($parameters, $additionalParams);
            }
            $this->controller->HeaderRedirect($parameters);
        } else {
            $sModuleName = get_class($this);
            $sTableName = $this->oTableManager->oTableConf->GetName();
            $this->data['errorMessage'] = TGlobal::Translate('chameleon_system_core.cms_module_table_editor.error_unable_to_create_new_record', array('%id%' => $this->sId, '%tableName%' => $sTableName));
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

        if (!$bPreventRedirect) {
            $inputFilterUtil = $this->getInputFilterUtil();
            $isInIFrame = $inputFilterUtil->getFilteredInput('_isiniframe');
            $parentURL = '';

            $parameter = array();
            $parameter['_isiniframe'] = $isInIFrame;
            $parameter['id'] = $this->oTableManager->sTableId;

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

                    $this->global->GetURLHistory()->PopURL();
                    $this->controller->HeaderRedirect($parameter);
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

                    $this->global->GetURLHistory()->PopURL();
                    $this->controller->HeaderRedirect($parameter);
                }
            } else {
                //remove last item from url history
                $this->global->GetURLHistory()->PopURL();
                //search for id in the url we want to redirect now
                //if the id equals to the current id of the record that is deleted remove this item too
                while (preg_match('#id='.$this->sId.'#', $this->global->GetURLHistory()->GetURL()) > 0) {
                    $this->global->GetURLHistory()->PopURL();
                }
                $parentURL = $this->global->GetURLHistory()->GetURL().'&_histid='.($this->global->GetURLHistory()->index - 1);
            }

            if (!empty($parentURL)) {
                $this->controller->HeaderURLRedirect($parentURL);
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
        $this->global->GetURLHistory()->PopURL();

        if ('_mlt' === substr($this->oTableManager->sRestrictionField, -4)) {
            $targetTable = $this->oTableManager->oTableConf->sqlData['name'];
            $sourceTable = substr($this->oTableManager->sRestrictionField, 0, -4);
            $MLTTable = $sourceTable.'_'.$targetTable.'_mlt';
            $mltQuery = 'INSERT INTO `'.MySqlLegacySupport::getInstance()->real_escape_string($MLTTable)."` SET `source_id` ='".MySqlLegacySupport::getInstance()->real_escape_string($this->oTableManager->sRestriction)."', `target_id` = '{$this->oTableManager->sId}'";
            MySqlLegacySupport::getInstance()->query($mltQuery);
        }

        $parameter = array('pagedef' => $this->global->GetUserData('pagedef'), 'tableid' => $this->oTableManager->sTableId, 'id' => $this->oTableManager->sId);

        $aAdditionalParams = $this->GetHiddenFieldsHook();
        if (is_array($aAdditionalParams) && count($aAdditionalParams) > 0) {
            $parameter = array_merge($parameter, $aAdditionalParams);
        }

        $this->controller->HeaderRedirect($parameter);
    }

    /**
     * copy a record using data from database.
     */
    public function DatabaseCopy()
    {
        $this->oTableManager->DatabaseCopy(false, array(), true);
        $this->global->GetURLHistory()->PopURL();

        if ('_mlt' === substr($this->oTableManager->sRestrictionField, -4)) {
            $targetTable = $this->oTableManager->oTableConf->sqlData['name'];
            $sourceTable = substr($this->oTableManager->sRestrictionField, 0, -4);
            $MLTTable = $sourceTable.'_'.$targetTable.'_mlt';
            $mltQuery = 'INSERT INTO `'.MySqlLegacySupport::getInstance()->real_escape_string($MLTTable)."` SET `source_id` ='".MySqlLegacySupport::getInstance()->real_escape_string($this->oTableManager->sRestriction)."', `target_id` = '{$this->oTableManager->sId}'";
            MySqlLegacySupport::getInstance()->query($mltQuery);
        }

        $parameters = array(
            'pagedef' => $this->global->GetUserData('pagedef'),
            'tableid' => $this->oTableManager->sTableId,
            'id' => $this->oTableManager->sId,
            'copyState' => 'copied',
        );

        $additionalParameters = $this->GetHiddenFieldsHook();
        if (is_array($additionalParameters) && count($additionalParameters) > 0) {
            $parameters = array_merge($parameters, $additionalParameters);
        }

        $this->controller->HeaderRedirect($parameters);
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
     * checks for the last revision number for this record,
     * if no revisions are found it returns 0.
     *
     * @return int
     */
    protected function GetLastRevisionNumber()
    {
        $iLastRevisionNumber = $this->oTableManager->oTableEditor->GetLastRevisionNumber();

        return $iLastRevisionNumber;
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
     * translates field contents using Microsoft Translator.
     *
     * @deprecated since 6.2.0 - translation service is no longer supported.
     */
    public function TranslateString()
    {
        if ($this->global->UserDataExists('txt')) {
            return $this->global->GetUserData('txt');
        }

        return false;
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
        $stateContainer = \ChameleonSystem\CoreBundle\ServiceLocator::get('cmsPkgCore.tableEditorListFieldState');
        $stateContainer->setState($this->oTableManager->oTableConf->sqlData['name'], $fieldName, $state);
    }

    /**
     * @return InputFilterUtilInterface
     */
    private function getInputFilterUtil()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.input_filter');
    }
}

<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CmsBackendBundle\BackendSession\BackendSessionInterface;
use ChameleonSystem\CoreBundle\CoreEvents;
use ChameleonSystem\CoreBundle\DataModel\CommentDataModel;
use ChameleonSystem\CoreBundle\Event\RecordChangeEvent;
use ChameleonSystem\CoreBundle\Exception\GuidCreationFailedException;
use ChameleonSystem\CoreBundle\Interfaces\FlashMessageServiceInterface;
use ChameleonSystem\CoreBundle\Interfaces\GuidCreationServiceInterface;
use ChameleonSystem\CoreBundle\Service\DeletedFieldsServiceInterface;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\DatabaseMigration\DataModel\LogChangeDataModel;
use ChameleonSystem\DatabaseMigration\Query\MigrationQueryData;
use ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\Recorder\MigrationRecorderStateHandler;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;
use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;
use Doctrine\DBAL\Connection;
use esono\pkgCmsCache\CacheInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * manages saving, inserting, and deleting data from a table.
 * /**/
class TCMSTableEditorEndPoint
{
    /**
     * @deprecated since the use for this blacklist handling was removed when the revision logic was removed. Will be remove in 7.2
     * session variable name for black listed records and tables which should not be deleted
     * this is needed to prevent recursive deletes with DeleteRecordReferences.
     */
    public const DELETE_BLACKLIST_SESSION_VAR = 'aDeleteBlacklist';

    /**
     * pointer to the oTableConf item.
     *
     * @var TdbCmsTblConf
     */
    public $oTableConf;

    /**
     * pointer to the table data.
     *
     * @var TCMSRecord
     */
    public $oTable;

    /**
     * the original data of the row before an edit overwrites the data.
     *
     * @var TCMSRecord
     */
    public $oTablePreChangeData;

    /**
     * record ID.
     *
     * @var string
     */
    public $sId;

    /**
     * holds the source ID AFTER a copy was performed (so the source can be used by the post copy functions).
     *
     * @var string
     */
    protected $sSourceId;

    /**
     * cms_tbl_conf ID.
     *
     * @var string
     */
    public $sTableId;

    public $sRestriction;

    public $sRestrictionField;

    /**
     * an iterator of the menu items for the table (save, new, etc).
     *
     * @var TIterator
     */
    protected $oMenuItems;

    /**
     * switch to prevent copy, new and delete buttons.
     *
     * @var bool
     */
    protected $editOnly = false;

    /**
     * if set to true, no user access rights will be checked.
     *
     * @var bool
     */
    protected $bAllowEditByAll = false;

    /**
     * per default hidden, edit-on-click and readonly-if-filled fieldtype are ignored on save,
     * to prevent resetting them to empty content if they are missing or empty in postdata (user saved via MTTableEditor)
     * set this to true if you want to save data manually and postdata is the full record.
     *
     * @var bool
     */
    protected $bForceHiddenFieldWriteOnSave = false;

    /**
     * set to true via AllowEditByWebUser() method if you want to save user records from a web module.
     *
     * @var bool
     */
    protected $bAllowEditByWebUser = false;

    /**
     * if set to true, any delete checks are ignored for the item.
     *
     * @var bool
     */
    protected $bAllowDeleteByAll = false;

    /**
     * array of methods that may be called from modules via ajax.
     *
     * @var array
     */
    public $methodCallAllowed = [];

    /**
     * if true the PostSaveHook function for fields wont be execute.
     * So we can prevent do spezial things on saving fields.
     *
     * @var bool
     */
    public $bPreventPostSaveHookOnFields = false;

    /**
     * if true the PreGetSQLHook function for fields wont be execute.
     * So we can prevent do spezial things on saving fields.
     *
     * @var bool
     */
    public $bPreventPreGetSQLHookOnFields = false;

    /**
     * holds a copy of the source table on databasecopy.
     *
     * @var TCMSRecord
     */
    public $oSourceTable;

    /**
     * calls GetDatabaseCopySQL instead of GetSQL in OnAfterCopy() method if true.
     *
     * @var bool
     */
    protected $bIsDatabaseCopy = false;

    /**
     * If set to true copy all languages on a new data base copy in OnAfterCopy().
     */
    protected $bIsCopyAllLanguageValues = false;

    /**
     * This was set at the save method. Because somtimes we need this info in PostSaveHook().
     *
     * @var bool
     */
    protected $bSaveDataIsSqlData = false;

    /**
     * indicates that the record was an update and not an initial insert
     * note: is set in WriteDataToDatabase.
     */
    protected $bIsUpdateCall = false;

    /**
     * @var Connection
     */
    private $databaseConnection;

    /**
     * current edit-on-click fieldname.
     *
     * @var string|null
     */
    private $activeEditField;

    /**
     * initalises the table editor object.
     *
     * @param string $sTableId
     * @param string $sId
     * @param string $sLanguageID - overwrites the user language and loads the record in this language instead
     */
    public function Init($sTableId, $sId = null, $sLanguageID = null)
    {
        $this->sId = $sId;
        $this->sTableId = $sTableId;
        /** @var $oCmsTblConf TdbCmsTblConf */
        $oCmsTblConf = TdbCmsTblConf::GetNewInstance();
        if (is_null($sLanguageID)) {
            /** @var BackendSessionInterface $backendSession */
            $backendSession = ServiceLocator::get('chameleon_system_cms_backend.backend_session');
            $oCmsTblConf->SetLanguage($backendSession->getCurrentEditLanguageId());
        } else {
            $oCmsTblConf->SetLanguage($sLanguageID);
        }
        $oCmsTblConf->Load($this->sTableId);
        $this->oTableConf = $oCmsTblConf;
        $this->LoadDataFromDatabase();
    }

    /**
     * set public methods here that may be called from outside.
     *
     * @return void
     */
    public function DefineInterface()
    {
        $this->methodCallAllowed = ['GetDisplayValue', 'GetTransactionOwnership', 'AddNewRevision'];
    }

    /**
     * use this to change the "allow edit by all" setting.
     *
     * @param bool $bSetting
     */
    public function AllowEditByAll($bSetting)
    {
        $this->bAllowEditByAll = $bSetting;
    }

    /**
     * use this method to allow external delete calls without checking user rights (as may be required when
     * an external delete is called)
     * IMPORTANT: will also set bAllowEditByAll.
     *
     * @param bool $bAllowDeleteByAll
     */
    public function AllowDeleteByAll($bAllowDeleteByAll = true)
    {
        $this->bAllowDeleteByAll = $bAllowDeleteByAll;
        $this->AllowEditByAll($bAllowDeleteByAll);
    }

    /**
     * set to true if you want to save user records from a web module
     * you know what you are doing, right?
     *
     * @param bool $bAllowEditByWebUser
     */
    public function AllowEditByWebUser($bAllowEditByWebUser)
    {
        $this->bAllowEditByWebUser = $bAllowEditByWebUser;
    }

    /**
     * if true, fields in hidden mode are also updated.
     *
     * @param bool $bForceHiddenFieldWriteOnSave
     */
    public function ForceHiddenFieldWriteOnSave($bForceHiddenFieldWriteOnSave = true)
    {
        $this->bForceHiddenFieldWriteOnSave = $bForceHiddenFieldWriteOnSave;
    }

    /**
     * here you can add checks to validate the data and prevent saving.
     *
     * @param array $postData - raw post data (e.g. datetime fields are splitted into 2 post values and in non sql format)
     * @param TIterator $oFields - TIterator of TCMSField objects
     *
     * @return bool
     */
    protected function DataIsValid($postData, $oFields = null)
    {
        $bDataValid = true;
        if (!is_null($oFields)) {
            /** @var $oField TCMSField */
            while ($oField = $oFields->Next()) {
                if ('hidden' != $oField->oDefinition->sqlData['modifier'] && !$oField->DataIsValid()) {
                    $bDataValid = false;
                }
            }

            $oFields->GoToStart();
        }

        return $bDataValid;
    }

    /**
     * returns true if the current cms user is the owner of the record.
     *
     * @param array $aPostData - used if $this->oTable is null (happens on insert)
     *
     * @return bool
     */
    public function IsOwner($aPostData = null)
    {
        if ($this->bAllowEditByAll) {
            return true;
        }
        $bIsOwner = false;
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        $userId = $securityHelper->getUser()?->getId();

        if (null !== $userId && !is_null($this->oTable) && is_array($this->oTable->sqlData)) {
            $bIsOwner = (array_key_exists('cms_user_id', $this->oTable->sqlData) && $this->oTable->sqlData['cms_user_id'] == $userId);
        } elseif (is_array($aPostData) && null !== $userId) {
            $recUserId = null;
            if (array_key_exists('cms_user_id', $aPostData)) {
                $recUserId = $aPostData['cms_user_id'];
            }
            $bIsOwner = ($recUserId === $userId);
        }

        return $bIsOwner;
    }

    /**
     * return true if the current user has the right to edit the table.
     *
     * @param array $postData - if oTable is null, and we have postData, use it instead. happens on inserts
     *
     * @return bool
     */
    public function AllowEdit($postData = null)
    {
        if ($this->bAllowEditByAll) {
            return true;
        }

        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        if (null !== $this->oTable) {
            return $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT, $this->oTable);
        }

        return $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT, $this->oTableConf->sqlData['name']);
    }

    /**
     * returns true if the current user has the right to see the record in readonly mode.
     *
     * @param array $postData - if oTable is null, and we have postData, use it instead. happens on inserts
     *
     * @return bool
     */
    public function AllowReadOnly($postData = null)
    {
        if ($this->bAllowEditByAll) {
            return true;
        }
        if ($this->AllowEdit($postData)) {
            return true;
        }

        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        if (null !== $this->oTable) {
            return $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_ACCESS, $this->oTableConf->fieldName);
        }

        return $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_ACCESS, $this->oTable);
    }

    /**
     * returns an iterator with the menuitems for the current table. if you want to add your own
     * items, overwrite the GetCustomMenuItems (NOT GetMenuItems)
     * the iterator will always be reset to start.
     *
     * todo [refactor]: method is way to long and deeply nested
     *
     * @return TIterator
     */
    public function GetMenuItems()
    {
        if (null !== $this->getActiveEditField()) {
            $this->oMenuItems = $this->getMenuButtonsForFieldEditor();

            return $this->oMenuItems;
        }

        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        if (false === $this->IsRecordInReadOnlyMode() && true === $this->AllowReadOnly()) {
            if (is_null($this->oMenuItems)) {
                $this->oMenuItems = new TIterator();
                // std menuitems...

                if ($this->AllowEdit()) {
                    $oMenuItem = new TCMSTableEditorMenuItem();
                    $oMenuItem->sItemKey = 'save';
                    $oMenuItem->sDisplayName = ServiceLocator::get('translator')->trans('chameleon_system_core.action.save');
                    $oMenuItem->sIcon = 'fas fa-save';

                    $sOnSaveViaAjaxHookMethods = '';
                    /** @var $oFields TIterator */
                    $oFields = $this->oTableConf->GetFields($this->oTable);
                    $oFields->GoToStart();
                    /** @var $oField TCMSField */
                    while ($oField = $oFields->Next()) {
                        $sFieldDisplayType = $oField->GetDisplayType();
                        if ('none' == $sFieldDisplayType) {
                            $sOnSaveViaAjaxHookMethods .= $oField->getOnSaveViaAjaxHookMethod();
                        }
                    }
                    $oMenuItem->sOnClick = $sOnSaveViaAjaxHookMethods.'SaveViaAjax();return false;';

                    $this->oMenuItems->AddItem($oMenuItem);
                }

                if (1 != $this->oTableConf->sqlData['only_one_record_tbl'] && true !== $this->editOnly) {
                    if ($securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_NEW, $this->oTableConf->sqlData['name'])) {
                        // copy
                        $oMenuItem = new TCMSTableEditorMenuItem();
                        $oMenuItem->sItemKey = 'copy';
                        $oMenuItem->sDisplayName = ServiceLocator::get('translator')->trans('chameleon_system_core.action.copy');
                        $oMenuItem->sIcon = 'far fa-clone';
                        $oMenuItem->sOnClick = "if(confirm('".TGlobalBase::OutJS(ServiceLocator::get('translator')->trans('chameleon_system_core.action.confirm_copy'))."')){ExecutePostCommand('DatabaseCopy');}";
                        $this->oMenuItems->AddItem($oMenuItem);

                        // new
                        $oMenuItem = new TCMSTableEditorMenuItem();
                        $oMenuItem->sItemKey = 'new';
                        $oMenuItem->sDisplayName = ServiceLocator::get('translator')->trans('chameleon_system_core.action.new');
                        $oMenuItem->sIcon = 'fas fa-plus';
                        $oMenuItem->sOnClick = "ExecutePostCommand('Insert');";
                        $this->oMenuItems->AddItem($oMenuItem);
                    }

                    // delete
                    if (null !== $this->oTable) {
                        $allowDelete = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_DELETE, $this->oTable);
                    } else {
                        $allowDelete = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_DELETE, $this->oTableConf->sqlData['name']);
                    }
                    if ($allowDelete) {
                        $oMenuItem = new TCMSTableEditorMenuItem();
                        $oMenuItem->sItemKey = 'delete';
                        $oMenuItem->sDisplayName = ServiceLocator::get('translator')->trans('chameleon_system_core.action.delete');
                        $oMenuItem->sIcon = 'far fa-trash-alt';
                        $oMenuItem->sOnClick = 'DeleteRecord();';
                        $oMenuItem->setButtonStyle('btn-danger');
                        $this->oMenuItems->AddItem($oMenuItem);
                    }

                    // preview button
                    if (1 == $this->oTableConf->sqlData['show_previewbutton']) {
                        $oMenuItem = new TCMSTableEditorMenuItem();
                        $oMenuItem->sItemKey = 'previewPage';
                        $oMenuItem->sDisplayName = ServiceLocator::get('translator')->trans('chameleon_system_core.action.preview');
                        $oMenuItem->sIcon = 'far fa-eye';

                        $ajaxURL = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript(['pagedef' => 'tableeditor', 'id' => $this->sId, 'tableid' => $this->oTableConf->id, 'module_fnc' => ['contentmodule' => 'ExecuteAjaxCall'], '_fnc' => 'AjaxGetPreviewURL']);
                        $oMenuItem->sOnClick = "GetAjaxCallTransparent('".$ajaxURL."', OpenPreviewURL);";
                        $this->oMenuItems->AddItem($oMenuItem);
                    }

                    // if we have edit access to the table editor, then we also show a link to it
                    if ($securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT, 'cms_tbl_conf')) {
                        /** @var $oTableEditorConf TCMSTableConf */
                        $oTableEditorConf = new TCMSTableConf();
                        $oTableEditorConf->LoadFromField('name', 'cms_tbl_conf');
                        $oMenuItem = new TCMSTableEditorMenuItem();
                        $oMenuItem->sItemKey = 'edittableconf';
                        $oMenuItem->setTitle(ServiceLocator::get('translator')->trans('chameleon_system_core.action.open_table_configuration'));
                        $oMenuItem->sIcon = 'fas fa-cogs';
                        $oMenuItem->setButtonStyle('btn-warning');

                        $aParameter = [
                            'pagedef' => $this->getInputFilterUtil()->getFilteredGetInput('pagedef', 'tableeditor'),
                            'id' => $this->oTableConf->id,
                            'tableid' => $oTableEditorConf->id,
                        ];
                        $aAdditionalParams = $this->GetHiddenFieldsHook();
                        if (is_array($aAdditionalParams) && count($aAdditionalParams) > 0) {
                            $aParameter = array_merge($aParameter, $aAdditionalParams);
                        }

                        $oMenuItem->href = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript($aParameter);
                        $this->oMenuItems->AddItem($oMenuItem);
                    }
                    // now add custom items
                    $this->GetCustomMenuItems();
                }
            } else {
                $this->oMenuItems->GoToStart();
            }

            if (defined('ACTIVE_TRANSLATION') && ACTIVE_TRANSLATION) {
                $this->oMenuItems = $this->getTranslationMenu($this->oMenuItems);
            }
        } else {
            $this->oMenuItems = new TIterator();
            $this->GetCustomReadOnlyMenuItem();
            $this->oMenuItems->GoToStart();
        }

        return $this->oMenuItems;
    }

    /**
     * @return TIterator|null
     */
    protected function getMenuButtonsForFieldEditor()
    {
        $editField = $this->getActiveEditField();
        $field = $this->oTableConf->GetField($editField, $this->oTable);
        $this->oMenuItems = $field->getMenuButtonsForFieldEditor();

        return $this->oMenuItems;
    }

    /**
     * sets menu item with sub menu of languages to load translations from.
     *
     * @return TIterator
     */
    protected function getTranslationMenu(TIterator $menuItems)
    {
        $translatedFields = TdbCmsConfig::GetInstance()->GetListOfTranslatableFields($this->oTable->table);
        if (false === is_array($translatedFields) || 0 === count($translatedFields)) {
            return $menuItems;
        }

        $inputFilter = $this->getInputFilterUtil();

        $aParameter = [
            'pagedef' => $inputFilter->getFilteredGetInput('pagedef', 'tableeditor'),
            'id' => $this->oTable->id,
            'tableid' => $this->oTableConf->id,
            'module_fnc' => [
                TGlobal::instance()->GetExecutingModulePointer()->sModuleSpotName => 'setFillEmptyFromLanguageId',
            ],
        ];

        // get list of languages
        $oBaseLanguage = TdbCmsConfig::GetInstance()->GetFieldTranslationBaseLanguage();
        $allowedLanguages[$oBaseLanguage->fieldIso6391] = $oBaseLanguage->fieldName;
        $allowedLanguages = array_merge(
            $allowedLanguages,
            TdbCmsConfig::GetInstance()->GetFieldBasedTranslationLanguageArray()
        );

        /** @var BackendSessionInterface $backendSession */
        $backendSession = ServiceLocator::get('chameleon_system_cms_backend.backend_session');

        $currentLanguageISO = $backendSession->getCurrentEditLanguageIso6391();

        $oMenuItem = new TCMSTableEditorMenuItem();
        $oMenuItem->sItemKey = 'copy_translation';
        $oMenuItem->setTitle(ServiceLocator::get('translator')->trans('chameleon_system_core.action.translate_from_copy'));
        $oMenuItem->sIcon = TGlobal::GetPathTheme().'/images/icons/language-flags/'.strtolower($currentLanguageISO).'.png';
        $languageService = $this->getLanguageService();

        foreach ($allowedLanguages as $isoCode => $name) {
            if ($oBaseLanguage->fieldIso6391 === $isoCode) {
                continue;
            }
            $oLang = $languageService->getLanguageFromIsoCode($isoCode);
            $aParameter['languageId'] = $oLang->id;

            $oSubMenuItem = new TCMSTableEditorMenuItem();
            $oSubMenuItem->sItemKey = 'trans_'.$isoCode;
            $oSubMenuItem->setTitle($name);
            $oSubMenuItem->sIcon = TGlobal::GetPathTheme().'/images/icons/language-flags/'.strtolower(
                $isoCode
            ).'.png';
            $oSubMenuItem->sCSSClass = 'translation-sub';

            $aAdditionalParams = $this->GetHiddenFieldsHook();
            if (is_array($aAdditionalParams) && count($aAdditionalParams) > 0) {
                $aParameter = array_merge($aParameter, $aAdditionalParams);
            }
            $sFullURL = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript($aParameter);

            $text = ServiceLocator::get('translator')->trans(
                'chameleon_system_core.action.translate_from_copy_confirm',
                ['%lang%' => $name]
            );
            $oSubMenuItem->sOnClick = "if (confirm('{$text}')) {document.location.href='{$sFullURL}';}";
            $oMenuItem->addSubMenuItem($oSubMenuItem);
        }

        $this->oMenuItems->AddItem($oMenuItem);

        return $menuItems;
    }

    /**
     * adds table-specific buttons to the editor (add them directly to $this->oMenuItems).
     */
    protected function GetCustomMenuItems()
    {
    }

    /**
     * adds table-specific buttons to the editor (add them directly to $this->oMenuItems)
     * will only be loadd in read only mode instead of GetCustomMenuItems();.
     */
    protected function GetCustomReadOnlyMenuItem()
    {
    }

    /**
     * returns the url for the preview button
     * is called by MTTableEditor module via ajax
     * overwrite this to set your custom preview url.
     *
     * @return string;
     */
    public function GetPreviewURL()
    {
        $page = null;
        if (array_key_exists('cms_tpl_module_instance_id', $this->oTable->sqlData) && !empty($this->oTable->sqlData['cms_tpl_module_instance_id'])) {
            $moduleInstance = new TCMSTPLModuleInstance($this->oTable->sqlData['cms_tpl_module_instance_id']);
            $page = $moduleInstance->GetConnectedPage();
        } elseif (!empty($this->oTableConf->fieldCmsTplPageId)) {
            $page = $this->oTableConf->GetFieldCmsTplPage();
        }

        if (null === $page) {
            return '';
        }

        $portalId = $page->GetPortal()->id;
        $domainName = $this->getPortalDomainService()->getPrimaryDomain($portalId)->GetActiveDomainName();
        /** @var BackendSessionInterface $backendSession */
        $backendSession = ServiceLocator::get('chameleon_system_cms_backend.backend_session');

        $editLanguageID = $backendSession->getCurrentEditLanguageId();

        $scheme = $this->getCurrentRequest()?->getScheme();
        if (null === $scheme) {
            $scheme = 'https';
        }

        return $scheme.'://'.$domainName.'/'.PATH_CUSTOMER_FRAMEWORK_CONTROLLER.'?pagedef='.$this->sId.'&esdisablelinks=true&__previewmode=true&previewLanguageId='.$editLanguageID;
    }

    /**
     * loads the record object TCMSRecord that is edited.
     */
    protected function LoadDataFromDatabase()
    {
        if (!is_null($this->sId) && !empty($this->sId)) {
            $this->oTable = $this->GetNewTableObjectForEditor();
            $this->oTable->SetLanguage($this->oTableConf->GetLanguage());
            $this->oTable->Load($this->sId);
        }
    }

    /**
     * loads the record object TCMSRecord that is edited with default language
     * Was needed at database copy.
     */
    protected function LoadDataFromDatabaseWithDefaultLanguage()
    {
        if (!is_null($this->sId) && !empty($this->sId)) {
            $this->oTable = $this->GetNewTableObjectForEditor();
            $oCmsConfiguration = TdbCmsConfig::GetInstance();
            $sActiveLanguageId = $this->getLanguageService()->getActiveLanguageId();
            $oDefaultLanguage = $oCmsConfiguration->GetFieldTranslationBaseLanguage();
            if ($oDefaultLanguage && $sActiveLanguageId != $oDefaultLanguage->id) {
                $oCmsConfiguration->SetLanguage($oDefaultLanguage->id);
                $this->oTable->SetLanguage($oDefaultLanguage->id);
            }
            $this->oTable->Load($this->sId);
            if ($oDefaultLanguage && $sActiveLanguageId !== $oDefaultLanguage->id) {
                $oCmsConfiguration->SetLanguage($oCmsConfiguration->GetLanguage());
            }
        }
    }

    /**
     * returns the name of the current database record.
     *
     * @return string
     */
    public function GetName()
    {
        $sRecordName = '';
        if (is_object($this->oTable)) {
            $sRecordName = $this->oTable->GetName();
        }

        return $sRecordName;
    }

    /**
     * returns the name of the current database record.
     *
     * @return string
     */
    public function GetDisplayValue()
    {
        return $this->oTable->GetDisplayValue();
    }

    /**
     * saves the record with $postData
     * checks if postdata is valid and calls PostSaveHook after save.
     *
     * @param array $postData
     * @param bool $bDataIsInSQLForm - set to true, if the data in $postData is in sql form
     *
     * @return TCMSstdClass|false
     */
    public function Save($postData, $bDataIsInSQLForm = false)
    {
        $returnVal = false;

        if (!is_null($this->oTable)) {
            $this->oTablePreChangeData = clone $this->oTable;
        }

        $postData = $this->PrepareDataForSave($postData);
        $oPostTable = $this->GetNewTableObjectForEditor();

        if (TGlobal::IsCMSMode() && (isset($this->oTable) && false === $this->oTable->sqlData)) {
            // record doesn't exist anymore
        } else {
            $oPostTable->DisablePostLoadHook(true);
            $oPostTable->LoadFromRow($postData);

            $oFields = $this->oTableConf->GetFields($oPostTable);

            $this->PrepareFieldsForSave($oFields);
            if ($bDataIsInSQLForm || $this->DataIsValid($postData, $oFields)) {
                if ($this->_WriteDataToDatabase($oFields, $oPostTable, $bDataIsInSQLForm)) {
                    $this->PostSaveHook($oFields, $oPostTable);
                    $this->IfSaveModuleContentSaveModuleInstance();
                    $returnVal = $this->GetObjectShortInfo($postData);
                }
            }
        }

        return $returnVal;
    }

    /**
     * If saving a table with field cms_tpl_module_instance_id, save the module instance
     * to change the workflow status of the module instance.
     */
    protected function IfSaveModuleContentSaveModuleInstance()
    {
        $aTableBlackList = ['cms_tpl_page_cms_master_pagedef_spot'];
        if (!is_null($this->oTable) && !in_array($this->oTable->table, $aTableBlackList)) {
            if (is_array($this->oTable->sqlData) && array_key_exists('cms_tpl_module_instance_id', $this->oTable->sqlData) && !empty($this->oTable->sqlData['cms_tpl_module_instance_id'])) {
                $oTableEditorManager = TTools::GetTableEditorManager('cms_tpl_module_instance', $this->oTable->sqlData['cms_tpl_module_instance_id']);
                if (!empty($oTableEditorManager->oTableEditor->oTable->sqlData)) {
                    $oTableEditorManager->SaveField('name', $oTableEditorManager->oTableEditor->oTable->sqlData['name']);
                }
            }
        }
    }

    /**
     * gets called after save if all posted data was valid.
     *
     * @param TIterator $oFields holds an iterator of all field classes from DB table with the posted values or default if no post data is present
     * @param TCMSRecord $oPostTable holds the record object of all posted data
     */
    protected function PostSaveHook($oFields, $oPostTable)
    {
        $oFields->GoToStart();
        /** @var $oField TCMSField */
        while ($oField = $oFields->Next()) {
            if (!$this->bPreventPostSaveHookOnFields) {
                $oField->PostSaveHook($this->sId);
            }
        }
        if (TGlobal::IsCMSMode()) {
            $this->RefreshLock();
        }
        if (!is_null($this->oTableConf) && TCMSRecord::TableExists('shop_search_indexer') && (!defined('CMSUpdateManagerRunning'))) {
            TdbShopSearchIndexer::UpdateIndex($this->oTableConf->sqlData['name'], $this->sId, 'update');
        }

        $event = new RecordChangeEvent($this->oTableConf->sqlData['name'], $this->sId);
        $this->getEventDispatcher()->dispatch($event, CoreEvents::UPDATE_RECORD);
    }

    /**
     * saves only one field of a record (like the edit-on-click WYSIWYG).
     *
     * @param string $sFieldName the fieldname to save to
     * @param string $sFieldContent the content to save
     * @param bool $bTriggerPostSaveHook - if set to true, the PostSaveHook method will be called at the end of the call
     *
     * @return TCMSstdClass
     */
    public function SaveField($sFieldName, $sFieldContent, $bTriggerPostSaveHook = false)
    {
        if (!is_null($this->oTable)) {
            $this->oTablePreChangeData = clone $this->oTable;
        }

        $oPostTable = $this->GetNewTableObjectForEditor();
        $postData = [$sFieldName => $sFieldContent, 'id' => $this->sId];
        $oPostTable->DisablePostLoadHook(true);
        $oPostTable->LoadFromRow($postData);

        $oField = $this->oTableConf->GetField($sFieldName, $oPostTable);

        if (false === $oField->DataIsValid()) {
            return $this->GetObjectShortInfo($postData);
        }

        // overwrite the modifier type, because we definitely want to save the record field.
        $oField->oDefinition->sqlData['modifier'] = 'none';

        // and allow saving even if restricted!
        $oField->oDefinition->sqlData['restrict_to_groups'] = '0';
        $oField->data = $sFieldContent;

        $oFields = new TIterator();
        $oFields->AddItem($oField);

        if ($this->_WriteDataToDatabase($oFields, $oPostTable)) {
            $this->oTable->LoadFromRow($this->oTable->sqlData);
            if ($bTriggerPostSaveHook) {
                $this->PostSaveHook($oFields, $oPostTable);
            }
        }

        return $this->GetObjectShortInfo($postData);
    }

    /**
     * saves only one field of a record (like the edit-on-click WYSIWYG).
     *
     * @param array $aFieldData - name, value paris
     * @param bool $bTriggerPostSaveHook - if set to true, the PostSaveHook method will be called at the end of the call
     *
     * @return TCMSstdClass
     */
    public function SaveFields($aFieldData, $bTriggerPostSaveHook = false)
    {
        if (!is_null($this->oTable)) {
            $this->oTablePreChangeData = clone $this->oTable;
        }

        $oPostTable = $this->GetNewTableObjectForEditor();
        $postData = $aFieldData;
        $postData['id'] = $this->sId;

        $oPostTable->DisablePostLoadHook(true);
        $oPostTable->LoadFromRow($postData);

        $oFields = new TIterator();
        foreach (array_keys($aFieldData) as $sFieldName) {
            $oField = $this->oTableConf->GetField($sFieldName, $oPostTable);

            // overwrite the modifier type, because we definitly want to save the record field.
            $oField->oDefinition->sqlData['modifier'] = 'none';

            // and allow saving even if restricted!
            $oField->oDefinition->sqlData['restrict_to_groups'] = '0';
            $oField->data = $aFieldData[$sFieldName];
            $oFields->AddItem($oField);
        }

        if ($this->_WriteDataToDatabase($oFields, $oPostTable)) {
            if ($bTriggerPostSaveHook) {
                $this->PostSaveHook($oFields, $oPostTable);
            }
        }

        return $this->GetObjectShortInfo($postData);
    }

    /**
     * removes one connection from mlt if $sConnectedID is set
     * removes all connections from mlt where source id is current record id if $sConnectedID is false.
     *
     * @param string $sFieldName mlt fieldname (connected table name)
     * @param bool|string $sConnectedID the connected record id that will be removed
     */
    public function RemoveMLTConnection($sFieldName, $sConnectedID = false)
    {
        /** @var TCMSMLTField $oField */
        $oField = $this->oTableConf->GetField($sFieldName, $this->oTable);

        $this->RemoveMLTConnectionExecute($oField, $sConnectedID);
    }

    /**
     * removes one connection from mlt.
     *
     * @param TCMSField $oField mlt field object
     * @param int $iConnectedID the connected record id that will be removed
     */
    protected function RemoveMLTConnectionExecute($oField, $iConnectedID)
    {
        /** @var TCMSMLTField $oField */
        $mltTableName = $oField->GetMLTTableName();

        $databaseConnection = $this->getDatabaseConnection();
        $quotedMltTableName = $databaseConnection->quoteIdentifier($mltTableName);
        $quotedId = $databaseConnection->quote($this->sId);

        $deleteQuery = "DELETE FROM $quotedMltTableName WHERE `source_id` = $quotedId";
        $conditionFields = [
            'source_id' => $this->sId,
        ];
        if (false != $iConnectedID) {
            $quotedConnectedId = $databaseConnection->quote($iConnectedID);
            $deleteQuery .= " AND `target_id` = $quotedConnectedId";
            $conditionFields['target_id'] = $iConnectedID;
        }
        if (MySqlLegacySupport::getInstance()->query($deleteQuery)) {
            $editLanguage = TdbCmsLanguage::GetNewInstance($this->getBackendSession()->getCurrentEditLanguageId());
            $migrationQueryData = new MigrationQueryData($mltTableName, $editLanguage->fieldIso6391);
            $migrationQueryData
                ->setWhereEquals($conditionFields)
            ;
            $aQuery[] = new LogChangeDataModel($migrationQueryData, LogChangeDataModel::TYPE_DELETE);
            TCMSLogChange::WriteTransaction($aQuery);
            $this->getCacheService()->callTrigger($this->oTableConf->sqlData['name'], $this->sId);
        }
    }

    /**
     * adds an mlt entry to the record via AddMLTConnectionExecute.
     *
     * @param string $sFieldName mlt fieldname (connected table name)
     * @param int $iConnectedID
     */
    public function AddMLTConnection($sFieldName, $iConnectedID)
    {
        /** @var TCMSMLTField $oField */
        $oField = $this->oTableConf->GetField($sFieldName, $this->oTable);
        $oFieldType = $oField->oDefinition->GetFieldType();
        if ('_mlt' == substr($oField->name, -4)) {
            $sTargetTable = $oField->GetConnectedTableName();
        } elseif ('CMSFIELD_DOCUMENTS' == $oFieldType->sqlData['constname']) {
            // we have a cms_document field
            $sTargetTable = 'cms_document';
        } else {
            $sTargetTable = $oField->GetConnectedTableName();
        }

        $mltTableName = $oField->GetMLTTableName();

        $databaseConnection = $this->getDatabaseConnection();
        $quotedMltTableName = $databaseConnection->quoteIdentifier($mltTableName);
        $quotedTargetTable = $databaseConnection->quoteIdentifier($sTargetTable);
        $quotedId = $databaseConnection->quote($this->sId);
        $quotedConnectedId = $databaseConnection->quote($iConnectedID);

        // check if record with $iConnectedId exists
        $query = "SELECT COUNT(*) AS cmsmatches FROM $quotedTargetTable WHERE id = $quotedConnectedId";
        $aTmpRes = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query));
        if ($aTmpRes['cmsmatches'] > 0) {
            // make sure the connection doesn`t already exist

            $query = "SELECT COUNT(*) AS cmsmatches FROM $quotedMltTableName WHERE `source_id` = $quotedId AND `target_id` = $quotedConnectedId";
            $aTmpRes = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query));
            if (0 == $aTmpRes['cmsmatches']) {
                $this->AddMLTConnectionExecute($oField, $iConnectedID);
            }
        }
    }

    /**
     * adds an mlt entry to the record.
     *
     * @param TCMSField $oField mlt field object
     * @param int $iConnectedID
     *
     * @return void
     */
    protected function AddMLTConnectionExecute($oField, $iConnectedID)
    {
        /** @var TCMSMLTField $oField */
        $mltTableName = $oField->GetMLTTableName();
        $databaseConnection = $this->getDatabaseConnection();
        $quotedMltTableName = $databaseConnection->quoteIdentifier($mltTableName);
        // make sure the connection doesn`t exist already
        $query = "SELECT COUNT(*) AS cmsmatches FROM $quotedMltTableName WHERE `source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."' AND `target_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($iConnectedID)."'";
        $aTmpRes = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query));
        if (0 == $aTmpRes['cmsmatches']) {
            $iSortNumber = $this->GetMLTSortNumber($mltTableName);
            $insertQuery = "INSERT INTO $quotedMltTableName SET `source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."', `target_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($iConnectedID)."', `entry_sort` = '".MySqlLegacySupport::getInstance()->real_escape_string($iSortNumber)."'";
            if (MySqlLegacySupport::getInstance()->query($insertQuery)) {
                $editLanguage = TdbCmsLanguage::GetNewInstance($this->getBackendSession()->getCurrentEditLanguageId());
                $migrationQueryData = new MigrationQueryData($mltTableName, $editLanguage->fieldIso6391);
                $migrationQueryData
                    ->setFields([
                        'source_id' => $this->sId,
                        'target_id' => $iConnectedID,
                        'entry_sort' => $iSortNumber,
                    ])
                ;
                $aQuery = [new LogChangeDataModel($migrationQueryData, LogChangeDataModel::TYPE_INSERT)];
                TCMSLogChange::WriteTransaction($aQuery);
                $this->getCacheService()->callTrigger($this->oTableConf->sqlData['name'], $this->sId);
            }
        }
    }

    /**
     * Set new order position and updates order position in all other
     * connected connections behind the new position.
     *
     * @param string $sFieldName
     * @param string $sConnectedId
     * @param int $iPosition
     */
    public function updateMLTSortOrder($sFieldName, $sConnectedId, $iPosition)
    {
        /** @var TCMSMLTField $oField */
        $oField = $this->oTableConf->GetField($sFieldName, $this->oTable);
        $sMltTableName = $oField->GetMLTTableName();

        $databaseConnection = $this->getDatabaseConnection();
        $quotedMltTableName = $databaseConnection->quoteIdentifier($sMltTableName);
        $quotedId = $databaseConnection->quote($this->sId);
        $quotedPosition = $databaseConnection->quote($iPosition);

        $sQuery = "SELECT *
                   FROM $quotedMltTableName
                   WHERE `source_id` = $quotedId
                   ORDER BY `entry_sort` ASC ";
        $oRes = MySqlLegacySupport::getInstance()->query($sQuery);
        $bConnectedRecordExists = false;
        $aRowList = [];
        while ($aRow = MySqlLegacySupport::getInstance()->fetch_assoc($oRes)) {
            if ($aRow['target_id'] == $sConnectedId) {
                $bConnectedRecordExists = true;
                $quotedConnectedId = $databaseConnection->quote($sConnectedId);
                $sUpdateQuery = "UPDATE $quotedMltTableName
                           SET `entry_sort` = $quotedPosition
                         WHERE `target_id` = $quotedConnectedId
                           AND `source_id` = $quotedId";
                MySqlLegacySupport::getInstance()->query($sUpdateQuery);
            } else {
                $aRowList[] = $aRow;
            }
        }
        if ($bConnectedRecordExists) {
            $iSetPosition = false;
            foreach ($aRowList as $aRow) {
                if ($aRow['entry_sort'] >= $iPosition) {
                    if (false === $iSetPosition) {
                        $iSetPosition = $iPosition + 1;
                    } else {
                        ++$iSetPosition;
                    }
                    $quotedSetPosition = $databaseConnection->quote($iSetPosition);
                    $quotedTargetId = $databaseConnection->quote($aRow['target_id']);
                    $sUpdateQuery = "UPDATE $quotedMltTableName
                           SET `entry_sort` = $quotedSetPosition
                         WHERE `target_id` = $quotedTargetId
                           AND `source_id` = $quotedId";
                    MySqlLegacySupport::getInstance()->query($sUpdateQuery);
                }
            }
            $this->getCacheService()->callTrigger($this->oTableConf->sqlData['name'], $this->sId);
        }
    }

    /**
     * Get Last sort number for source.
     *
     * @param string $mltTableName
     *
     * @return int
     */
    protected function GetMLTSortNumber($mltTableName)
    {
        $iSortNumber = 0;
        $sQuery = 'SELECT `entry_sort` FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($mltTableName)."`
                 WHERE `source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."'
                 ORDER BY `entry_sort` DESC
                 LIMIT 1";
        $res = MySqlLegacySupport::getInstance()->query($sQuery);
        if (MySqlLegacySupport::getInstance()->num_rows($res) > 0) {
            $aRow = MySqlLegacySupport::getInstance()->fetch_assoc($res);
            $iSortNumber = $aRow['entry_sort'] + 1;
        }

        return $iSortNumber;
    }

    /**
     * here you can modify, clean or filter data before saving.
     *
     * @param array $postData
     *
     * @return array
     */
    protected function PrepareDataForSave($postData)
    {
        return $postData;
    }

    /**
     * creates a new record.
     *
     * @return TCMSstdClass
     */
    public function Insert()
    {
        $oPostTable = $this->GetNewTableObjectForEditor();
        $oFields = $this->oTableConf->GetFields($oPostTable, true); // for the insert we always load the defaults
        // we need to initialize all fields with the default values from the database.
        // this is needed since some of the values will need to be overwritten for some tables
        // we need to overwrite the default of the restriction field, if a restriction was given
        $this->_AddRestriction($oFields);
        $this->_OverwriteDefaults($oFields);
        if ($this->_WriteDataToDatabase($oFields, $oPostTable, true)) {
            $this->PostInsertHook($oFields);
        }

        return $this->GetObjectShortInfo();
    }

    /**
     * called after inserting a new record.
     *
     * note:
     * triggers cache invalidation on dummy record ID "NEW"
     * or if this is a table with a cms_tpl_module_instance_id field trigger only cache elements watching for new entries with the instance id
     *
     * your module needs a cache trigger element like this to recognize new entries
     *
     *  $aClearTriggers[] = array(
     *     'table' => 'your_module_table',
     *     'id' => 'NEW['.$this->instanceID.']'
     *  );
     *
     * @param TIterator $oFields - the fields inserted
     *
     * @return void
     */
    protected function PostInsertHook($oFields)
    {
        $oFields->GoToStart();
        $oCMSUserFieldType = TdbCmsFieldType::GetNewInstance();
        $bIsDone = false;
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        $userId = $securityHelper->getUser()?->getId();

        $sModuleInstanceID = '';
        while (!$bIsDone && ($oField = $oFields->Next())) {
            /** @var $oField TCMSField */
            if ('cms_user_id' == $oField->oDefinition->sqlData['name']) {
                // check if the field type is ok
                $bIsDone = true;
                if ($oCMSUserFieldType->LoadFromField('constname', 'CMSFIELD_PROPERTY_PARENT_ID')) {
                    if ($oField->oDefinition->sqlData['cms_field_type_id'] != $oCMSUserFieldType->id) {
                        $this->SaveField($oField->oDefinition->sqlData['name'], $userId);
                    }
                }
            } elseif ('cms_tpl_module_instance_id' == $oField->oDefinition->sqlData['name']) {
                $sModuleInstanceID = $oField->data;
            }
        }
        $oFields->GoToStart();
        while ($oField = $oFields->Next()) {
            $oField->PostInsertHook($this->sId);
        }
        $oFields->GoToStart();
        if (!is_null($this->oTableConf) && TCMSRecord::TableExists('shop_search_indexer') && !defined('CMSUpdateManagerRunning')) {
            TdbShopSearchIndexer::UpdateIndex($this->oTableConf->sqlData['name'], $this->sId, 'update');
        }

        $sCacheTriggerID = 'NEW';
        if (!empty($sModuleInstanceID)) {
            $sCacheTriggerID .= '-'.$sModuleInstanceID;
            $sCacheTriggerID = md5($sCacheTriggerID);
        }

        $this->getCacheService()->callTrigger($this->oTableConf->sqlData['name'], $sCacheTriggerID);

        $event = new RecordChangeEvent($this->oTableConf->sqlData['name'], $this->sId);
        $this->getEventDispatcher()->dispatch($event, CoreEvents::INSERT_RECORD);
    }

    /**
     * allows subclasses to overwrite default values.
     *
     * @param TIterator $oFields
     */
    protected function _OverwriteDefaults($oFields)
    {
    }

    /**
     * use this method to change field configurations pefore saving
     * e.g. overload fieldtypes or field modifier types (make it possible to
     * save hidden and readonly fields).
     *
     * @param TIterator $oFields
     */
    protected function PrepareFieldsForSave($oFields)
    {
    }

    /**
     * add the table restrictions.
     *
     * @param TIterator $oFields
     */
    protected function _AddRestriction($oFields)
    {
        if (!is_null($this->sRestriction) && !is_null($this->sRestrictionField) && '_id' == substr($this->sRestrictionField, -3)) {
            $restriction = $this->sRestriction;

            $oFields->GoToStart();
            /** @var $oField TCMSField */
            while ($oField = $oFields->Next()) {
                if ($oField->name == $this->sRestrictionField) {
                    $oField->data = $restriction;
                }
            }
            $oFields->GoToStart();
        }
    }

    /**
     * deletes the record and all language children; updates all references to this record.
     *
     * @param string $sId
     *
     * @return void
     */
    public function Delete($sId = null)
    {
        if (!is_null($sId)) {
            $this->sId = $sId;
            // reset oTable if id from method call isn`t the same as the currently loaded record
            if (!is_null($this->oTable) && ($sId != $this->oTable->id && (property_exists($this->oTable, 'idTranslated') && $sId != $this->oTable->idTranslated))) {
                $this->oTable = null;
            }

            $this->DeleteExecute();
        }
    }

    /**
     * is called only from Delete method and calls all delete relevant methods
     * executes the final SQL Delete Query.
     *
     * @return void
     */
    protected function DeleteExecute()
    {
        $deleteId = $this->sId; // prevent delete of wrong records when id is accidentally reset
        $tableName = $this->oTableConf->sqlData['name'];

        if (!is_null($this->oTableConf) && TCMSRecord::TableExists('shop_search_indexer') && !defined('CMSUpdateManagerRunning')) {
            TdbShopSearchIndexer::UpdateIndex($tableName, $deleteId, 'delete');
        }
        $this->DeleteRecordReferences();

        // final mysql delete
        $query = 'DELETE FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($this->oTableConf->sqlData['name'])."`
                      WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($deleteId)."'";
        MySqlLegacySupport::getInstance()->query($query);

        $this->addFieldDeletedTodoComment();

        $editLanguage = TdbCmsLanguage::GetNewInstance($this->getBackendSession()->getCurrentEditLanguageId());
        $migrationQueryData = new MigrationQueryData($tableName, $editLanguage->fieldIso6391);
        $migrationQueryData
            ->setWhereEquals([
                'id' => $deleteId,
            ])
        ;
        $queries = [new LogChangeDataModel($migrationQueryData, LogChangeDataModel::TYPE_DELETE)];
        TCMSLogChange::WriteTransaction($queries);

        $event = new RecordChangeEvent($tableName, $deleteId);
        $this->getEventDispatcher()->dispatch($event, CoreEvents::DELETE_RECORD);

        $this->sId = null;
    }

    /**
     * @throws TPkgSnippetRenderer_SnippetRenderingException
     * @throws MapperException
     */
    protected function addFieldDeletedTodoComment(): void
    {
        if (false === property_exists($this, '_oField') || null === $this->_oField) {
            return;
        }

        $todo = sprintf(
            '// @todo (usually only in the Core) you may add this table/field entry in the "deletedFields.json" like: "%s": [..., "%s"]'."\n",
            $this->_oField->sTableName,
            $this->_oField->name,
        );
        TCMSLogChange::WriteSqlTransactionWithPhpCommands('', [$todo]);
    }

    /**
     * makes a copy of current class. make sure to overwrite in children to copy
     * properties of child class.
     *
     * @param string $id
     *
     * @return TCMSTableEditor
     */
    public function CopyClass($id)
    {
        $oClass = clone $this;
        $oClass->Init($this->sTableId, $id);
        $oClass->sRestriction = $this->sRestriction;
        $oClass->sRestrictionField = $this->sRestrictionField;

        return $oClass;
    }

    /**
     * copies only $_POST data to a new record, so readonly and edit-on-click fields are empty.
     *
     * @todo: there should be a lookup before the copy to fetch readonly data
     *
     * @param array $postData
     * @param bool $bNoConversion
     *
     * @return TCMSstdClass - object from GetObjectShortInfo() method with id, error messages etc
     */
    public function Copy($postData, $bNoConversion = false)
    {
        $this->sSourceId = $this->sId;
        $oPostTable = $this->GetNewTableObjectForEditor();
        $oPostTable->LoadFromRow($postData);
        $oPostTable->id = null;
        $oPostTable->sqlData['id'] = null;
        $oFields = $this->oTableConf->GetFields($oPostTable);
        if ($this->_WriteDataToDatabase($oFields, $oPostTable, $bNoConversion, true)) {
            $this->OnAfterCopy();
        }

        return $this->GetObjectShortInfo();
    }

    /**
     * copies a record using data from database instead of post data.
     *
     * @param bool $bLanguageCopy
     * @param array $aOverloadedFields fields to copy with given value
     * @param bool $bCopyAllLanguages Set to true if you want to copy all language fields
     *
     * @return TCMSstdClass - object from GetObjectShortInfo() method with id, error messages etc
     */
    public function DatabaseCopy($bLanguageCopy = false, $aOverloadedFields = [], $bCopyAllLanguages = true)
    {
        $this->sSourceId = $this->sId;
        $this->oSourceTable = $this->oTable;
        $this->bIsCopyAllLanguageValues = $bCopyAllLanguages;
        $this->LoadDataFromDatabaseWithDefaultLanguage();
        $this->oTable->id = null;
        $this->oTable->sqlData['id'] = null;
        $this->OnBeforeCopy();
        $oFields = $this->oTableConf->GetFields($this->oTable);

        $oFields->GoToStart();
        while ($oField = $oFields->Next()) {
            /* @var $oField TCMSField */
            // overwrite modifier to 'none' so that even hidden fields are copied
            if (is_array($aOverloadedFields) && count($aOverloadedFields) > 0) {
                if (array_key_exists($oField->name, $aOverloadedFields)) {
                    $oField->data = $aOverloadedFields[$oField->name];
                    $this->oTable->sqlData[$oField->name] = $aOverloadedFields[$oField->name];
                }
            }
            $oField->oDefinition->sqlData['modifier'] = 'none';
        }

        $isCopy = true;
        if ($bLanguageCopy) {
            $isCopy = false;
        }
        if ($this->_WriteDataToDatabase($oFields, $this->oTable, true, $isCopy, false, $bCopyAllLanguages)) {
            if (false === $this->bPreventPostSaveHookOnFields) {
                $oFields->GoToStart();
                /** @var $oField TCMSField */
                while ($oField = $oFields->Next()) {
                    $oField->PostSaveHook($this->sId);
                }
            }
            $this->bIsDatabaseCopy = true;
            $this->OnAfterCopy();
        }

        return $this->GetObjectShortInfo();
    }

    /**
     * is executed before a record copy.
     */
    protected function OnBeforeCopy()
    {
        if ('1' == $this->oTableConf->sqlData['rename_on_copy'] && empty($this->oTableConf->sqlData['name_column_callback'])) {
            $sNameColumn = '';
            if (!empty($this->oTableConf->sqlData['name_column'])) {
                $sNameColumn = $this->oTableConf->sqlData['name_column'];
            } else {
                if (array_key_exists('name', $this->oTable->sqlData)) {
                    $sNameColumn = 'name';
                }
            }

            if (!empty($sNameColumn)) {
                $oFieldDefinition = $this->oTableConf->GetFieldDefinition($sNameColumn);
                $sTranslatedNameColumns = $oFieldDefinition->GetRealEditFieldName();
                $this->oTable->sqlData[$sTranslatedNameColumns] = $this->oTable->sqlData[$sTranslatedNameColumns].' ['.ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_table_editor.copied_record_suffix').']';
            }
        }
    }

    /**
     * makes it possible to modify the contents written to database after the copy
     * is commited.
     *
     * note: on shop systems the TdbShopSearchIndexer will be triggered
     */
    protected function OnAfterCopy()
    {
        // hotfix: call GetSQL to trigger additional scripts even on copy
        $this->LoadDataFromDatabase();
        $oFields = $this->oTableConf->GetFields($this->oTable);
        $sModuleInstanceID = '';
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        $userId = $securityHelper->getUser()?->getId();
        /** @var $oField TCMSField */
        while ($oField = $oFields->Next()) {
            $oField->PreGetSQLHook();
            if ($this->bIsDatabaseCopy) {
                $oField->GetDatabaseCopySQL();
            } else {
                $oField->GetSQL();
            }

            if ('cms_user_id' == $oField->oDefinition->sqlData['name']) {
                // check if the field type is ok
                $oCMSUserFieldType = TdbCmsFieldType::GetNewInstance();
                if ($oCMSUserFieldType->LoadFromField('constname', 'CMSFIELD_PROPERTY_PARENT_ID')) {
                    if ($oField->oDefinition->sqlData['cms_field_type_id'] != $oCMSUserFieldType->id) {
                        $this->SaveField($oField->oDefinition->sqlData['name'], $userId);
                    }
                }
            } elseif ('cms_tpl_module_instance_id' == $oField->oDefinition->sqlData['name']) {
                $sModuleInstanceID = $oField->data;
            }
        }

        if (!is_null($this->oTableConf) && TCMSRecord::TableExists('shop_search_indexer') && !defined('CMSUpdateManagerRunning')) {
            TdbShopSearchIndexer::UpdateIndex($this->oTableConf->sqlData['name'], $this->sId, 'update');
        }

        $sCacheTriggerID = 'NEW';
        if (!empty($sModuleInstanceID)) {
            $sCacheTriggerID .= '-'.$sModuleInstanceID;
            $sCacheTriggerID = md5($sCacheTriggerID);
        }

        $this->getCacheService()->callTrigger($this->oTableConf->sqlData['name'], $sCacheTriggerID);
    }

    /**
     * Returns all languages for field based translation.
     *
     * @return TdbCmsLanguageList
     */
    protected function GetLanguageListForDatabaseCopy()
    {
        return TdbCmsConfig::GetInstance()->GetFieldCmsLanguageList();
    }

    /**
     * write the data to the database.
     *
     * @param TIterator $oFields
     * @param TCMSRecord $oData
     * @param bool $bDataFromDatabase - disables the $oField->GetSQL(); lookup to prevent field format conversion
     * @param bool $isCopy
     * @param bool $bForceInsert - forces an INSERT instead of UPDATE try if record is missing
     * @param bool $bCopyAllLanguages
     *
     * @return bool
     */
    protected function _WriteDataToDatabase($oFields, $oData, $bDataFromDatabase = false, $isCopy = false, $bForceInsert = false, $bCopyAllLanguages = false)
    {
        $aMLTFields = [];
        $aPropertyFields = [];
        $comments = [];
        $oFields->GoToStart();

        $tableName = $this->oTableConf->sqlData['name'];
        $languageId = $this->oTableConf->GetLanguage();
        $language = TdbCmsLanguage::GetNewInstance();
        $language->Load($languageId);

        $bRecordExists = false;
        if ($bForceInsert) {
            $bRecordExists = TTools::RecordExists($tableName, 'id', $oData->id);
        }
        $bIsUpdateCall = (!is_null($oData->id) && !empty($oData->id));
        $bIsUpdateCall = $bIsUpdateCall && (($bRecordExists && $bForceInsert) || (!$bForceInsert));

        $this->bIsUpdateCall = $bIsUpdateCall;

        if ($bIsUpdateCall) {
            $query = 'UPDATE ';
        } else {
            $query = 'INSERT INTO ';
        }
        $query .= '`'.MySqlLegacySupport::getInstance()->real_escape_string($tableName).'` ';

        $deletedFields = $this->getDeletedFieldsService()->getTableDeletedFields($tableName);

        $isFirst = true;
        if ($bCopyAllLanguages) {
            $oLanguageCopyList = $this->GetLanguageListForDatabaseCopy();
        } else {
            $oLanguageCopyList = new TIterator();
        }
        $dataForChangeRecorder = [];
        $setLanguageFields = [];
        /** @var $oField TCMSField */
        while ($oField = $oFields->Next()) {
            $sFieldDisplayType = 'none';
            if (false === $this->bAllowEditByAll) {
                $sFieldDisplayType = $oField->GetDisplayType();
            }

            if ('readonly-if-filled' == $sFieldDisplayType && !$oField->HasDBContent()) {
                $this->bForceHiddenFieldWriteOnSave = true;
            }

            if ($this->bForceHiddenFieldWriteOnSave) {
                $isFieldChangeAllowed = true;
            } else {
                $isFieldChangeAllowed = (false === $bIsUpdateCall || ('hidden' != $sFieldDisplayType && 'edit-on-click' != $sFieldDisplayType && 'readonly-if-filled' != $sFieldDisplayType && 'readonly' != $sFieldDisplayType));
            }

            // prevent saving fields the user has no access to
            if (true === $isFieldChangeAllowed) {
                $sqlValue = '';
                $writeField = true;
                if (!$this->bPreventPreGetSQLHookOnFields && empty($isCopy) && !empty($bIsUpdateCall)) {
                    $writeField = $oField->PreGetSQLHook();
                }

                // prevent saving a field without real mysql field
                $oFieldType = $oField->oDefinition->GetFieldType();
                if ('' == $oFieldType->sqlData['mysql_type']) {
                    $writeField = false;
                }

                if ($bDataFromDatabase) {
                    $sqlValue = $oField->GetSQLOnCopy();
                    if ($oField->isMLTField) {
                        $sqlValue = false;
                    }
                } else {
                    $sqlValue = $oField->GetSQL();
                } // do format conversion if needed (e.g. date fields or fields that are as more than one field in postdata, like "datetime")

                // save MLT and Property fields for later processing
                if ($oField->isMLTField) {
                    $aMLTFields[] = $oField;
                } elseif ($oField->isPropertyField) {
                    $aPropertyFields[] = $oField;
                }

                $fieldName = '1' == $oField->oDefinition->sqlData['is_translatable'] ? $oField->oDefinition->GetEditFieldNameForLanguage($language) : $oField->name;
                $valuePrev = $this->oTable->sqlData[$fieldName] ?? null;

                $isCommentedField = false;
                if (true === $bIsUpdateCall && $sqlValue === $valuePrev) {
                    if ('name' === $oField->name) { // special field, comment line (maybe extended by table specific lists)
                        $comments[$oField->name] = new CommentDataModel(full: true);
                        $isCommentedField = true; // skip such for the SQL query
                    } else {
                        continue;
                    }
                }

                // now convert field name (if this is a multi-language field)
                $sqlFieldNameWithLanguageCode = $oField->oDefinition->GetRealEditFieldName($languageId);
                if (false !== $sqlValue && false !== $writeField) {
                    $isDeletedField = true === in_array($oField->name, $deletedFields);
                    $isCommentedField = true === $isCommentedField || true === $isDeletedField;

                    if (false === $isCommentedField) {
                        if ($isFirst) {
                            $isFirst = false;
                            $query .= 'SET ';
                        } else {
                            $query .= ', ';
                        }
                    }

                    if ($bCopyAllLanguages && '1' == $oField->oDefinition->sqlData['is_translatable']) {
                        $sqlFieldNameWithLanguageCode = $oField->oDefinition->sqlData['name'];
                        $sqlValue = $oField->oTableRow->sqlData[$sqlFieldNameWithLanguageCode];
                        $oLanguageCopyList->GoToStart();
                        while ($oLanguageCopy = $oLanguageCopyList->Next()) {
                            $sTargetFieldNameLanguage = $oField->oDefinition->GetEditFieldNameForLanguage($oLanguageCopy);
                            if ($sTargetFieldNameLanguage && $sTargetFieldNameLanguage !== $sqlFieldNameWithLanguageCode) {
                                $sqlValueLanguage = $oField->oTableRow->sqlData[$sTargetFieldNameLanguage];

                                if (false === $isCommentedField) {
                                    $query .= ' `'.MySqlLegacySupport::getInstance()->real_escape_string($sTargetFieldNameLanguage)."` = '".MySqlLegacySupport::getInstance()->real_escape_string($sqlValueLanguage)."', ";
                                }

                                if (false === isset($setLanguageFields[$oLanguageCopy->fieldIso6391])) {
                                    $setLanguageFields[$oLanguageCopy->fieldIso6391] = [];
                                }
                                $setLanguageFields[$oLanguageCopy->fieldIso6391][$sqlFieldNameWithLanguageCode] = $sqlValueLanguage;
                            }
                        }
                    }

                    if (false === $isCommentedField) {
                        $query .= '`'.MySqlLegacySupport::getInstance()->real_escape_string($sqlFieldNameWithLanguageCode)."` = '".MySqlLegacySupport::getInstance()->real_escape_string($sqlValue)."'";

                        if (true === $bIsUpdateCall) {
                            $previousComment = $this->getPreviousComment($valuePrev, $sqlValue);
                            if (null !== $previousComment) {
                                $comments[$oField->name] = new CommentDataModel($previousComment);
                            }
                        }
                    }

                    if (true === $isDeletedField) {
                        $comments[$oField->name] = new CommentDataModel('(!) this field was deleted', true);
                    }

                    // filter insert default values, except field "name"
                    if (true === $bIsUpdateCall || $sqlValue !== $oField->oDefinition->fieldFieldDefaultValue || 'name' === $oField->name) {
                        $dataForChangeRecorder[$oField->name] = $sqlValue;
                    }
                }
            }
        }

        if (true === $isFirst || [] === $dataForChangeRecorder && true === $bIsUpdateCall) {
            return true; // no changes made, but is a valid operation
        }

        $databaseChanged = false;
        $error = '';
        $whereConditions = [];

        if ($bIsUpdateCall) {
            $query .= " WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."'";
            $whereConditions['id'] = $this->sId;
        }

        $sourceRecordID = $this->sId;

        if (false === $bIsUpdateCall) {
            $databaseChanged = false;

            try {
                $id = $this->getGuidCreationService()->findUnusedId($tableName);

                $insertQuery = $query.", `id`='".MySqlLegacySupport::getInstance()->real_escape_string($id)."'";

                MySqlLegacySupport::getInstance()->query($insertQuery);
                $error = MySqlLegacySupport::getInstance()->error();
                if (empty($error)) {
                    $query = $insertQuery;
                    $databaseChanged = true;
                    $this->sId = $id;
                    $dataForChangeRecorder['id'] = $id;
                }
            } catch (GuidCreationFailedException $exception) {
                $error = $exception->getMessage();
            }
        } else {
            if (MySqlLegacySupport::getInstance()->query($query)) {
                $databaseChanged = true;
            } else {
                $error = MySqlLegacySupport::getInstance()->error();
            }
        }

        if ($databaseChanged) {
            $this->LoadDataFromDatabase();
            if (true === $this->isRecordingActive() && \count($dataForChangeRecorder) > 0) {
                $this->writePostWriteLogChangeData(
                    $bIsUpdateCall,
                    $dataForChangeRecorder,
                    $whereConditions,
                    $setLanguageFields,
                    $comments
                );
            }

            $this->getCacheService()->callTrigger($tableName, $this->sId);
        } else {
            // we need this because we use a redirect later and would not see the error message
            TTools::WriteLogEntrySimple('SQL Error: '.$error, 1, __FILE__, __LINE__);
        }

        if ($databaseChanged) {
            // handle MLT and Property Tables only if we do a copy
            if ($isCopy && !is_null($sourceRecordID)) { // copy fields only if there is no current record ID and it's not a table create
                // copy MLT fields
                foreach ($aMLTFields as $key => $oField) {
                    $this->CopyMLTRecords($oField, $sourceRecordID);
                }

                // copy Property fields
                foreach ($aPropertyFields as $key => $oField) {
                    $this->CopyPropertyRecords($oField, $sourceRecordID);
                }
            }
        }

        $bSaveSuccess = false;
        if (!empty($error)) {
            $this->getFlashMessageService()->AddMessage(
                TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER,
                'TABLEEDITOR_SAVE_ERROR',
                ['sqlError' => $error, 'sRecordID' => $this->sId, 'sTableID' => $this->sTableId]
            );
        } else {
            $bSaveSuccess = true;
        }

        return $bSaveSuccess;
    }

    /**
     * prints an adequate comment for the previous field value, consider WYSIWYG fields and shows partly the difference.
     */
    private function getPreviousComment(mixed $valuePrev, mixed $valueNow): ?string
    {
        if (true === is_array($valuePrev)) {
            return null;
        }

        if (true === is_string($valueNow) && strlen($valuePrev) > 255) {
            $maxLength = max(strlen($valuePrev), strlen($valueNow));

            // record first difference with fix length, starting if chars are not equal anymore (includes one string has ended)
            for ($i = 0; $i < $maxLength; ++$i) {
                $previousChar = $valuePrev[$i] ?? null;
                $nowChar = $valueNow[$i] ?? null;
                if ($nowChar !== $previousChar) {
                    $differencePrev = substr($valuePrev, $i, 100);

                    return sprintf('prev.: %s',
                        '' === $differencePrev ? '(at end)' : '...'.$this->getDatabaseConnection()->quote($differencePrev).'...'
                    );
                }
            }
        }

        return sprintf('prev.: %s', $this->getDatabaseConnection()->quote($valuePrev));
    }

    private function isRecordingActive(): bool
    {
        $migrationRecorderStateHandler = $this->getMigrationRecorderStateHandler();

        return $this->IsQueryLoggingAllowed() && $migrationRecorderStateHandler->isDatabaseLoggingActive();
    }

    /**
     * @param bool $isUpdateCall
     * @param array<string, CommentDataModel> $comments
     */
    private function writePostWriteLogChangeData($isUpdateCall, array $setFields, array $whereConditions, array $setLanguageFields, array $comments)
    {
        $tableName = $this->oTableConf->sqlData['name'];
        $languageService = $this->getLanguageService();
        if ($isUpdateCall) {
            $language = $languageService->getLanguage($languageId = $this->oTableConf->GetLanguage());
            $changeType = LogChangeDataModel::TYPE_UPDATE;
        } else {
            $language = $languageService->getCmsBaseLanguage();
            $changeType = LogChangeDataModel::TYPE_INSERT;
        }
        $migrationQueryData = new MigrationQueryData($tableName, $language->fieldIso6391);
        $migrationQueryData->setFields($setFields);
        $migrationQueryData->setWhereEquals($whereConditions);
        $migrationQueryData->setComments($comments);
        $dataModelList = [];
        $dataModelList[] = new LogChangeDataModel($migrationQueryData, $changeType);
        if (false === $isUpdateCall) {
            $whereConditions['id'] = $this->sId;
        }

        foreach ($setLanguageFields as $language => $fields) {
            $migrationQueryData = new MigrationQueryData($tableName, $language);
            $migrationQueryData->setFields($fields);
            $migrationQueryData->setWhereEquals($whereConditions);
            $dataModelList[] = new LogChangeDataModel($migrationQueryData, LogChangeDataModel::TYPE_UPDATE);
        }

        TCMSLogChange::WriteTransaction($dataModelList);
    }

    /**
     * checks for pages that use the module instance and performs
     * a dummy save (name field) to get a lock.
     *
     * @param string $sModuleInstanceID
     */
    protected function TriggerLockOnConnectedPages($sModuleInstanceID = '')
    {
        if (!empty($sModuleInstanceID)) {
            $query = "SELECT * FROM `cms_tpl_page_cms_master_pagedef_spot` WHERE `cms_tpl_page_cms_master_pagedef_spot`.`cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sModuleInstanceID)."'";
            $oModuleInstanceSpots = TdbCmsTplPageCmsMasterPagedefSpotList::GetList($query);
            while ($oModuleInstanceSpot = $oModuleInstanceSpots->Next()) {
                $sPageID = $oModuleInstanceSpot->fieldCmsTplPageId;
                if (!empty($sPageID)) {
                    $iTableID = TTools::GetCMSTableId('cms_tpl_page');
                    $oTableEditor = new TCMSTableEditorManager();
                    $oTableEditor->Init($iTableID, $sPageID);
                    $oTableEditor->SaveField('name', $oTableEditor->oTableEditor->oTable->sqlData['name']);
                }
            }
        }
    }

    /**
     * extend this method and return false if you want to disable
     * query logging using TCMSLogChange for updates files for a table.
     *
     * @return bool
     */
    protected function IsQueryLoggingAllowed()
    {
        $bAllowed = true;
        $aTableBlacklist = ['cms_lock'];
        if (in_array($this->oTableConf->sqlData['name'], $aTableBlacklist)) {
            $bAllowed = false;
        }

        return $bAllowed;
    }

    /**
     * copy multiple linked foreign record connections.
     *
     * @param TCMSMLTField $oField
     * @param string $sourceRecordID
     */
    public function CopyMLTRecords($oField, $sourceRecordID)
    {
        $mltTableName = $oField->GetMLTTableName();
        $oField->oTableRow->id = $sourceRecordID;

        $query = sprintf(
            '
                SELECT %1$s.`target_id`
                  FROM %1$s
                 WHERE %1$s.`source_id` = :sourceRecordId
              ORDER BY %1$s.`entry_sort` ASC',
            $this->databaseConnection->quoteIdentifier($mltTableName)
        );
        $connectedItems = $this->databaseConnection->fetchFirstColumn($query, ['sourceRecordId' => $sourceRecordID]);
        foreach ($connectedItems as $connectedItemId) {
            $this->AddMLTConnection($oField->name, $connectedItemId);
        }
    }

    /**
     * copy linked foreign property records.
     *
     * @param TCMSFieldPropertyTable $oField
     * @param string $sourceRecordID
     */
    public function CopyPropertyRecords($oField, $sourceRecordID)
    {
        $allowReferenceCopy = $oField->allowCopyRecordReferences();
        if (false === $allowReferenceCopy) {
            return;
        }

        $oField->oTableRow->id = $sourceRecordID;

        $propertyTableName = $oField->GetPropertyTableName();
        $tableClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $propertyTableName).'List';
        $targetTableForeignKeyFieldName = $oField->GetMatchingParentFieldName();
        $query = 'SELECT * FROM `'.$propertyTableName.'` WHERE `'.$targetTableForeignKeyFieldName."` = '".$sourceRecordID."'";
        $propertyList = $tableClassName::GetList($query);

        $tableID = TTools::GetCMSTableId($propertyTableName);
        $tableEditorManager = new TCMSTableEditorManager();
        while ($oProperty = $propertyList->Next()) {
            /* @var $oProperty TCMSRecord */
            $tableEditorManager->Init($tableID, $oProperty->id);
            $overloadedFields = [$targetTableForeignKeyFieldName => $this->sId];
            $tableEditorManager->AllowEditByAll(true);
            $tableEditorManager->DatabaseCopy(false, $overloadedFields, $this->bIsCopyAllLanguageValues);
        }
        $tableEditorManager->AllowEditByAll(false);
    }

    /**
     * called by the DeleteRecordReferences method on every property field for the current record being deleted.
     *
     * @param TCMSFieldDefinition $oPropertyField
     */
    protected function DeleteRecordReferencesProperties($oPropertyField)
    {
        $oPropertyFieldObject = $this->oTableConf->GetField($oPropertyField->sqlData['name'], $this->oTable, true);
        /** @var $oPropertyFieldObject TCMSFieldPropertyTable */
        $bAllowRecordReferenceDeletion = $oPropertyFieldObject->allowDeleteRecordReferences();
        $foreignKeyName = $oPropertyFieldObject->GetMatchingParentFieldName();
        if (false !== $foreignKeyName) {
            $propertyTable = $oPropertyFieldObject->GetPropertyTableName();
            $query = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($propertyTable).'` WHERE `'.MySqlLegacySupport::getInstance()->real_escape_string($foreignKeyName)."` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."'";

            $sClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $propertyTable).'List';
            $oRecordList = call_user_func_array([$sClassName, 'GetList'], [$query, null, false, true, true]);
            while ($oRecord = $oRecordList->Next()) {
                /** @var $oRecord TCMSRecord */
                $oTableConf = $oRecord->GetTableConf();
                $oEditor = new TCMSTableEditorManager();
                $oEditor->Init($oTableConf->id, $oRecord->id);
                $oEditor->AllowEditByAll(true);
                $oEditor->AllowDeleteByAll(true);
                if ($bAllowRecordReferenceDeletion) {
                    $oEditor->Delete($oRecord->id);
                } else {
                    $oEditor->SaveField($foreignKeyName, '');
                }
                $oEditor->AllowDeleteByAll(false);
                $oEditor->AllowEditByAll(false);
            }
        }
    }

    /**
     * remove all references to the current record in all tables (including mlt tables).
     */
    protected function DeleteRecordReferences()
    {
        $this->DeleteRecordReferencesFromSource();
        $this->getCacheService()->callTrigger($this->oTableConf->sqlData['name'], $this->sId);
        $this->DeleteConnectedRecordReferences();
    }

    /**
     * Delete record references from connected records.
     */
    public function DeleteConnectedRecordReferences()
    {
        $this->DeleteIdConnectedRecordReferences();
        $this->DeleteMltConnectedRecordReferences();
        $this->deleteMultiTableRecordReferences($this->oTableConf->sqlData['name'], $this->sId);
    }

    /**
     * Delete record reference from records connected with [tablename]_id.
     */
    public function DeleteIdConnectedRecordReferences()
    {
        // first remove from standard tables (fieldname = [tablename]_id)
        $oTableEditor = new TCMSTableEditorManager();
        $oTableEditor->AllowEditByAll($this->bAllowEditByAll);
        $query = "SELECT DISTINCT `cms_field_conf`.*, `cms_tbl_conf`.`name` AS tablename
                           FROM `cms_field_conf`
                      LEFT JOIN `cms_tbl_conf` ON `cms_field_conf`.`cms_tbl_conf_id` = `cms_tbl_conf`.`id`
                          WHERE `cms_field_conf`.`name` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->oTableConf->sqlData['name'])."_id'
               ";
        $rResult = MySqlLegacySupport::getInstance()->query($query);
        while ($aField = MySqlLegacySupport::getInstance()->fetch_assoc($rResult)) {
            if (!empty($aField['tablename'])) {
                $iTableID = $aField['cms_tbl_conf_id'];
                $query = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($aField['tablename']).'` WHERE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->oTableConf->sqlData['name'])."_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."'";
                $sClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $aField['tablename']).'List';
                $oRecordList = call_user_func_array([$sClassName, 'GetList'], [$query, null, false, true, true]);
                while ($oRecord = $oRecordList->Next()) {
                    $oTableEditor->Init($iTableID, $oRecord->id);
                    $oTableEditor->AllowEditByAll($this->bAllowEditByAll);
                    $oTableEditor->SaveField($this->oTableConf->sqlData['name'].'_id', '');
                }
            }
        }
    }

    protected function deleteMultiTableRecordReferences(string $tableName, string $id = ''): void
    {
        $fieldTypeId = $this->getFieldTypeIdBySystemName(TCMSFieldExtendedLookupMultiTable::FIELD_SYSTEM_NAME);

        if (null === $fieldTypeId) {
            return;
        }

        $databaseConnection = $this->getDatabaseConnection();
        $editLanguage = TdbCmsLanguage::GetNewInstance($this->getBackendSession()->getCurrentEditLanguageId());

        $fieldConfigResult = $this->getFieldsOfType($fieldTypeId);

        $recordedQueries = [];
        foreach ($fieldConfigResult as $row) {
            if (false === $this->hasMultiTableRecordReferences($row['tableName'], $row['fieldName'], $tableName, $id)) {
                continue;
            }

            $updateQuery = '
              UPDATE '.$databaseConnection->quoteIdentifier($row['tableName']).'
                 SET '.$databaseConnection->quoteIdentifier($row['fieldName'].TCMSFieldExtendedLookupMultiTable::TABLE_NAME_FIELD_SUFFIX)." = '',
                     ".$databaseConnection->quoteIdentifier($row['fieldName'])." = ''
               WHERE ".$databaseConnection->quoteIdentifier($row['fieldName'].TCMSFieldExtendedLookupMultiTable::TABLE_NAME_FIELD_SUFFIX).' = '.$databaseConnection->quote($tableName);

            $setFields[$row['fieldName']] = '';
            $setFields[$row['fieldName'].TCMSFieldExtendedLookupMultiTable::TABLE_NAME_FIELD_SUFFIX] = '';

            $whereEquals[$row['fieldName'].TCMSFieldExtendedLookupMultiTable::TABLE_NAME_FIELD_SUFFIX] = $tableName;

            if ('' !== $id) {
                $updateQuery .= ' AND '.$databaseConnection->quoteIdentifier($row['fieldName']).' = '.$databaseConnection->quote($id);

                $whereEquals[$row['fieldName']] = $id;
            }

            $databaseConnection->executeStatement($updateQuery);

            $migrationQueryData = new MigrationQueryData($row['tableName'], $editLanguage->fieldIso6391);
            $migrationQueryData->setFields($setFields);
            $migrationQueryData->setWhereEquals($whereEquals);

            $recordedQueries[] = new LogChangeDataModel($migrationQueryData, LogChangeDataModel::TYPE_UPDATE);
        }

        if (count($recordedQueries) > 0) {
            TCMSLogChange::WriteTransaction($recordedQueries);
        }
    }

    protected function getFieldTypeIdBySystemName(string $systemName): ?string
    {
        $databaseConnection = $this->getDatabaseConnection();

        $query = 'SELECT `id` FROM `cms_field_type` WHERE `constname` = '.$databaseConnection->quote($systemName);
        $fieldTypeId = $databaseConnection->fetchOne($query);

        if (false === $fieldTypeId) {
            return null;
        }

        return $fieldTypeId;
    }

    protected function getFieldsOfType(string $fieldTypeId): array
    {
        $databaseConnection = $this->getDatabaseConnection();

        $fieldConfigQuery = '
                  SELECT `cms_field_conf`.`name` AS fieldName,
                         `cms_tbl_conf`.`name` AS tableName
                    FROM `cms_field_conf`
               LEFT JOIN `cms_tbl_conf` ON `cms_tbl_conf`.`id` = `cms_field_conf`.`cms_tbl_conf_id`
                   WHERE `cms_field_conf`.`cms_field_type_id` = :fieldTypeId';

        return $databaseConnection->fetchAllAssociative($fieldConfigQuery, ['fieldTypeId' => $fieldTypeId]);
    }

    protected function hasMultiTableRecordReferences(string $tableName, string $fieldName, string $deletedTableName, string $deletedRecordId = ''): bool
    {
        $databaseConnection = $this->getDatabaseConnection();

        $relatedRecordsQuery = '
                   SELECT `id`
                     FROM '.$databaseConnection->quoteIdentifier($tableName).'
                    WHERE '.$databaseConnection->quoteIdentifier($fieldName.TCMSFieldExtendedLookupMultiTable::TABLE_NAME_FIELD_SUFFIX).' = '.$databaseConnection->quote($deletedTableName);

        if ('' !== $deletedRecordId) {
            $relatedRecordsQuery .= ' AND '.$databaseConnection->quoteIdentifier($fieldName).' = '.$databaseConnection->quote($deletedRecordId);
        }

        $relatedRecordsResult = $databaseConnection->executeQuery($relatedRecordsQuery);

        return $relatedRecordsResult->rowCount() > 0;
    }

    /**
     * Delete record references from records connected with mlt.
     */
    public function DeleteMltConnectedRecordReferences()
    {
        $oTableEditor = new TCMSTableEditorManager();
        $sQuery = "SELECT `cms_field_conf`.`id` as sFieldId,`cms_tbl_conf`.`name` AS sSourceTableName, `cms_tbl_conf`.`id` AS sSourceTableId  FROM `cms_field_conf`
                    INNER JOIN `cms_field_type` ON `cms_field_type`.`id` = `cms_field_conf`.`cms_field_type_id`
                    INNER JOIN `cms_tbl_conf` ON `cms_tbl_conf`.`id` = `cms_field_conf`.`cms_tbl_conf_id`
                         WHERE (`cms_field_conf`.`name` REGEXP '^".MySqlLegacySupport::getInstance()->real_escape_string($this->oTableConf->sqlData['name'])."([1-9]?_mlt$|[1-9]*$|_mlt$)'
                            OR `cms_field_conf`.`fieldtype_config` REGEXP 'connectedTableName\s*=\s*".MySqlLegacySupport::getInstance()->real_escape_string($this->oTableConf->sqlData['name'])."(\s*|$)')
                           AND `cms_field_type`.`base_type` ='mlt'";
        $oMySqLConnectedReferenceData = MySqlLegacySupport::getInstance()->query($sQuery);
        while ($aConnectedReferenceData = MySqlLegacySupport::getInstance()->fetch_assoc($oMySqLConnectedReferenceData)) {
            $sSourceTableName = $aConnectedReferenceData['sSourceTableName'];
            $sSourceTableId = $aConnectedReferenceData['sSourceTableId'];
            $sSourceTableNameFieldId = $aConnectedReferenceData['sFieldId'];
            $oSourceField = $this->GetConnectedRecordReferenceSourceField($sSourceTableNameFieldId, $sSourceTableName);
            if (null !== $oSourceField) {
                /**
                 * @var TCMSMLTField $oSourceField
                 */
                $sMLTTableName = $oSourceField->GetMLTTableName();
                $query = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($sSourceTableName).'` INNER JOIN `'.MySqlLegacySupport::getInstance()->real_escape_string($sMLTTableName).'` ON `'.MySqlLegacySupport::getInstance()->real_escape_string($sMLTTableName)."`.`target_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."' WHERE `source_id` = `".MySqlLegacySupport::getInstance()->real_escape_string($sSourceTableName).'`.`id`';
                $sClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $sSourceTableName).'List';
                $oRecordList = call_user_func_array([$sClassName, 'GetList'], [$query, null, false, true, true]);
                while ($oRecord = $oRecordList->Next()) {
                    $oTableEditor->Init($sSourceTableId, $oRecord->id);
                    $oTableEditor->RemoveMLTConnection($oSourceField->name, $this->sId);
                    $oTableEditor->_PostDeleteRelationTableEntry($sMLTTableName);
                }
            }
        }
    }

    /**
     * Returns the source field object for a connected reference like mlt field.
     *
     * @param string $sSourceTableNameFieldId id of the field config
     * @param string $sSourceTableName source table name
     *
     * @return TCMSField|null
     */
    protected function GetConnectedRecordReferenceSourceField($sSourceTableNameFieldId, $sSourceTableName)
    {
        $oFieldDefinition = TdbCmsFieldConf::GetNewInstance();
        $oSourceField = null;
        if ($oFieldDefinition->Load($sSourceTableNameFieldId)) {
            $oSourceField = $oFieldDefinition->GetFieldObject();
            $oSourceField->sTableName = $sSourceTableName;
            $oSourceField->name = $oFieldDefinition->sqlData['name'];
            $oSourceField->oDefinition = $oFieldDefinition;
        }

        return $oSourceField;
    }

    /**
     * deletes all references from the deleted record to other records
     * property records and mlt connections.
     */
    public function DeleteRecordReferencesFromSource()
    {
        // we need to delete any entries in related property tables...
        $oPropertyFields = $this->oTableConf->GetFieldDefinitions(['CMSFIELD_PROPERTY']);
        /* @var $oPropertyField TCMSFieldPropertyTable */
        while ($oPropertyField = $oPropertyFields->Next()) {
            $this->DeleteRecordReferencesProperties($oPropertyField);
        }

        // now delete all mlt connections
        $oFields = $this->oTableConf->GetFields($this->oTable);
        /** @var $oFields TIterator */
        while ($oField = $oFields->Next()) {
            if ($oField->isMLTField) {
                /* @var $oField TCMSFieldLookupMultiselect */
                $this->RemoveMLTConnection($oField->name, false);
                $this->_PostDeleteRelationTableEntry($oField->GetMLTTableName());
            }
        }
    }

    /**
     * called for every mlt table related to the main table when the main table is
     * deleted. $tableName holds the name of the mlt table.
     *
     * @param string $tableName
     */
    public function _PostDeleteRelationTableEntry($tableName)
    {
    }

    /**
     * fetches short record data for processing after an ajaxSave
     * is returned by Save method
     * id and name is always available in the returned object
     * overwrite this method to add custom return data.
     *
     * @param array $postData
     *
     * @return TCMSstdClass
     */
    public function GetObjectShortInfo($postData = [])
    {
        $oRecordData = new TCMSstdClass();

        // get name value
        $name = $this->GetName();
        if (empty($name) || false === $name || is_null($name)) {
            $name = ServiceLocator::get('translator')->trans('chameleon_system_core.text.unnamed_record');
        }

        if (!is_null($this->sId)) {
            $oRecordData->id = $this->sId;
            if (is_null($this->oTable)) {
                $this->LoadDataFromDatabase();
            }
            $oRecordData->cmsident = null;
            if (!is_null($this->oTable)) {
                $oRecordData->cmsident = $this->oTable->sqlData['cmsident'];
            }
        }
        $oRecordData->name = TGlobal::OutHTML($name);

        return $oRecordData;
    }

    /**
     * @return string[]
     */
    public function GetHtmlHeadIncludes()
    {
        return [];
    }

    /**
     * @return string[]
     */
    public function GetHtmlFooterIncludes()
    {
        return [];
    }

    /**
     * return the table object for the record being changed.
     *
     * @return TCMSRecord
     */
    public function GetNewTableObjectForEditor()
    {
        $sClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $this->oTableConf->sqlData['name']);

        return new $sClassName();
    }

    /**
     * method is called before the fields are shown in the editor - this allows the
     * TCMSTableEditor class or children to modify the fields before they are shown to the user.
     *
     * @param TIterator $oFields
     */
    public function ProcessFieldsBeforeDisplay($oFields)
    {
    }

    /**
     * inserts or refreshes the lock for the current record.
     *
     * @return TCMSstdClass|bool
     */
    public function RefreshLock()
    {
        $oReturnData = false;

        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        if ($securityHelper->isGranted(CmsUserRoleConstants::CMS_USER)) {
            return false;
        }

        $user = $securityHelper->getUser();

        if (null !== $user && TGlobal::IsCMSMode() && array_key_exists('locking_active', $this->oTableConf->sqlData) && '1' == $this->oTableConf->sqlData['locking_active'] && CHAMELEON_ENABLE_RECORD_LOCK) {
            // prevent locking of cms_lock table
            $iTableID = TTools::GetCMSTableId('cms_lock');
            if ($this->sTableId !== $iTableID) {
                $oGlobal = TGlobal::instance();
                $aData = [];
                $aData['time_stamp'] = time();
                $aData['cms_user_id'] = $user->getId();

                $oTableEditor = new TCMSTableEditorManager();
                /** @var $oTableEditor TCMSTableEditorManager */
                // check if lock exists
                $query = 'SELECT * FROM `cms_lock` WHERE `recordid` = :recordId AND `cms_tbl_conf_id` = :tableId';
                $row = $this->getDatabaseConnection()->fetchAssociative($query, ['recordId' => $this->sId, 'tableId' => $this->sTableId]);
                if (false !== $row) {
                    $aData['id'] = $row['id'];
                    $oTableEditor->Init($iTableID, $row['id']);
                } else {
                    $aData['cms_tbl_conf_id'] = $this->sTableId;
                    $aData['recordid'] = $this->sId;
                    $oTableEditor->Init($iTableID, null);
                }

                $oTableEditor->AllowEditByAll(true);
                $oReturnData = $oTableEditor->Save($aData);
                $oTableEditor->AllowEditByAll(false);
            }
        }

        return $oReturnData;
    }

    /**
     * removes the edit lock for the current record and user.
     */
    public function RemoveLock()
    {
        if (array_key_exists('locking_active', $this->oTableConf->sqlData) && '1' == $this->oTableConf->sqlData['locking_active']) {
            /** @var SecurityHelperAccess $securityHelper */
            $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
            $userId = $securityHelper->getUser()?->getId();
            if (null !== $userId) {
                $query = 'DELETE FROM `cms_lock` WHERE `recordid` = :recordId AND `cms_tbl_conf_id` = :tableId AND `cms_user_id` = :userId';
                $this->getDatabaseConnection()->executeQuery($query, ['recordId' => $this->sId, 'tableId' => $this->sTableId, 'userId' => $userId]);
            }
        }
    }

    /**
     * checks if the parentrecord of a record is locked.
     *
     * @return TdbCmsLock|bool - the lock record if found, else false
     */
    protected function IsParentRecordLocked()
    {
        $lockActive = false;
        if (!is_null($this->oTable)) {
            $oCurrentTable = $this->GetNewTableObjectForEditor();
            $oCurrentTable->LoadWithCaching($this->sId);
            $oFieldConfs = $this->oTableConf->GetFieldDefinitions(['CMSFIELD_PROPERTY_PARENT_ID']);
            /** @var $oFieldConf TCMSFieldDefinition */
            while ($oFieldConf = $oFieldConfs->Next()) {
                $oParentTableEditor = new TCMSTableEditor();
                /** @var $oField TCMSMLTField */
                $oField = $this->oTableConf->GetField($oFieldConf->fieldName, $this->oTable);

                $sParentTableName = $oField->GetConnectedTableName();
                $sTableID = TTools::GetCMSTableId($sParentTableName);
                $oParentTableEditor->Init($sTableID, $oField->data);
                if (array_key_exists('locking_active', $oParentTableEditor->oTableConf->sqlData) && '1' == $oParentTableEditor->oTableConf->sqlData['locking_active']) {
                    $lockActive = $oParentTableEditor->IsRecordLocked(false);
                }
            }
        }

        return $lockActive;
    }

    /**
     * checks if record is currently locked by other editor.
     *
     * @param bool $bCheckParentRecord - if this is set to true it will check the parent record for locking
     *
     * @return TdbCmsLock|bool - the lock record if found, else false
     */
    public function IsRecordLocked($bCheckParentRecord = true)
    {
        static $aRecordLockCache;

        $lockActive = false;
        if (array_key_exists('locking_active', $this->oTableConf->sqlData) && '1' == $this->oTableConf->sqlData['locking_active'] && CHAMELEON_ENABLE_RECORD_LOCK) {
            if (!is_null($aRecordLockCache) && is_array($aRecordLockCache) && isset($aRecordLockCache[$this->sId])) {
                $lockActive = $aRecordLockCache[$this->sId];
            } else {
                $lockActive = TTools::IsRecordLocked($this->sTableId, $this->sId);

                if (!$lockActive && $bCheckParentRecord) {
                    $lockActive = $this->IsParentRecordLocked();
                }
                if (is_null($aRecordLockCache)) {
                    $aRecordLockCache = [];
                }
                $aRecordLockCache[$this->sId] = $lockActive;
            }
        }

        return $lockActive;
    }

    /**
     * returns true if record is locked by another user, a transaction or edit rights are missing.
     *
     * @return TdbCmsLock|bool
     */
    public function IsRecordInReadOnlyMode()
    {
        $bIsReadOnlyMode = $this->IsRecordLocked();

        if (!$bIsReadOnlyMode) {
            if (!$this->AllowEdit()) {
                $bIsReadOnlyMode = true;
            }
        }

        return $bIsReadOnlyMode;
    }

    /**
     * @deprecated since the use for this blacklist handling was removed when the revision logic was removed. Will be remove in 7.2
     * adds a table + record id to the delete blacklist.
     *
     * @param bool|string $sTableId
     * @param bool|string $sRecordId
     */
    protected function SetDeleteBlackList($sTableId = false, $sRecordId = false)
    {
    }

    /**
     * @deprecated since the use for this blacklist handling was removed when the revision logic was removed. Will be remove in 7.2
     * checks if recordID of tableID is in delete blacklist.
     *
     * @param bool|string $sTableId
     * @param bool|string $sRecordId
     *
     * @return bool
     */
    public function IsInDeleteBlackList($sTableId = false, $sRecordId = false)
    {
        return false;
    }

    /**
     * change position of current record
     * currently expects a list of ids to sort via get/post aPosOrder.
     *
     * @param string $sPositionField
     *
     * @return int|null - returns NULL if position of current record did not change
     */
    public function UpdatePositionField($sPositionField)
    {
        $iNewPositionOfCurrentRecord = null;
        $oGlobal = TGlobal::instance();
        $aPosOrder = $oGlobal->GetUserData('aPosOrder');
        $oTableEditorManager = new TCMSTableEditorManager();
        $iPos = 0;
        $sInstanceID = '';

        foreach ($aPosOrder as $sId) {
            ++$iPos;
            $oTableEditorManager->Init($this->oTableConf->id, $sId);
            if ($sId == $this->sId) {
                $iNewPositionOfCurrentRecord = $iPos;
            }
            if ($oTableEditorManager->oTableEditor->oTable->sqlData[$sPositionField] != $iPos) {
                $oTableEditorManager->SaveField($sPositionField, $iPos, true);
            }
            if ((1 == $iPos) && isset($oTableEditorManager->oTableEditor->oTable->sqlData['cms_tpl_module_instance_id'])) {
                $sInstanceID = $oTableEditorManager->oTableEditor->oTable->sqlData['cms_tpl_module_instance_id'];
            }
        }

        if (!empty($sInstanceID)) {
            /** @var $oInstance TdbCmsTplModuleInstance */
            $oInstance = TdbCmsTplModuleInstance::GetNewInstance();
            if ($oInstance->Load($sInstanceID)) {
                $oPagedefSpotList = $oInstance->GetFieldCmsTplPageCmsMasterPagedefSpotList();
                while ($oPagedefSpot = $oPagedefSpotList->Next()) {
                    $oCmsTplPage = $oPagedefSpot->GetFieldCmsTplPage();
                    if (!is_null($oCmsTplPage)) {
                        $oTableEditorManagerPage = TTools::GetTableEditorManager('cms_tpl_page', $oCmsTplPage->id);
                        $oTableEditorManagerPage->SaveField('name', $oCmsTplPage->fieldName);
                    }
                }
            }
        }

        return $iNewPositionOfCurrentRecord;
    }

    /**
     * return array of hidden fields (key=>value) that will be added to
     * MTTableEditor forms.
     *
     * @return array
     */
    public function GetHiddenFieldsHook()
    {
        $oGlobal = TGlobal::instance();
        $aAdditionalParameterData = [];
        $aAdditionalParameters = ['sRestrictionField', 'sRestriction'];
        foreach ($aAdditionalParameters as $sKey) {
            if ($oGlobal->UserDataExists($sKey)) {
                $aAdditionalParameterData[$sKey] = $oGlobal->GetUserData($sKey);
            }
        }

        return $aAdditionalParameterData;
    }

    /**
     * removes one connection of from mlt if $sConnectedID is set
     * removes all connections from mlt where source id is current record id if $sConnectedID is false.
     *
     * @param string $sFieldName mlt fieldname (connected table name)
     * @param bool|string $sConnectedID the connected record id that will be removed
     */
    public function removeTagMLTConnection($sFieldName, $sConnectedID)
    {
        $this->RemoveMLTConnection($sFieldName, $sConnectedID);
        $this->decreaseTagCount($sConnectedID);
    }

    /**
     * adds an mlt entry of tag to the record via AddMLTConnectionExecute.
     *
     * @param string $sFieldName mlt fieldname (connected table name)
     * @param string $sConnectedID
     */
    public function addTagMLTConnection($sFieldName, $sConnectedID)
    {
        $this->AddMLTConnection($sFieldName, $sConnectedID);
        $this->increaseTagCount($sConnectedID);
    }

    /**
     * Increase count of record with $sConnectedID  in table cms_tags.
     *
     * @param string $sConnectedID
     */
    protected function increaseTagCount($sConnectedID)
    {
        $oCmsTag = TdbCmsTags::GetNewInstance();
        if ($oCmsTag->Load($sConnectedID)) {
            $oCmsTagsTableEditor = TTools::GetTableEditorManager('cms_tags', $sConnectedID);
            $oCmsTagsTableEditor->AllowEditByAll(true);
            $iCount = $oCmsTag->sqlData['count'] + 1;
            $oCmsTagsTableEditor->SaveField('count', $iCount);
            $oCmsTagsTableEditor->AllowEditByAll(false);
        }
    }

    /**
     * decrease count of record with $sConnectedID  in table cms_tags.
     *
     * @param string $sConnectedID
     */
    protected function decreaseTagCount($sConnectedID)
    {
        $oCmsTag = TdbCmsTags::GetNewInstance();
        if ($oCmsTag->Load($sConnectedID)) {
            $oCmsTagsTableEditor = TTools::GetTableEditorManager('cms_tags', $sConnectedID);
            if ($oCmsTag->sqlData['count'] > 0) {
                --$oCmsTag->sqlData['count'];
            }
            $oCmsTagsTableEditor->AllowEditByAll(true);
            $oCmsTagsTableEditor->SaveField('count', $oCmsTag->sqlData['count']);
            $oCmsTagsTableEditor->AllowEditByAll(false);
        }
    }

    /**
     * @param string|null $fieldName
     */
    public function setActiveEditField($fieldName)
    {
        $this->activeEditField = $fieldName;
    }

    /**
     * @return string|null
     */
    protected function getActiveEditField()
    {
        return $this->activeEditField;
    }

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

    protected function getBackendSession(): BackendSessionInterface
    {
        return ServiceLocator::get('chameleon_system_cms_backend.backend_session');
    }

    /**
     * @return LanguageServiceInterface
     */
    protected function getLanguageService()
    {
        return ServiceLocator::get('chameleon_system_core.language_service');
    }

    /**
     * @return EventDispatcherInterface
     */
    protected function getEventDispatcher()
    {
        return ServiceLocator::get('event_dispatcher');
    }

    /**
     * @return TGlobal
     */
    private function getGlobal()
    {
        return ServiceLocator::get('chameleon_system_core.global');
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private function getPortalDomainService()
    {
        return ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }

    private function getCurrentRequest(): ?Request
    {
        return ServiceLocator::get('request_stack')->getCurrentRequest();
    }

    private function getInputFilterUtil(): InputFilterUtilInterface
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }

    private function getMigrationRecorderStateHandler(): MigrationRecorderStateHandler
    {
        return ServiceLocator::get('chameleon_system_database_migration.recorder.migration_recorder_state_handler');
    }

    private function getGuidCreationService(): GuidCreationServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.service.guid_creation');
    }

    private function getFlashMessageService(): FlashMessageServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.flash_messages');
    }

    protected function getCacheService(): CacheInterface
    {
        return ServiceLocator::get('chameleon_system_core.cache');
    }

    protected function getDeletedFieldsService(): DeletedFieldsServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.service.deleted_fields');
    }
}

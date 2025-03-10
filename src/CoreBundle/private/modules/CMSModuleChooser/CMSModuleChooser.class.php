<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\i18n\TranslationConstants;
use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\FieldTranslationUtil;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;
use Doctrine\DBAL\Connection;
use esono\pkgCmsCache\CacheInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * used to pick the module, and configure it
 * individual modules can skip this through a pagedef parameter.
 * /**/
class CMSModuleChooser extends TCMSModelBase
{
    public bool $bMasterPagedefRequest = false;

    /**
     * the module instance data.
     */
    protected ?TdbCmsTplModuleInstance $oModuleInstance = null;

    /**
     * the module of the module instance.
     */
    protected ?TdbCmsTplModule $oModule = null;

    /**
     * holds a pointer to the original model. we need this so that the delete, update, insert
     * functions can trigger the correct response...
     */
    public ?TModelBase $oCustomerModelObject = null;

    /**
     * instance id of the module of this spot.
     */
    public ?string $instanceID = null;

    /**
     * access via GetCmsTplPageCmsMasterPagedefSpot.
     */
    private ?TdbCmsTplPageCmsMasterPagedefSpot $oCmsTplPageCmsMasterPagedefSpot = null;

    /**
     * is true if the page is locked by a user.
     */
    protected bool $bPageIsLockedByUser = false;

    /**
     * called before the execute method, and before any external functions gets called, but
     * after the constructor.
     */
    public function Init()
    {
        $this->LoadModuleInstanceData();
    }

    public function Execute()
    {
        parent::Execute();
        $securityHelper = $this->getSecurityHelperAccess();
        $this->data['hasRightToEditModules'] = $securityHelper->isGranted('CMS_RIGHT_CMS_TEMPLATE_MODULE_EDIT');
        $this->data['hasRightToSwitchLayouts'] = $securityHelper->isGranted('CMS_RIGHT_CMS_MASTER_PAGEDEF_SPOT');

        $activePortal = $this->getPortalDomainService()->getActivePortal();

        if (null !== $activePortal) {
            $this->data['activePortalId'] = $activePortal->id;
        }

        $this->data['moduleInstance'] = $this->oModuleInstance;
        $this->data['moduleEditStateColor'] = '';
        if (null !== $this->oModuleInstance) {
            $this->data['moduleEditStateColor'] = $this->oModuleInstance->GetModuleColorEditState();
        }
        $this->data['module'] = $this->oModule;
        $this->data['moduleSpotName'] = $this->sModuleSpotName;
        $this->data['viewMapping'] = [];
        $this->data['moduleViews'] = null;
        $this->data['relatedTables'] = [];
        if (null !== $this->oModule) {
            $this->data['viewMapping'] = $this->oModule->GetViewMapping();
            $this->data['moduleViews'] = $this->oModule->GetViews();
            $this->getRelatedTables();
        }
        $this->data['menuPrefix'] = TGlobal::OutHTML($this->sModuleSpotName);
        $this->data['cmsMasterPageDefinitionSpotTableId'] = TTools::GetCMSTableId('cms_master_pagedef_spot');

        $this->getRequestData();

        if (array_key_exists('permittedModules', $this->aModuleConfig)) {
            $this->data['aPermittedModules'] = $this->aModuleConfig['permittedModules'];
        } else {
            $this->data['aPermittedModules'] = null;
        }

        $this->CheckFunctionRights();

        $this->data['cmsMasterPageDefSpot'] = $this->getMasterPageDefSpot();

        $this->data['createModuleMenu'] = '';

        if ($this->bMasterPagedefRequest) {
            // no change right and no module set, then just show the box
            $this->SetTemplate('CMSModuleChooser', 'placeholder');
        } elseif (is_null($this->oModuleInstance) && !$securityHelper->isGranted('CMS_RIGHT_CMS_TEMPLATE_MODULE_EDIT')) {
            $this->SetTemplate('CMSModuleChooser', 'readonly');
        } else {
            $this->GetModuleList();
            $this->data['createModuleMenu'] = $this->createModuleMenuHtml();
        }

        if ($this->bPageIsLockedByUser) {
            $this->SetTemplate('CMSModuleChooser', 'readonly');
        }

        return $this->data;
    }

    private function getRelatedTables(): void
    {
        $relatedTablesList = $this->oModule->GetMLT('cms_tbl_conf_mlt');
        if (null !== $relatedTablesList) {
            while($relatedTable = $relatedTablesList->Next()) {
                $this->data['relatedTables'][] = [
                    'id' => $relatedTable->id,
                    'name' => $relatedTable->sqlData['name'],
                ];
            }
        }
    }

    private function getMasterPageDefSpot(): TdbCmsMasterPagedefSpot
    {
        $pageDefinition = TCMSPagedef::GetCachedInstance($this->data['pagedef']);
        $cmsMasterPageDefSpot = TdbCmsMasterPagedefSpot::GetNewInstance();
        $cmsMasterPageDefSpot->LoadFromFieldsWithCaching(
            [
                'name' => $this->sModuleSpotName,
                'cms_master_pagedef_id' => $pageDefinition->fieldCmsMasterPagedefId
            ]
        );

        return $cmsMasterPageDefSpot;
    }

    /**
     *  Check the rights of the functions, who call the corresponding table in database
     *  fills data['functionRights'] with function rights based on current user.
     */
    protected function CheckFunctionRights()
    {
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        $bHasModuleInstanceEditRight = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT, 'cms_tpl_module_instance');
        $bHasSpotEditRight = true; // we ignore the table rights on cms_tpl_page_cms_master_pagedef_spot at this moment

        // NewInstance
        $this->data['functionRights']['instanceNewInstanceAllowed'] = false;
        if ($bHasSpotEditRight && $bHasModuleInstanceEditRight) {
            $this->data['functionRights']['instanceNewInstanceAllowed'] = true;
        }
        // ClearInstance
        $this->data['functionRights']['instanceClearInstanceAllowed'] = false;
        if ($bHasSpotEditRight) {
            $this->data['functionRights']['instanceClearInstanceAllowed'] = true;
        }
        // DeleteInstance
        $this->data['functionRights']['instanceDeleteInstanceAllowed'] = false;
        if ($bHasSpotEditRight && $bHasModuleInstanceEditRight) {
            $this->data['functionRights']['instanceDeleteInstanceAllowed'] = true;
        }
        // ChangeView
        $this->data['functionRights']['instanceChangeViewAllowed'] = false;
        if ($bHasSpotEditRight && $bHasModuleInstanceEditRight) {
            $this->data['functionRights']['instanceChangeViewAllowed'] = true;
        }
        // RenameInstance
        $this->data['functionRights']['instanceRenameInstanceAllowed'] = false;
        if ($bHasSpotEditRight && $bHasModuleInstanceEditRight) {
            $this->data['functionRights']['instanceRenameInstanceAllowed'] = true;
        }
        // SetInstance
        $this->data['functionRights']['instanceSetInstanceAllowed'] = false;
        if ($bHasSpotEditRight && $bHasModuleInstanceEditRight) {
            $this->data['functionRights']['instanceSetInstanceAllowed'] = true;
        }
        // CopyInstance
        $this->data['functionRights']['instanceCopyInstanceAllowed'] = false;
        if ($bHasSpotEditRight && $bHasModuleInstanceEditRight) {
            $this->data['functionRights']['instanceCopyInstanceAllowed'] = true;
        }
        // SwitchInstances / Move
        $this->data['functionRights']['instanceSwitchingAllowed'] = false;
        if ($bHasSpotEditRight) {
            $this->data['functionRights']['instanceSwitchingAllowed'] = true;
        }
    }

    /**
     * @return void
     */
    public function getRequestData(): void
    {
        $currentRequest = $this->getCurrentRequest();

        $this->data['pagedef'] = $currentRequest->get('pagedef');
        $this->data['id'] = $currentRequest->get('id');
        $this->data['disableLinks'] = $currentRequest->get('esdisablelinks');
        $this->data['disableFrontendJs'] = $currentRequest->get('esdisablefrontendjs');
        $this->data['previewMode'] = $currentRequest->get('__previewmode');
        $this->data['previewLanguageId'] = $currentRequest->get('previewLanguageId');
    }

    protected function CheckLogin()
    {
        return false;
    }

    public function GetHtmlHeadIncludes()
    {
        static $included = false;
        if ($included) {
            return [];
        }

        $included = true;

        $aIncludes = parent::GetHtmlHeadIncludes();
        if ($this->global->GetUserData('sRedirectURL')) {
            $aIncludes[] = '<script> top.document.location.href="'.$this->global->GetUserData('sRedirectURL', [], TCMSUserInput::FILTER_URL).'";</script>';
        } else {
            /** @var $viewRenderer ViewRenderer */
            $viewRenderer = ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
            $viewRenderer->AddSourceObject('themePath', TGlobal::GetPathTheme());
            $viewRenderer->AddSourceObject('resourcePath', '/'.TGlobalBase::$PATH_TO_WEB_LIBRARY);

            $viewRenderer->AddSourceObject('hasLockUser', false);

            $aIncludes[] = $viewRenderer->Render('CMSModuleChooser/headerjs.html.twig');
        }

        return $aIncludes;
    }

    /**
     * loads the module instance data.
     */
    protected function LoadModuleInstanceData()
    {
        // check if page is under workflow control and locked
        $pageDefinition = $this->global->GetUserData('pagedef');
        $oCmsTplPage = null;
        if (!empty($pageDefinition)) {
            // load page
            $oCmsTplPage = $this->getActivePageService()->getActivePage();

            $iTableID = $this->getTools()::GetCMSTableId('cms_tpl_page');
            /** @var $oTableEditor TCMSTableEditorManager */
            $oTableEditor = new TCMSTableEditorManager();
            $oTableEditor->Init($iTableID, $pageDefinition);
            $oLock = $oTableEditor->IsRecordLocked();
            if (is_object($oLock)) {
                $this->bPageIsLockedByUser = true;
            }
        }

        if (!empty($this->instanceID)) {
            $cmsTplModuleInstance = TdbCmsTplModuleInstance::GetNewInstance();
            if ($cmsTplModuleInstance->LoadWithCaching($this->instanceID)) {
                $this->oModuleInstance = $cmsTplModuleInstance;
                $oCmsTplModule = TdbCmsTplModule::GetNewInstance();
                $oCmsTplModule->Load($this->oModuleInstance->sqlData['cms_tpl_module_id']);
                $this->oModule = $oCmsTplModule;
                if ($this->oModuleInstance->id !== $this->oCustomerModelObject->instanceID) {
                    $defaultView = $this->GetDefaultView($oCmsTplPage, $cmsTplModuleInstance, $this->oModule, $this->sModuleSpotName);
                } else {
                    $defaultView = $this->aModuleConfig['view'];
                }

                if (array_key_exists('permittedModules', $this->aModuleConfig)) {
                    if (array_key_exists($this->oModule->sqlData['classname'], $this->aModuleConfig['permittedModules'])) {
                        $this->oModule->aPermittedViews = $this->aModuleConfig['permittedModules'][$this->oModule->sqlData['classname']];
                    }
                }

                if (is_array($this->oModule->aPermittedViews) && count($this->oModule->aPermittedViews) > 0) { // module views are restricted on current spot
                    if (!in_array($defaultView, $this->oModule->aPermittedViews)) { // check if view is allowed
                        $defaultView = $this->oModule->aPermittedViews[0]; // set view to first allowed view
                    }
                }
                $this->oModuleInstance->sqlData['template'] = $defaultView;
                $this->oModuleInstance->fieldTemplate = $defaultView;
            } else {
                // the instance no longer exists... this is an error... we clear the spot
                $this->_ClearInstance();
                $this->oModuleInstance = null;
                $this->oModule = null;
            }
        }
    }

    /**
     * Get the default view for a module spot.
     *
     * returns the first view of the module or "standard" if no views are defined.
     */
    private function GetDefaultView(TdbCmsTplPage $oCmsTplPage, TdbCmsTplModuleInstance $oCmsTplModuleInstance, TdbCmsTplModule $oCmsTplModule, string $sModuleInstanceSpotName): string
    {
        $viewList = $oCmsTplModule->GetViews();

        if (null === $viewList) {
            return 'standard';
        }

        return $viewList->current();
    }

    /**
     * Define the functions that can be called from the website.
     */
    public function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = ['NewInstance', 'ClearInstance', 'DeleteInstance', 'ChangeView', 'RenameInstance', 'SetInstance', 'CopyInstance', 'SwitchInstances'];
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    /**
     * set the view template of the model.
     */
    protected function SetTemplate($modelName, $name)
    {
        $this->viewTemplate = PATH_CORE_MODULES.$modelName.'/views/'.$name.'.view.php';
    }

    /**
     * places the module list into the view $data.
     */
    protected function GetModuleList()
    {
        static $oModuleList = null;

        if (is_null($oModuleList)) {
            /** @var SecurityHelperAccess $securityHelper */
            $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
            $oModuleList = new TCMSRecordList();
            $oModuleList->sTableObject = 'TCMSTPLModule';
            $oModuleList->sTableName = 'cms_tpl_module';
            // add restrictions if present
            $sListRestriction = '';
            $query = 'SELECT DISTINCT `cms_tpl_module`.*
                 FROM `cms_tpl_module`
            LEFT JOIN `cms_tpl_module_cms_usergroup_mlt` ON `cms_tpl_module`.`id` = `cms_tpl_module_cms_usergroup_mlt`.`source_id`
            LEFT JOIN `cms_tpl_module_cms_portal_mlt` ON `cms_tpl_module`.`id` = `cms_tpl_module_cms_portal_mlt`.`source_id`
              ';

            if (empty($sListRestriction)) {
                $sListRestriction .= ' WHERE ';
            } else {
                $sListRestriction .= ' AND ';
            }

            $sListRestriction .= "`cms_tpl_module`.`show_in_template_engine` = '1'";

            $query .= $sListRestriction;

            $sUserGroupRestriction = '';
            $sGroupList = '';
            $userGroups = $securityHelper->getUser()?->getGroups();
            if (null !== $userGroups && count($userGroups) > 0) {
                $sGroupList = implode(', ', array_map(fn (string $groupId) => $this->getDatabaseConnection()->quote($groupId), array_keys($userGroups)));
            }
            if (!empty($sGroupList)) {
                $sUserGroupRestriction = " OR `cms_tpl_module_cms_usergroup_mlt`.`target_id` IN ({$sGroupList})";
            }
            $query .= " AND (`cms_tpl_module`.`is_restricted` = '0'{$sUserGroupRestriction})";
            // add portal restrictions
            $portalList = $securityHelper->getUser()?->getPortals();
            $sPortalList = '';
            if (null !== $portalList && count($portalList) > 0) {
                $sPortalList = implode(', ', array_map(fn (string $portalId) => $this->getDatabaseConnection()->quote($portalId), array_keys($portalList)));
            }
            if (!empty($sPortalList)) {
                $sPortalRestriction = ' OR `cms_tpl_module_cms_portal_mlt`.`target_id` IN ('.$sPortalList.')';
            }
            $query .= ' AND (
          (SELECT COUNT(`target_id`) FROM `cms_tpl_module_cms_portal_mlt` WHERE `source_id` = `cms_tpl_module`.`id`)=0
          '.$sPortalRestriction.'
          ) ';

            $nameField = $this->getFieldTranslationUtil()->getTranslatedFieldName('cms_tpl_module', 'name');
            $quotedNameField = $this->getDatabaseConnection()->quoteIdentifier($nameField);
            $query .= " ORDER BY $quotedNameField";

            $oModuleList = TdbCmsTplModuleList::GetList($query);
        }
        $this->data['oModuleList'] = $oModuleList;
    }

    /**
     * external function: creates a new instance of a module.
     */
    public function NewInstance()
    {
        $oPage = new TCMSPage();
        $sPageId = $this->global->GetUserData('pagedef');
        $oPage->Load($sPageId);
        $sName = $oPage->sqlData['name'].' ('.$oPage->GetPortal()->GetName().')';
        $moduleID = $this->global->GetUserData('moduleid');
        $view = $this->global->GetUserData('view');
        $this->OnCreateInstance($sName, $moduleID, $view, $oPage);
        $this->LoadModuleInstanceData();
        $this->UpdatePageMasterPagedefSpot($sPageId, $this->instanceID, $view, $this->oModule->fieldClassname);
        // if we have only one table connected to the module... and that table is a one-record-only, then open the edit view
        $oModule = TdbCmsTplModule::GetNewInstance();
        $aRedirectParameters = [];
        if ($oModule->Load($moduleID)) {
            $oConnectedTables = $oModule->GetFieldCmsTblConfList();
            if ($oConnectedTables && 1 === $oConnectedTables->Length()) {
                $oTable = $oConnectedTables->Current();
                if ($oTable->fieldOnlyOneRecordTbl) {
                    $aURLParam = ['pagedef' => 'tablemanagerframe', 'id' => $oTable->id, 'sRestrictionField' => 'cms_tpl_module_instance_id', 'sRestriction' => $this->instanceID];
                    $sURL = PATH_CMS_CONTROLLER.'?'.str_replace('&amp;', '&', $this->getUrlUtil()->getArrayAsUrl($aURLParam));
                    $aRedirectParameters['sRedirectURL'] = $sURL;
                }
            }
        }
        $this->RedirectToEditPage($sPageId, $aRedirectParameters);
    }

    /**
     * Insert or Update page module spot connection to module instance.
     *
     * @param string $sPageId
     * @param string $sModuleInstanceId
     * @param string $sView
     * @param string $sModuleName
     *
     * @return TdbCmsTplPageCmsMasterPagedefSpot
     */
    protected function UpdatePageMasterPagedefSpot($sPageId, $sModuleInstanceId, $sView, $sModuleName)
    {
        $oPageDefinition = TCMSPagedef::GetCachedInstance($sPageId);
        $sMasterPageDefSpotId = $oPageDefinition->aModuleList[$this->sModuleSpotName]->id;
        $sPageMasterPagedefSpot = $this->GetCmsTplPageCmsMasterPagedefSpot();
        if (null === $sPageMasterPagedefSpot) {
            $sPageMasterPagedefSpotId = null;
            $aData = [];
        } else {
            $sPageMasterPagedefSpotId = $sPageMasterPagedefSpot->id;
            $aData = $sPageMasterPagedefSpot->sqlData;
        }
        $oPageMasterPagedefSpotManager = TTools::GetTableEditorManager('cms_tpl_page_cms_master_pagedef_spot', $sPageMasterPagedefSpotId);
        $aData['cms_tpl_page_id'] = $sPageId;
        $aData['cms_master_pagedef_spot_id'] = $sMasterPageDefSpotId;
        $aData['cms_tpl_module_instance_id'] = $sModuleInstanceId;
        $aData['view'] = $sView;
        $aData['model'] = $sModuleName;
        $oPageMasterPagedefSpotManager->AllowEditByAll(true);
        $oPageMasterPagedefSpotManager->ForceHiddenFieldWriteOnSave(true);
        if (false !== $oPageMasterPagedefSpotManager->Save($aData)) {
            $this->oCmsTplPageCmsMasterPagedefSpot = $oPageMasterPagedefSpotManager->oTableEditor->oTable;
            $this->TriggerWorkflowLockForPage($sPageId);
        }

        return $this->oCmsTplPageCmsMasterPagedefSpot;
    }

    /**
     * copy the existing Module instance.
     */
    public function CopyInstance()
    {
        $oPage = new TCMSPage();
        $sPageId = $this->global->GetUserData('pagedef');
        $sModuleInstanceName = $this->global->GetUserData('instancename');
        $oPage->Load($sPageId);
        $sName = sprintf('%s [%s]', $sModuleInstanceName, $this->getTranslator()->trans(
            'chameleon_system_core.text.copied_record_marker', [], TranslationConstants::DOMAIN_BACKEND
        ));
        $moduleID = $this->global->GetUserData('moduleid');
        $view = $this->global->GetUserData('view');
        $sOldModuleInstanceId = $this->global->GetUserData('moduleinstanceid');
        $sNewModuleInstanceId = $this->OnCreateInstance($sName, $moduleID, $view, $oPage);
        $this->LoadModuleInstanceData();

        // now we need to update the pagedef
        $this->UpdatePageMasterPagedefSpot($sPageId, $this->instanceID, $view, $this->oModule->fieldClassname);
        $this->CopyModuleTableRecords($moduleID, $sOldModuleInstanceId, $sNewModuleInstanceId);
    }

    /**
     * Copy all table records connected with the old module instance.
     *
     * @param string $moduleID
     * @param string $sOldModlueInstanceId
     * @param string $sNewModuleInstanceId
     *
     * @return bool $bCopySuccess
     */
    protected function CopyModuleTableRecords($moduleID, $sOldModlueInstanceId, $sNewModuleInstanceId)
    {
        $bCopySuccess = false;
        $oUsedModule = TdbCmsTplModule::GetNewInstance();
        if ($oUsedModule->Load($moduleID)) {
            $oModuleUsedTableList = $oUsedModule->GetFieldCmsTblConfList();
            if ($oModuleUsedTableList->Length() > 0) {
                while ($oModuleUsedTable = $oModuleUsedTableList->Next()) {
                    $oRecordListToCopy = $this->GetRecordsToCopy($oModuleUsedTable->fieldName, $sOldModlueInstanceId);
                    $bCopySuccess = $this->CopyRecords($oRecordListToCopy, $sNewModuleInstanceId);
                }
            }
        }

        return $bCopySuccess;
    }

    /**
     * Get all records from given table with instance id from the copied module instance.
     *
     * @param string $sTableName
     * @param string $sOldModuleInstanceId
     *
     * @return TIterator $oRecordListToCopy
     */
    protected function GetRecordsToCopy($sTableName, $sOldModuleInstanceId)
    {
        $oRecordListToCopy = new TIterator();

        $sClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $sTableName.'List');

        /** @var $oTableList TCMSRecordList */
        $oTableList = new $sClassName();
        if (TTools::FieldExists($sTableName, 'cms_tpl_module_instance_id')) {
            $sQuery = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($sTableName)."` WHERE `cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sOldModuleInstanceId)."'";
            /** @var $oRecordToCopyList TCMSRecordList */
            $oRecordToCopyList = $oTableList::GetList($sQuery);
            while ($oRecordToCopy = $oRecordToCopyList->Next()) {
                $oRecordListToCopy->AddItem($oRecordToCopy);
            }
        }

        return $oRecordListToCopy;
    }

    /**
     * Copy given records and connect it to the new module instance.
     *
     * @param TIterator $oRecordListToCopy
     * @param string $sNewModuleInstanceId
     *
     * @return bool $bSuccess
     */
    protected function CopyRecords($oRecordListToCopy, $sNewModuleInstanceId)
    {
        $bSuccess = false;
        /** @var $oRecordToCopy TCMSRecord */
        while ($oRecordToCopy = $oRecordListToCopy->Next()) {
            $iTableID = $this->getTools()::GetCMSTableId($oRecordToCopy->table);
            $oTableEditor = new TCMSTableEditorManager();
            $oTableEditor->Init($iTableID, $oRecordToCopy->id);
            $aOverloadedFields = ['cms_tpl_module_instance_id' => $sNewModuleInstanceId];
            $bSuccess = false !== $oTableEditor->DatabaseCopy(false, $aOverloadedFields);
        }

        return $bSuccess;
    }

    /**
     * set a new module/instance into the current spot...
     * if bLoadCopy ist set via URL the instance is loaded and then copied.
     *
     * redirects back to the page in edit mode
     */
    public function SetInstance()
    {
        $sPageId = $this->global->GetUserData('pagedef');
        $sInstanceID = $this->global->GetUserData('instanceid');
        $bLoadCopy = $this->global->GetUserData('bLoadCopy');
        $this->instanceID = $sInstanceID;
        $this->LoadModuleInstanceData();
        $view = $this->oModuleInstance->sqlData['template'];
        /** loads TdbCmsTplModuleInstance into $this->oModuleInstance */
        if ($this->global->UserDataExists('template') && '' !== $this->global->GetUserData('template')) {
            $view = $this->global->GetUserData('template');
        }

        if (!empty($bLoadCopy) && '1' === $bLoadCopy) {
            $this->CopyInstanceData();
        } else {
            $this->UpdatePageMasterPagedefSpot($sPageId, $this->instanceID, $view, $this->oModule->fieldClassname);
        }
        $this->RedirectToEditPage($sPageId);
    }

    /**
     * copy the existing Module instance
     * is used by SetInstance() if bLoadCopy was set in module loading call.
     */
    protected function CopyInstanceData()
    {
        $oPage = new TCMSPage();
        $sPageId = $this->global->GetUserData('pagedef');
        $oPage->Load($sPageId);

        $sModuleInstanceName = $this->oModuleInstance->GetName();
        $sName = sprintf('%s [%s]', $sModuleInstanceName, $this->getTranslator()->trans(
            'chameleon_system_core.text.copied_record_marker', [], TranslationConstants::DOMAIN_BACKEND
        ));

        $moduleID = $this->oModuleInstance->fieldCmsTplModuleId;
        $view = ($this->global->UserDataExists('template') && '' !== $this->global->GetUserData('template')) ? ($this->global->GetUserData('template')) : ($this->oModuleInstance->fieldTemplate);
        $sOldModuleInstanceId = $this->oModuleInstance->id;
        $sNewModuleInstanceId = $this->OnCreateInstance($sName, $moduleID, $view, $oPage);
        $this->LoadModuleInstanceData();

        // now we need to update the pagedef
        $this->UpdatePageMasterPagedefSpot($sPageId, $this->instanceID, $view, $this->oModule->fieldClassname);
        $this->CopyModuleTableRecords($moduleID, $sOldModuleInstanceId, $sNewModuleInstanceId);
    }

    /**
     * external function: remove the instance from the page.
     */
    public function ClearInstance()
    {
        $sPageId = $this->global->GetUserData('pagedef');
        // and now redirect to the current page...
        $this->_ClearInstance();
        $this->RedirectToEditPage($sPageId);
    }

    /**
     * clear the module spot.
     */
    protected function _ClearInstance()
    {
        if (null !== $this->instanceID) {
            // check for page spot record

            $oSpot = $this->GetCmsTplPageCmsMasterPagedefSpot();
            if (null !== $oSpot) {
                $oTableEditor = new TCMSTableEditorManager();
                $iTableID = TTools::GetCMSTableId('cms_tpl_page_cms_master_pagedef_spot');
                $oTableEditor->Init($iTableID, $oSpot->id);
                $oTableEditor->AllowDeleteByAll(true);
                $oTableEditor->Delete($oSpot->id);
                $this->oCmsTplPageCmsMasterPagedefSpot = null; // reset spot...
            }
        }
        $this->TriggerWorkflowLockForPage();
        $this->oModule = null;
        $this->instanceID = null;
    }

    /**
     * external function: changes the view of the current instance.
     */
    public function ChangeView()
    {
        $this->LoadModuleInstanceData();
        $sPageId = $this->global->GetUserData('pagedef');
        $view = $this->global->GetUserData('view');

        $iTableID = $this->getTools()::GetCMSTableId('cms_tpl_module_instance');
        $oTableEditor = new TCMSTableEditorManager();
        $oTableEditor->Init($iTableID, $this->instanceID);

        $oTableEditor->SaveField('template', $view);

        $this->UpdatePageMasterPagedefSpot($sPageId, $this->instanceID, $view, $this->oModule->fieldClassname);
        $this->TriggerWorkflowLockForPage();
        $this->RedirectToEditPage($sPageId);
    }

    /**
     * rename the instance.
     */
    public function RenameInstance()
    {
        $this->LoadModuleInstanceData();
        $sPageId = $this->global->GetUserData('pagedef');
        $instanceName = $this->global->GetUserData('instancename');

        $iTableID = TTools::GetCMSTableId('cms_tpl_module_instance');
        $oTableEditor = new TCMSTableEditorManager();
        $oTableEditor->Init($iTableID, $this->instanceID);
        $oTableEditor->SaveField('name', $instanceName);

        $this->TriggerWorkflowLockForPage();
        $this->RedirectToEditPage($sPageId);
    }

    /**
     * delete the instance from the page.
     */
    public function DeleteInstance()
    {
        $sTmpInstanceID = $this->instanceID;
        $this->OnDeleteInstance();
        $this->instanceID = $sTmpInstanceID;
        $this->_ClearInstance();
        $sPageId = $this->global->GetUserData('pagedef');
        $this->RedirectToEditPage($sPageId);
    }

    /**
     * update the pagedef using the current instance information.
     *
     * @param string $sPageId the page id
     * @param string $view the view used by the instance
     */
    protected function UpdatePagedef($sPageId, $view = null)
    {
        $oPageDefinition = TCMSPagedef::GetCachedInstance($sPageId);

        // update instance of current spot
        if (!is_null($this->oModule)) {
            $oPageDefinition->UpdateModule($this->sModuleSpotName, $this->oModule->sqlData['classname'], $view, $this->instanceID);
        }
        $this->TriggerWorkflowLockForPage($sPageId);
        $oPageDefinition->Save();
    }

    /**
     * cms calls this function to create an instance of the module. overwrite it
     * to do additional processing (like creating a record in a related table).
     *
     * @param string $sName name of the instance
     * @param int $moduleID id of the module
     * @param string $view name of the view
     * @param TCMSPage $oPage the current page record
     *
     * @return string
     */
    protected function OnCreateInstance($sName, $moduleID, $view, $oPage)
    {
        $iTableID = TTools::GetCMSTableId('cms_tpl_module_instance');
        $oEditor = new TCMSTableEditorManager();
        $oEditor->Init($iTableID, null);
        $aData = ['name' => $sName, 'cms_tpl_module_id' => $moduleID, 'template' => $view, 'cms_portal_id' => $oPage->sqlData['cms_portal_id']];
        $oEditor->ForceHiddenFieldWriteOnSave(true);
        $oEditor->Save($aData);
        $this->instanceID = $oEditor->sId;
        $this->TriggerWorkflowLockForPage();

        return $this->instanceID;
    }

    /**
     * removes the module instance from the database. overwrite the function
     * to remove related tables.
     */
    protected function OnDeleteInstance()
    {
        $iTableID = $this->getTools()::GetCMSTableId('cms_tpl_module_instance');
        $oEditor = new TCMSTableEditorManager();
        $oEditor->Init($iTableID, $this->instanceID);
        $oEditor->Delete($this->instanceID);
        $this->TriggerWorkflowLockForPage();
        $this->instanceID = null;
    }

    /**
     * Return the spot info for the current page and spot.
     * Delete Not needed older module instances.
     *
     * @return TdbCmsTplPageCmsMasterPagedefSpot
     */
    protected function GetCmsTplPageCmsMasterPagedefSpot()
    {
        $sPageId = $this->global->GetUserData('pagedef');
        if (is_null($this->oCmsTplPageCmsMasterPagedefSpot)) {
            $this->oCmsTplPageCmsMasterPagedefSpot = TdbCmsTplPageCmsMasterPagedefSpot::GetNewInstance();
            $query = "SELECT DISTINCT `cms_tpl_page_cms_master_pagedef_spot`.*
                    FROM `cms_tpl_page_cms_master_pagedef_spot`
              INNER JOIN `cms_master_pagedef_spot` ON `cms_tpl_page_cms_master_pagedef_spot`.`cms_master_pagedef_spot_id` = `cms_master_pagedef_spot`.`id`
                   WHERE `cms_tpl_page_cms_master_pagedef_spot`.`cms_tpl_page_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sPageId)."'
                     AND `cms_master_pagedef_spot`.`name` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sModuleSpotName)."'
                 ";
            $TdbCmsTplPageCmsMasterPagedefSpotList = TdbCmsTplPageCmsMasterPagedefSpotList::GetList($query);
            if ($TdbCmsTplPageCmsMasterPagedefSpotList->Length() > 1) {
                $this->DeleteNotNeededModuleInstances($sPageId, $TdbCmsTplPageCmsMasterPagedefSpotList);
            } elseif (1 === $TdbCmsTplPageCmsMasterPagedefSpotList->Length()) {
                $this->oCmsTplPageCmsMasterPagedefSpot = $TdbCmsTplPageCmsMasterPagedefSpotList->Current();
            } else {
                $this->oCmsTplPageCmsMasterPagedefSpot = null;
            }
        }

        return $this->oCmsTplPageCmsMasterPagedefSpot;
    }

    /**
     * More than one instance in one spot.
     * we remove all except the one for the current template (just a fallback to fix older entries).
     *
     * @param string $sPageId
     * @param TdbCmsTplPageCmsMasterPagedefSpotList $TdbCmsTplPageCmsMasterPagedefSpotList
     */
    protected function DeleteNotNeededModuleInstances($sPageId, $TdbCmsTplPageCmsMasterPagedefSpotList)
    {
        $this->oCmsTplPageCmsMasterPagedefSpot = $this->GetRealModuleInstanceForCurrentTemplate($sPageId);
        /** @var $oSpotToDelete TdbCmsTplPageCmsMasterPagedefSpot */
        while ($oSpotToDelete = $TdbCmsTplPageCmsMasterPagedefSpotList->Next()) {
            if ($oSpotToDelete->id != $this->oCmsTplPageCmsMasterPagedefSpot->id) {
                $iTableID = TTools::GetCMSTableId('cms_tpl_page_cms_master_pagedef_spot');
                $oTableEditorSpotToDelete = new TCMSTableEditorManager();
                $oTableEditorSpotToDelete->Init($iTableID, $oSpotToDelete->id);
                $oTableEditorSpotToDelete->AllowDeleteByAll(true);
                $oTableEditorSpotToDelete->Delete($oSpotToDelete->id);
                $oTableEditorSpotToDelete->AllowDeleteByAll(false);
            }
        }
    }

    /**
     * Return the real module instance of the template and page.
     *
     * @param string $sPageId
     *
     * @return TdbCmsTplPageCmsMasterPagedefSpot
     */
    protected function GetRealModuleInstanceForCurrentTemplate($sPageId)
    {
        $oPage = TdbCmsTplPage::GetNewInstance();
        $oPage->Load($sPageId);
        $query_currentTemplate = "SELECT DISTINCT `cms_tpl_page_cms_master_pagedef_spot`.*
                                           FROM `cms_tpl_page_cms_master_pagedef_spot`
                                     INNER JOIN `cms_master_pagedef_spot` ON `cms_tpl_page_cms_master_pagedef_spot`.`cms_master_pagedef_spot_id` = `cms_master_pagedef_spot`.`id`
                                          WHERE `cms_tpl_page_cms_master_pagedef_spot`.`cms_tpl_page_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sPageId)."'
                                            AND `cms_master_pagedef_spot`.`name` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sModuleSpotName)."'
                               ";
        $TdbCmsTplPageCmsMasterPagedefSpotListPage = TdbCmsTplPageCmsMasterPagedefSpotList::GetList($query_currentTemplate);

        return $TdbCmsTplPageCmsMasterPagedefSpotListPage->Current();
    }

    /**
     * external function: switches two instances.
     */
    public function SwitchInstances()
    {
        $sPageId = $this->global->GetUserData('pagedef');
        $sTargetModuleSpotName = $this->global->GetUserData('sTargetModuleSpotName');
        if ($this->global->UserDataExists('sTargetModuleSpotName')) {
            $oSourceSpot = $this->GetCmsTplPageCmsMasterPagedefSpot();
            $activePage = $this->getActivePageService()->getActivePage();
            $TdbCmsTplPageCmsMasterPagedefTargetSpotList = $this->GetSpotInstanceList($activePage, $sTargetModuleSpotName);
            if ($TdbCmsTplPageCmsMasterPagedefTargetSpotList->Length() > 0) {
                $this->SwitchInstanceToUsedSpot($TdbCmsTplPageCmsMasterPagedefTargetSpotList->Current(), $oSourceSpot);
            } else {
                $this->SwitchInstanceToEmptySpot($sTargetModuleSpotName, $oSourceSpot);
            }
            $this->TriggerWorkflowLockForPage($sPageId);
            $this->RedirectToEditPage($sPageId);
        }
    }

    /**
     * Get list of installed module instances of a spot. Normally this should be one or none.
     *
     * @param TCMSPage $oPage
     * @param string $sTargetModuleSpotName
     *
     * @return TdbCmsTplPageCmsMasterPagedefSpotList $TdbCmsTplPageCmsMasterPagedefSpotList
     */
    protected function GetSpotInstanceList($oPage, $sTargetModuleSpotName)
    {
        $query = "SELECT DISTINCT `cms_tpl_page_cms_master_pagedef_spot`.*
                      FROM `cms_tpl_page_cms_master_pagedef_spot`
                INNER JOIN `cms_master_pagedef_spot` ON `cms_tpl_page_cms_master_pagedef_spot`.`cms_master_pagedef_spot_id` = `cms_master_pagedef_spot`.`id`
                     WHERE `cms_tpl_page_cms_master_pagedef_spot`.`cms_tpl_page_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oPage->id)."'
                       AND `cms_master_pagedef_spot`.`name` = '".MySqlLegacySupport::getInstance()->real_escape_string($sTargetModuleSpotName)."'
                   ";

        return TdbCmsTplPageCmsMasterPagedefSpotList::GetList($query);
    }

    protected function RedirectToEditPage(string $sPageId, array $aOtherParameters = [])
    {
        $urlParameters = [
            'pagedef' => $sPageId,
            'id' => $sPageId,
            '__modulechooser' => 'true',
        ];
        // we need to preserve certain parameters to avoid loading frontend js
        $additionalUrlParameters = [
            'esdisablelinks',
            '__previewmode',
            'previewLanguageId',
        ];
        // to redirect the page outside the iframe, we use javascript in a script tag included in the HTMLHeadIncludes. Therefore, we need to avoid the removal of all frontend script tags, if we are going to redirect the page. (by passing 'sRedirectURL' in  $aOtherParameters). So if this parameter is present, we avoid retaining the value of 'esdisablefrontendjs'.
        if (!isset($aOtherParameters['sRedirectURL'])) {
            $additionalUrlParameters[] = 'esdisablefrontendjs';
        }
        foreach ($additionalUrlParameters as $parameterName) {
            if ($this->global->UserDataExists($parameterName)) {
                $urlParameters[$parameterName] = $this->global->GetUserData($parameterName);
            }
        }
        foreach ($aOtherParameters as $sKey => $sValue) {
            $urlParameters[$sKey] = $sValue;
        }

        $url = PATH_CMS_CONTROLLER_FRONTEND.'?'.http_build_query($urlParameters);

        $this->getRedirectService()->redirect($url);
    }

    /**
     * Switch instance to a used spot and switch the instance in the used spot to the source spot.
     *
     * @param TdbCmsTplPageCmsMasterPagedefSpot $oTargetSpot
     * @param TdbCmsTplPageCmsMasterPagedefSpot $oSourceSpot
     */
    protected function SwitchInstanceToUsedSpot($oTargetSpot, $oSourceSpot)
    {
        if (false === is_object($oSourceSpot) || false === is_object($oTargetSpot)) {
            return;
        }
        $aSourceSpot = $oSourceSpot->sqlData;
        $aTargetSpot = $oTargetSpot->sqlData;
        $bAllowed = $oTargetSpot->CheckAccess($oSourceSpot->fieldModel, $oSourceSpot->fieldView);
        $bAllowed = ($bAllowed && $oSourceSpot->CheckAccess($oTargetSpot->fieldModel, $oTargetSpot->fieldView));
        if ($bAllowed) {
            $this->SaveSwitchedSpotIdToInstance($oSourceSpot->id, $aTargetSpot['cms_master_pagedef_spot_id']);
            $this->SaveSwitchedSpotIdToInstance($oTargetSpot->id, $aSourceSpot['cms_master_pagedef_spot_id']);
        }
    }

    /**
     * Switch instance to an empty spot.
     *
     * @param string $sTargetModuleSpotName
     * @param TdbCmsTplPageCmsMasterPagedefSpot $oSourceSpot
     */
    protected function SwitchInstanceToEmptySpot($sTargetModuleSpotName, $oSourceSpot)
    {
        $oOldPagedefSpot = TdbCmsMasterPagedefSpot::GetNewInstance();
        $oOldPagedefSpot->Load($oSourceSpot->fieldCmsMasterPagedefSpotId);
        $oNewPagedefSpot = TdbCmsMasterPagedefSpot::GetNewInstance();
        $oNewPagedefSpot->LoadFromFields(['name' => $sTargetModuleSpotName, 'cms_master_pagedef_id' => $oOldPagedefSpot->fieldCmsMasterPagedefId]);
        $bAllowed = $oNewPagedefSpot->CheckAccess($oSourceSpot->fieldModel, $oSourceSpot->fieldView);
        $bAllowed = $bAllowed && (!empty($oSourceSpot->fieldCmsTplModuleInstanceId) && 'MTEmpty' !== $oSourceSpot->fieldModel);
        if ($bAllowed) {
            $this->SaveSwitchedSpotIdToInstance($oSourceSpot->id, $oNewPagedefSpot->id);
        }
    }

    /**
     * Save new spot id to module instance.
     *
     * @param string $sModuleInstanceId
     * @param string $sNewSpotId
     */
    protected function SaveSwitchedSpotIdToInstance($sModuleInstanceId, $sNewSpotId)
    {
        $oTableEditor = $this->getTools()::GetTableEditorManager('cms_tpl_page_cms_master_pagedef_spot', $sModuleInstanceId);
        $oTableEditor->oTableEditor->ForceHiddenFieldWriteOnSave(true);
        $oTableEditor->AllowEditByAll(true);
        $oTableEditor->SaveField('cms_master_pagedef_spot_id', $sNewSpotId);
    }

    /**
     * performs a dummy save in the name field to create a lock on the page.
     */
    protected function TriggerWorkflowLockForPage(?string $sPageId = null)
    {
        if (false === $sPageId) {
            $sPageId = $this->global->GetUserData('pagedef');
        }
        $oTableEditor = new TCMSTableEditorManager();
        $oTableEditor->Init($this->getTools()::GetCMSTableId('cms_tpl_page'), $sPageId);
        $oTableEditor->RefreshLock();
    }

    private function createModuleMenuHtml()
    {
        $cache = $this->getCacheManager();
        $key = $cache->getKey([
            'class' => __CLASS__,
            'method' => 'createModuleMenuHtml',
            'aPermittedModules' => $this->data['aPermittedModules'],
        ]);
        $content = $cache->get($key);
        if (null === $content) {
            $viewRenderer = $this->getViewRenderer();
            $viewRenderer->addMapperFromIdentifier('chameleon_system_core.mapper.template_engine.module_chooser_module_list');
            $viewRenderer->AddSourceObjectsFromArray(
                [
                    'moduleList' => $this->data['oModuleList'],
                    'aPermittedModules' => $this->data['aPermittedModules'],
                ]
            );
            $content = $viewRenderer->Render('CMSModuleChooser/moduleList.html.twig');

            $cache->set($key, $content, [
                    ['table' => 'cms_user', 'id' => null],
                    ['table' => 'cms_tpl_module', 'id' => null],
                ]
            );
        }

        return str_replace('[{replaceSpotName}]', $this->data['sModuleSpotName'], $content);
    }

    private function getActivePageService(): ActivePageServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.active_page_service');
    }

    private function getCacheManager(): CacheInterface
    {
        return ServiceLocator::get('chameleon_system_core.cache');
    }

    private function getDatabaseConnection(): Connection
    {
        return ServiceLocator::get('database_connection');
    }

    private function getViewRenderer(): ViewRenderer
    {
        return ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
    }

    private function getFieldTranslationUtil(): FieldTranslationUtil
    {
        return ServiceLocator::get('chameleon_system_core.util.field_translation');
    }

    private function getTranslator(): TranslatorInterface
    {
        return ServiceLocator::get('translator');
    }

    private function getRedirectService(): ICmsCoreRedirect
    {
        return ServiceLocator::get('chameleon_system_core.redirect');
    }

    private function getTools(): TTools
    {
        return ServiceLocator::get('chameleon_system_core.tools');
    }

    private function getUrlUtil(): UrlUtil
    {
        return ServiceLocator::get('chameleon_system_core.util.url');
    }

    private function getPortalDomainService(): PortalDomainServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }

    private function getCurrentRequest(): Request
    {
        return ServiceLocator::get('request_stack')->getCurrentRequest();
    }
}

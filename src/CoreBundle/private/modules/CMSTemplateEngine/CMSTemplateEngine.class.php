<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;

/**
 * module manages the page-system of the webpage. in it we assume that a page
 * has been selected, and now some operations are performed on that page (assigning a tree node,
 * picking a layout, placing modules into that layout, and editing the modules content).
/**/
class CMSTemplateEngine extends TCMSModelBase
{
    /**
     * is true if a page has a layout definition file set (pagedef)
     * if false the template engine redirects to the layout manager.
     *
     * @var bool
     */
    protected $bPageDefinitionAssigned = false;

    /**
     * cms_tpl_page id.
     *
     * @var string
     */
    protected $sPageId = null;

    /**
     * the active layout.
     *
     * @var CMSTemplateEngineLayout
     */
    protected $oActiveLayout = null;

    /**
     * the requested mode (function or component) of the template engine being called.
     *
     * @var string
     */
    protected $sMode = null;

    /**
     * the page being viewed.
     *
     * @var TCMSPagedef
     */
    protected $oPage = null;

    /**
     * the portal of the page being viewed.
     *
     * @var TCMSPortal
     */
    protected $oPortal = null;

    /**
     * the table manager for.
     *
     * @var TCMSTableEditor
     */
    protected $oTableManager = null;

    /**
     * table id of cms_tpl_page.
     *
     * @var string
     */
    protected $sTableID = null;

    /**
     * array of all modules of all spots of the page.
     *
     * @var array - key = spotname
     */
    protected $aModuleList = null;

    /**
     * indicates if the record is rendered in readonly mode.
     *
     * @var bool
     */
    protected $bIsReadOnlyMode = false;

    /**
     * {@inheritdoc}
     */
    public function Init()
    {
        $cspRules = CMS_AUTO_SEND_BACKEND_HEADER_CONTENT_SECURITY_POLICY;
        if (!empty($cspRules)) {
            $aHeaderNames = array('X-WebKit-CSP', 'X-Content-Security-Policy', 'Content-Security-Policy');
            foreach ($aHeaderNames as $csp) {
                header($csp.': '.$cspRules);
            }
        }

        parent::Init();

        $this->sPageId = $this->getPageIdFromRequest();
        $this->oTableManager = new TCMSTableEditorManager();
        $this->sTableID = TTools::GetCMSTableId('cms_tpl_page');
        $this->oTableManager->Init($this->sTableID, $this->sPageId);
        $this->oPage = TCMSPagedef::GetCachedInstance($this->sPageId);
        if ($this->global->UserDataExists('_mode')) {
            $this->sMode = $this->global->GetUserData('_mode');
        }
        $this->bPageDefinitionAssigned = (!empty($this->oPage->iMasterPageDefId));
        $this->AddURLHistory();

        // load portal object
        $this->oPortal = &TdbCmsPortal::GetPagePortal($this->sPageId);
        $this->data['aNavigationBreadCrumbs'] = $this->GetNavigationBreadCrumbs();

        $this->bIsReadOnlyMode = $this->oTableManager->oTableEditor->IsRecordInReadOnlyMode();
    }

    /**
     * @return null|string
     */
    private function getPageIdFromRequest()
    {
        $filter = $this->getInputFilter();

        return $filter->getFilteredInput('id');
    }

    /**
     * returns true if a main navigation is set for the page
     * get´s called via ajax by the button "templates" button in template engine
     * prevents switching to the template list without a navigation set which
     * may cause problems in the template because navigations are mandatory for
     * 99% of all page templates.
     *
     * @return TCMSstdClass
     */
    public function IsMainNavigationSet()
    {
        $oRecordData = null;

        $bMainNavigationIsSet = false;
        $oCmsTplPage = TdbCmsTplPage::GetNewInstance();
        /** @var $oCmsTplPage TdbCmsTplPage */
        $oCmsTplPage->Load($this->sPageId);
        if (!empty($oCmsTplPage->fieldPrimaryTreeIdHidden)) {
            $bMainNavigationIsSet = true;
        }

        $oRecordData = new TCMSstdClass();
        /** @var $oReturnData TCMSstdClass */
        $oRecordData->bMainNavigationIsSet = $bMainNavigationIsSet;
        $oRecordData->sPageId = $this->sPageId;
        $oRecordData->sToasterErrorMessage = TGlobal::Translate('chameleon_system_core.template_engine.error_primary_navigation_node_required_before_layout_selection');

        return $oRecordData;
    }

    /**
     * checks if record is currently locked by other editor.
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

    public function AddURLHistory()
    {
        if ($this->AllowAddingURLToHistory()) {
            $params = array();
            $params['pagedef'] = $this->global->GetUserData('pagedef');
            $params['id'] = $this->global->GetUserData('id');

            if ($this->global->UserDataExists('sRestriction')) {
                $params['sRestriction'] = $this->global->GetUserData('sRestriction');
            }
            if ($this->global->UserDataExists('sRestrictionField')) {
                $params['sRestrictionField'] = $this->global->GetUserData('sRestrictionField');
            }
            $breadcrumbTitle = TGlobal::Translate('chameleon_system_core.template_engine.breadcrumb_title_page').': '.$this->oPage->sqlData['name'];
            if (is_null($this->sMode)) {
                $this->global->GetURLHistory()->AddItem($params, $breadcrumbTitle);
            }
        }
    }

    public function &Execute()
    {
        parent::Execute();

        $oMainNavigationSet = $this->IsMainNavigationSet();
        if (!$oMainNavigationSet->bMainNavigationIsSet) {
            $sURL = PATH_CMS_CONTROLLER.'?'.str_replace('&amp;', '&', TTools::GetArrayAsURL(array('pagedef' => 'tableeditor', 'tableid' => $this->sTableID, 'id' => $this->sPageId)));
            $this->controller->HeaderURLRedirect($sURL);
        }

        $this->IsRecordLocked();
        $this->GetPermissionSettings();

        $this->data['tableid'] = $this->sTableID;

        $this->data['oTable'] = $this->oTableManager->oTableEditor->oTable;
        $tableName = $this->oTableManager->oTableConf->sqlData['name'];
        $sTableTitle = $this->oTableManager->oTableConf->GetName();
        $this->data['sTableTitle'] = $sTableTitle;

        $sRecordName = $this->oTableManager->oTableEditor->oTable->GetName();
        $this->data['sRecordName'] = $sRecordName;
        $this->data['cmsident'] = $this->oTableManager->oTableEditor->oTable->sqlData['cmsident'];

        $this->data['id'] = $this->sPageId;

        $this->LoadRevisionData();

        $this->data['oPage'] = $this->oPage;
        if ($this->bPageDefinitionAssigned) {
            $this->data['sActivePageDef'] = $this->oPage->iMasterPageDefId;
            $languageId = TCMSUser::GetActiveUser()->GetCurrentEditLanguageID();
            $this->data['sActualMasterLayout'] = URL_WEB_CONTROLLER.'?pagedef='.$this->sPageId.'&__masterPageDef=true&__modulechooser=true&id='.$this->oPage->iMasterPageDefId.'&previewLanguageId='.$languageId;
        } else {
            $this->data['sActivePageDef'] = '';
        }

        if (is_null($this->sMode) || 'layout_selection' == $this->sMode || 'edit_content' == $this->sMode || 'preview_content' == $this->sMode) {
            $view = $this->GetMainNavigation(); // container call
            $this->SetTemplate('CMSTemplateEngine', $view);
            $this->data['sPreviewURL'] = $this->oTableManager->oTableEditor->GetPreviewURL();
        } else {
            if ('layoutlist' == $this->sMode) {
                $this->_GetLayoutList();
                $this->SetTemplate('CMSTemplateEngine', 'cmp_layoutlist');
            } elseif ('load_module' == $this->sMode) {
                $bLoadCopy = $this->global->GetUserData('bLoadCopy');
                $this->data['bLoadCopy'] = $bLoadCopy;

                $this->_GetModuleInstanceList();
                $this->SetTemplate('CMSTemplateEngine', 'cmp_loadmoduleinstance');
            }
        }

        return $this->data;
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
     * loads workflow relevant data.
     *
     * @deprecated since 6.2.0 - workflow is not supported anymore
     */
    protected function LoadWorkflowData()
    {
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
            if (1 == $this->oTableManager->oTableConf->sqlData['only_one_record_tbl']) {
                $permissions['new'] = false;
                $permissions['delete'] = false;
            } else {
                $permissions['new'] = $this->global->oUser->oAccessManager->HasNewPermission($this->oTableManager->oTableConf->sqlData['name']);

                $permissions['delete'] = $this->global->oUser->oAccessManager->HasDeletePermission($this->oTableManager->oTableConf->sqlData['name']);
            }
        }
        $this->data['aPermission'] = $permissions;
    }

    /**
     * returns the Breadcrumb navigations.
     */
    protected function GetNavigationBreadCrumbs()
    {
        $oPortals = &$this->global->GetPortals();
        $stopNodes = $oPortals->GetTreeNodes();
        $oBreadcrumbs = &$this->oPage->GetAllNavigationPaths($stopNodes);

        $total = $oBreadcrumbs->Length();

        $aNavigations = array();

        $naviCount = 0;
        while ($oBreadCrumb = $oBreadcrumbs->Next()) {
            $path = '';
            ++$naviCount;
            if ($naviCount == $total) {
                $margin = 0;
            } else {
                $margin = 3;
            }

            $subPath = '<div class="treeField" style="margin-bottom: '.$margin."px; border: none;\"><ul>\n";
            $count = 0;
            while ($oNode = $oBreadCrumb->Next()) {
                if ($count > 0) {
                    // if (!empty($subPath)) $subPath .= '';
                    $subPath .= '<li><div class="treesubpath">'.$oNode->sqlData['name'].'</div></li>';
                }
                ++$count;
            }

            if (!stristr($subPath, '<li>')) { // no node active
                $subPath .= '<li>'.TGlobal::Translate('chameleon_system_core.template_engine.no_node_selected').'</li>';
            }

            $subPath .= "</ul></div>\n";

            $path .= $subPath."\n<div class=\"cleardiv\"></div>";
            $aNavigations[] = $path;
        }

        return $aNavigations;
    }

    /**
     * Define the functions that can be called from the website.
     */
    public function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = array('SetLayout', 'GetModuleMainMenu', 'IsMainNavigationSet', 'AddNewRevisionFromDatabase', 'getChooseModuleViewDialog');
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    /**
     * Set the selected layout as the current layout (if a layout had already been set,
     * this function will overwrite it).
     */
    public function SetLayout()
    {
        // load new master pagedef to get a list of spots
        $this->oPage->ChangeMasterPagedef($this->global->GetUserData('sourcepagedef'));

        // and redirect to "edit page"
        $parameter = array('pagedef' => 'templateengine', 'id' => $this->sPageId, '_mode' => $this->sMode);
        $this->controller->HeaderRedirect($parameter);
    }

    /**
     * load the available layouts into $data.
     */
    protected function _GetLayoutList()
    {
        $query = null;
        if (null != $this->oPortal) {
            $query = "SELECT `cms_master_pagedef`.*
                        FROM `cms_master_pagedef`
                   LEFT JOIN `cms_master_pagedef_cms_portal_mlt` ON `cms_master_pagedef_cms_portal_mlt`.`source_id` = `cms_master_pagedef`.`id`
                   LEFT JOIN `cms_portal` ON `cms_portal`.`id` = `cms_master_pagedef_cms_portal_mlt`.`target_id`
                            WHERE `cms_portal`.`id` IS NULL
                              OR `cms_portal`.`id` = '".$this->oPortal->id."'
                              OR `cms_master_pagedef`.`restrict_to_portals` = '0'  ";
        }
        $oMasterDefs = TdbCmsMasterPagedefList::GetList($query);
        $oMasterDefs->ChangeOrderBy(array('position' => 'ASC'));
        $this->data['oMasterDefs'] = $oMasterDefs;
    }

    /**
     * load the list object for existing module instances so the user can choose
     * one from the list and place it into a slot.
     */
    protected function _GetModuleInstanceList()
    {
        // need to pass the parameters (modulespotname) back to the view
        $this->data['spotname'] = $this->global->GetUserData('spotname');
        if (is_null($this->aModuleList)) {
            $this->aModuleList = $this->oPage->GetModuleList();
        }

        $oModuleListTableConf = new TCMSTableConf();
        /** @var $oModuleListTableConf TCMSTableConf */
        $oModuleListTableConf->LoadFromField('name', 'cms_tpl_module_instance');
        $this->data['oModuleListTableConf'] = $oModuleListTableConf;

        $listClass = 'TCMSListManagerModuleInstance';
        // fetch listClass first using the definition in the tableconf...
        if (!empty($oModuleListTableConf->sqlData['cms_tbl_list_class_id'])) {
            $oListDef = new TCMSRecord();
            /** @var $oListDef TCMSRecord */
            $oListDef->table = 'cms_tbl_list_class';
            if ($oListDef->Load($oModuleListTableConf->sqlData['cms_tbl_list_class_id'])) {
                $listClass = $oListDef->sqlData['classname'];
            }
        }

        $oListTable = &$oModuleListTableConf->GetListObject($listClass);
        if (array_key_exists($this->data['spotname'], $this->aModuleList)) {
            if (array_key_exists('permittedModules', $this->aModuleList[$this->data['spotname']])) {
                $oListTable->aPermittedModules = $this->aModuleList[$this->data['spotname']]['permittedModules'];
            }
        }
        $this->data['sTable'] = $oListTable->GetList();
    }

    /**
     * loads the navi into $data and returns the active menuItem.
     *
     * @return string
     */
    protected function GetMainNavigation()
    {
        $this->data['oMenuItems'] = $this->oTableManager->oTableEditor->GetMenuItems();
        $this->data['oMenuItems']->RemoveItem('sItemKey', 'save');
        $this->data['oMenuItems']->RemoveItem('sItemKey', 'copy');
        $this->data['oMenuItems']->RemoveItem('sItemKey', 'new');
        $this->data['oMenuItems']->RemoveItem('sItemKey', 'delete');
        $this->data['oMenuItems']->RemoveItem('sItemKey', 'revisionManagement');
        $this->data['oMenuItems']->RemoveItem('sItemKey', 'revisionManagementLoad');
        $this->data['oMenuItems']->RemoveItem('sItemKey', 'copy_translation');
        $this->data['oMenuItems']->RemoveItem('sItemKey', 'edittableconf');

        $view = 'layout_selection';

        // check if pagedef exists and switch to edit mode
        if (!empty($this->oPage->iMasterPageDefId)) {
            $view = 'edit_content';
        }

        // now activate the current navi item
        if (!$this->bPageDefinitionAssigned) {
            $view = 'layout_selection';
        } else {
            if (!is_null($this->sMode) && !empty($this->sMode)) {
                $view = $this->sMode;
            }
        }

        return $view;
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = array();
        // first the includes that are needed for the all fields

        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/cms.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<link href="'.TGlobal::GetPathTheme().'/css/tableeditcontainer.css" rel="stylesheet" type="text/css" />';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/tableEditor.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery/WayfarerTooltip/WayfarerTooltip.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<link href="'.TGlobal::GetPathTheme().'/css/tooltip.css" rel="stylesheet" type="text/css" />';

        if (!$this->IsRecordLocked() && array_key_exists('locking_active', $this->oTableManager->oTableConf->sqlData) && '1' == $this->oTableManager->oTableConf->sqlData['locking_active'] && !$this->bIsReadOnlyMode && CHAMELEON_ENABLE_RECORD_LOCK) {
            $aIncludes[] = '<script type="text/javascript">
        $(document).ready(function(){
           RefreshRecordEditLock();
        });
        </script>';
        }

        if ('cmp_loadmoduleinstance' == $this->aModuleConfig['view']) {
            $aIncludes[] = "<script type=\"text/javascript\">
      function openModuleViewChooseDialog() {
        CreateModalDialogFromContainer('chooseModuleViewDialog');
      }


      </script>";
        }

        return $aIncludes;
    }

    /**
     * {@inheritdoc}
     */
    public function GetHtmlFooterIncludes()
    {
        $aIncludes = parent::GetHtmlFooterIncludes();
        if ('cmp_loadmoduleinstance' == $this->aModuleConfig['view']) {
            $aChooseModuleViewDialog = $this->getChooseModuleViewDialog();
            if (is_array($aChooseModuleViewDialog) && isset($aChooseModuleViewDialog['html'])) {
                $aIncludes[] = $aChooseModuleViewDialog['html'];
            }
        }

        return $aIncludes;
    }

    /**
     * @return array|bool
     */
    public function getChooseModuleViewDialog()
    {
        $returnVal = false;

        $sDialogContent = '<div id="chooseModuleViewDialog" style="display:none;">
      <h2>'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.template_engine.select_module_view'))."</h2>\n";
        if ($this->global->UserDataExists('instanceid') && '' !== $this->global->GetUserData('instanceid')) {
            $sInstanceId = $this->global->GetUserData('instanceid');
            $sSpotName = $this->global->GetUserData('spotName');

            $oModuleListTableConf = new TCMSTableConf();
            /** @var $oModuleListTableConf TCMSTableConf */
            $oModuleListTableConf->LoadFromField('name', 'cms_tpl_module_instance');
            /** @var $oEditor TCMSTableEditorManager */
            $oEditor = new TCMSTableEditorManager();
            $oEditor->Init($oModuleListTableConf->id, $sInstanceId);

            $returnVal = array();
            $returnVal['bIsTableLocked'] = $oEditor->IsRecordLocked();

            $sSubmitButton = TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.template_engine.select_instance'), "javascript:$('#loadmoduleclass').submit();", URL_CMS.'/images/icons/tick.png');

            $sDialogContent .= '<form name="loadmoduleclass" id="loadmoduleclass" method="post" action="'.URL_WEB_CONTROLLER.'" accept-charset="UTF-8">'."\n".'
    <input type="hidden" name="pagedef" value="'.$this->sPageId.'"/>  '."\n".'
    <input type="hidden" name="id" value="'.$this->sPageId.'"/> '."\n".'
    <input type="hidden" name="instanceid" value="'.$sInstanceId.'"/>  '."\n".'
    <input type="hidden" name="__modulechooser" value="true"/>  '."\n".'
    <input type="hidden" name="spotname" value="'.$sSpotName.'"/> '."\n".'
    <input type="hidden" name="module_fnc['.$sSpotName.']" value="SetInstance"/>'."\n";

            if ($this->global->UserDataExists('bLoadCopy') && '1' == $this->global->GetUserData('bLoadCopy')) {
                $sDialogContent .= '<input type="hidden" name="bLoadCopy" value="1"/>';
            }

            $languageService = $this->getLanguageService();
            $editLanguage = $languageService->getActiveEditLanguage();
            if (null === $editLanguage) {
                $previewLanguageId = $languageService->getCmsBaseLanguageId();
            } else {
                $previewLanguageId = $editLanguage->id;
            }
            $sDialogContent .= '<input type="hidden" name="previewLanguageId" value="'.TGlobal::OutHTML($previewLanguageId).'"/>  '."\n".'';

            /** @var $oCmsTplModuleInstance TdbCmsTplModuleInstance */
            $oCmsTplModuleInstance = TdbCmsTplModuleInstance::GetNewInstance($sInstanceId);

            $lastUsedTemplate = '';
            $oViewList = false;
            if (is_object($oCmsTplModuleInstance)) {
                $oCmsTplModule = $oCmsTplModuleInstance->GetFieldCmsTplModule();
                if (!empty($oCmsTplModuleInstance->fieldTemplate)) {
                    $lastUsedTemplate = $oCmsTplModuleInstance->fieldTemplate;
                }
                /** @var $oCmsTplModule TdbCmsTplModule */
                if ($oCmsTplModule) {
                    /** @var $oViewList TIterator */
                    $oViewList = $oCmsTplModule->GetViews();
                }
            }
            $count = 0;

            $sListContent = '';

            if ($oViewList && $oViewList->Length() > 0) {
                if (1 == $oViewList->Length()) {
                    $returnVal['bOpenDialog'] = false;
                } else {
                    $returnVal['bOpenDialog'] = true;
                }
                $sListContent .= '<div style="padding: 5px;"><select name="template" style="min-width:200px">'."\n";

                $aViews = array();
                while ($sViewName = $oViewList->Next()) {
                    $aViews[$sViewName] = '';
                }
                ksort($aViews);
                $bLastUsedTemplateInArray = array_key_exists($lastUsedTemplate, $aViews);
                if ($bLastUsedTemplateInArray) {
                    $aViews[$lastUsedTemplate] = ' selected';
                }

                foreach ($aViews as $sViewName => $sSelect) {
                    if ($count < 1 && !$bLastUsedTemplateInArray) {
                        $sSelect = ' selected';
                    }
                    ++$count;
                    $sListContent .= '<option'.$sSelect.' value="'.TGlobal::OutHTML($sViewName).'">'.TGlobal::OutHTML($sViewName).'</option>'."\n";
                }
                $sListContent .= '</select></div>'."\n";
            }

            if (0 == $count) {
                $sSubmitButton = '';
            }

            $sDialogContent .= "<div class=\"cleardiv\" style=\"margin-bottom: 10px;\">&nbsp;</div>\n".$sListContent.'<div style="padding-top: 10px;">
          '.$sSubmitButton.'
          </div>
        </form>
      ';
        }
        $sDialogContent .= '</div>';
        $returnVal['html'] = $sDialogContent;

        return $returnVal;
    }

    /**
     * @return InputFilterUtilInterface
     */
    private function getInputFilter()
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }

    private function getLanguageService(): LanguageServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.language_service');
    }
}

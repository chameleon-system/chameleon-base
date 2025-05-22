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
use ChameleonSystem\CoreBundle\Service\BackendBreadcrumbServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * module manages the page-system of the webpage. in it we assume that a page
 * has been selected, and now some operations are performed on that page (assigning a tree node,
 * picking a layout, placing modules into that layout, and editing the modules content).
 * /**/
class CMSTemplateEngine extends TCMSModelBase
{
    /**
     * is true if a page has a layout definition file set (pagedef)
     * if false the template engine redirects to the layout manager.
     */
    protected bool $bPageDefinitionAssigned = false;

    /**
     * cms_tpl_page id.
     */
    protected string $sPageId = '';

    /**
     * the active layout.
     *
     * @var CMSTemplateEngineLayout
     */
    protected $oActiveLayout;

    /**
     * the requested mode (function or component) of the template engine being called.
     */
    protected string $sMode = '';

    /**
     * the page being viewed.
     *
     * @var TCMSPagedef
     */
    protected $oPage;

    /**
     * the portal of the page being viewed.
     */
    protected ?TCMSPortal $oPortal = null;

    /**
     * the table manager for.
     *
     * @var TCMSTableEditor
     */
    protected $oTableManager;

    /**
     * table id of cms_tpl_page.
     */
    protected string $sTableID = '';

    /**
     * array of all modules of all spots of the page.
     *
     * key = spotname
     */
    protected ?array $aModuleList = null;

    /**
     * indicates if the record is rendered in readonly mode.
     */
    protected bool $bIsReadOnlyMode = false;

    /**
     * {@inheritdoc}
     */
    public function Init()
    {
        $cspRules = CMS_AUTO_SEND_BACKEND_HEADER_CONTENT_SECURITY_POLICY;
        if (!empty($cspRules)) {
            $aHeaderNames = ['X-WebKit-CSP', 'X-Content-Security-Policy', 'Content-Security-Policy'];
            foreach ($aHeaderNames as $csp) {
                header($csp.': '.$cspRules);
            }
        }

        parent::Init();

        $this->sPageId = $this->getPageIdFromRequest();
        $this->oPage = TCMSPagedef::GetCachedInstance($this->sPageId);

        if (false === $this->oPage->sqlData) {
            throw new NotFoundHttpException(sprintf('A page with the id %s cannot be found.', $this->sPageId));
        }

        $this->oTableManager = new TCMSTableEditorManager();
        $this->sTableID = TTools::GetCMSTableId('cms_tpl_page');
        $this->oTableManager->Init($this->sTableID, $this->sPageId);
        if ($this->global->UserDataExists('_mode')) {
            $this->sMode = $this->global->GetUserData('_mode');
        }
        $this->bPageDefinitionAssigned = (!empty($this->oPage->iMasterPageDefId));
        $this->AddURLHistory();

        $this->oPortal = $this->oPage->GetPortal();
        $this->data['aNavigationBreadCrumbs'] = $this->GetNavigationBreadCrumbs();

        $this->bIsReadOnlyMode = $this->oTableManager->oTableEditor->IsRecordInReadOnlyMode();
    }

    /**
     * @return string|null
     */
    private function getPageIdFromRequest()
    {
        return $this->getInputFilter()->getFilteredInput('id');
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
        $bMainNavigationIsSet = false;
        $oCmsTplPage = TdbCmsTplPage::GetNewInstance();
        $oCmsTplPage->Load($this->sPageId);
        if (!empty($oCmsTplPage->fieldPrimaryTreeIdHidden)) {
            $bMainNavigationIsSet = true;
        }

        $oRecordData = new TCMSstdClass();
        $oRecordData->bMainNavigationIsSet = $bMainNavigationIsSet;
        $oRecordData->sPageId = $this->sPageId;
        $oRecordData->sToasterErrorMessage = ServiceLocator::get('translator')->trans('chameleon_system_core.template_engine.error_primary_navigation_node_required_before_layout_selection');

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
        if (false === $this->AllowAddingURLToHistory()) {
            return;
        }

        $params = [];
        $params['pagedef'] = $this->global->GetUserData('pagedef');
        $params['id'] = $this->global->GetUserData('id');

        if ($this->global->UserDataExists('sRestriction')) {
            $params['sRestriction'] = $this->global->GetUserData('sRestriction');
        }
        if ($this->global->UserDataExists('sRestrictionField')) {
            $params['sRestrictionField'] = $this->global->GetUserData('sRestrictionField');
        }

        $breadcrumbTitle = ServiceLocator::get('translator')->trans('chameleon_system_core.template_engine.breadcrumb_title_page').': '.(trim($this->oPage->sqlData['name']) ?: ServiceLocator::get('translator')->trans('chameleon_system_core.text.unnamed_record'));

        if ('preview_content' === $this->sMode || 'layout_selection' === $this->sMode || 'layoutlist' === $this->sMode) {
            return;
        }

        $breadcrumb = $this->getBreadcrumbService()->getBreadcrumb();
        $breadcrumb?->AddItem($params, $breadcrumbTitle, implode('::', ['CMSTemplateEngine', 'removeHistoryEntry']));
    }

    public static function removeHistoryEntry(array $historyEntry, string $tableId, string $entryId, string $cmsTblConfId): bool
    {
        return 'cms_tpl_page' === $tableId && 'templateengine' === ($historyEntry['params']['pagedef'] ?? null) && $entryId === ($historyEntry['params']['id'] ?? null);
    }

    public function Execute()
    {
        parent::Execute();

        $oMainNavigationSet = $this->IsMainNavigationSet();
        if (!$oMainNavigationSet->bMainNavigationIsSet) {
            $sURL = PATH_CMS_CONTROLLER.'?'.str_replace('&amp;', '&', TTools::GetArrayAsURL(['pagedef' => 'tableeditor', 'tableid' => $this->sTableID, 'id' => $this->sPageId]));
            $this->getRedirectService()->redirect($sURL);
        }

        $this->IsRecordLocked();
        $this->GetPermissionSettings();

        $this->data['only_one_record_tbl'] = '0';
        $this->data['oTableDefinition'] = $this->oTableManager->oTableConf;
        $this->data['tableid'] = $this->sTableID;

        $this->data['oTable'] = $this->oTableManager->oTableEditor->oTable;
        $sTableTitle = $this->oTableManager->oTableConf->GetName();
        $this->data['sTableTitle'] = $sTableTitle;

        $sRecordName = $this->oTableManager->oTableEditor->oTable->GetName();
        $this->data['sRecordName'] = $sRecordName;
        $this->data['cmsident'] = $this->oTableManager->oTableEditor->oTable->sqlData['cmsident'];

        $this->data['id'] = $this->sPageId;

        $this->data['oPage'] = $this->oPage;
        if ($this->bPageDefinitionAssigned) {
            $this->data['sActivePageDef'] = $this->oPage->iMasterPageDefId;
            /** @var BackendSessionInterface $backendSession */
            $backendSession = ServiceLocator::get('chameleon_system_cms_backend.backend_session');
            $languageId = $backendSession->getCurrentEditLanguageId();
            $this->data['sActualMasterLayout'] = PATH_CMS_CONTROLLER_FRONTEND.'?pagedef='.$this->sPageId.'&__masterPageDef=true&__modulechooser=true&id='.$this->oPage->iMasterPageDefId.'&previewLanguageId='.$languageId;
        } else {
            $this->data['sActivePageDef'] = '';
        }

        $this->switchTemplate();

        return $this->data;
    }

    /**
     * Switches the template according to the current active template submodule mode and loads additional needed content.
     */
    private function switchTemplate(): void
    {
        $viewName = $this->getActiveModuleLayout();

        switch ($this->sMode) {
            case 'layout_selection':
            case 'edit_content':
            case 'preview_content':
            default:
                $this->filterMainNavigation();
                $this->data['sPreviewURL'] = $this->oTableManager->oTableEditor->GetPreviewURL();

                break;
            case 'layoutlist':
                $this->_GetLayoutList();
                $viewName = 'cmp_layoutlist';

                break;
            case 'load_module':
                $inputFilter = $this->getInputFilter();
                $bLoadCopy = $inputFilter->getFilteredGetInput('bLoadCopy');
                $this->data['bLoadCopy'] = $bLoadCopy;

                $this->_GetModuleInstanceList();
                $viewName = 'cmp_loadmoduleinstance';

                break;
        }

        $this->SetTemplate('CMSTemplateEngine', $viewName);
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
            if (1 == $this->oTableManager->oTableConf->sqlData['only_one_record_tbl']) {
                $permissions['new'] = false;
                $permissions['delete'] = false;
            } else {
                $permissions['new'] = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_NEW, $this->oTableManager->oTableConf->sqlData['name']);

                $permissions['delete'] = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_DELETE, $this->oTableManager->oTableConf->sqlData['name']);
            }
        }
        $this->data['aPermission'] = $permissions;
    }

    /**
     * returns the Breadcrumb navigations.
     */
    protected function GetNavigationBreadCrumbs()
    {
        $oPortals = $this->global->GetPortals();
        $stopNodes = $oPortals->GetTreeNodes();
        $oBreadcrumbs = $this->oPage->GetAllNavigationPaths($stopNodes);

        $total = $oBreadcrumbs->Length();

        $aNavigations = [];

        $naviCount = 0;
        while ($oBreadCrumb = $oBreadcrumbs->Next()) {
            $path = '';
            ++$naviCount;
            if ($naviCount === $total) {
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

            if (false === stripos($subPath, '<li>')) { // no node active
                $subPath .= '<li>'.ServiceLocator::get('translator')->trans('chameleon_system_core.template_engine.no_node_selected').'</li>';
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
        $externalFunctions = ['SetLayout', 'GetModuleMainMenu', 'IsMainNavigationSet', 'AddNewRevisionFromDatabase', 'getChooseModuleViewDialog'];
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
        $parameter = ['pagedef' => 'templateengine', 'id' => $this->sPageId, '_mode' => $this->sMode];
        $this->getRedirectService()->redirectToActivePage($parameter);
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
        $oMasterDefs->ChangeOrderBy(['position' => 'ASC']);
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
        /* @var $oModuleListTableConf TCMSTableConf */
        $oModuleListTableConf->LoadFromField('name', 'cms_tpl_module_instance');
        $this->data['oModuleListTableConf'] = $oModuleListTableConf;

        $listClass = 'TCMSListManagerModuleInstance';
        // fetch listClass first using the definition in the tableconf...
        if (!empty($oModuleListTableConf->sqlData['cms_tbl_list_class_id'])) {
            $oListDef = new TCMSRecord();
            /* @var $oListDef TCMSRecord */
            $oListDef->table = 'cms_tbl_list_class';
            if ($oListDef->Load($oModuleListTableConf->sqlData['cms_tbl_list_class_id'])) {
                $listClass = $oListDef->sqlData['classname'];
            }
        }

        $oListTable = $oModuleListTableConf->GetListObject($listClass);
        if (array_key_exists($this->data['spotname'], $this->aModuleList)) {
            if (array_key_exists('permittedModules', $this->aModuleList[$this->data['spotname']])) {
                $oListTable->aPermittedModules = $this->aModuleList[$this->data['spotname']]['permittedModules'];
            }
        }
        $this->data['sTable'] = $oListTable->GetList();
    }

    /**
     * Filters the main navigation items.
     */
    protected function filterMainNavigation(): void
    {
        /**
         * @var $menuItems TIterator
         */
        $menuItems = $this->oTableManager->oTableEditor->GetMenuItems();

        $menuItems->RemoveItem('sItemKey', 'save');
        $menuItems->RemoveItem('sItemKey', 'copy');
        $menuItems->RemoveItem('sItemKey', 'new');
        $menuItems->RemoveItem('sItemKey', 'delete');
        $menuItems->RemoveItem('sItemKey', 'copy_translation');

        $tableEditorButton = $menuItems->FindItemWithProperty('sItemKey', 'edittableconf');
        if (false !== $tableEditorButton) {
            /*
             * @var $tableEditorButton TCMSTableEditorMenuItem
             */
            $tableEditorButton->sOnClick = str_replace('pagedef=templateengine', 'pagedef=tableeditor', $tableEditorButton->sOnClick);
        }

        $this->data['oMenuItems'] = $menuItems;
    }

    /**
     * Switches the module view to layout_selection if the page has no layout yet
     * (otherwise the page is not viewable).
     */
    protected function getActiveModuleLayout(): string
    {
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
        $aIncludes = [];
        // first the includes that are needed for the all fields

        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/cms.v2.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<link href="'.TGlobal::GetPathTheme().'/css/tableeditcontainer.css" rel="stylesheet" type="text/css" />';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/tableEditor.js?v1').'" type="text/javascript"></script>';
        $aIncludes[] = '<link href="'.TGlobal::GetStaticURLToWebLib('/components/select2.v4/css/select2.min.css').'" media="screen" rel="stylesheet" type="text/css" />';

        if (!$this->IsRecordLocked() && array_key_exists('locking_active', $this->oTableManager->oTableConf->sqlData) && '1' == $this->oTableManager->oTableConf->sqlData['locking_active'] && !$this->bIsReadOnlyMode && CHAMELEON_ENABLE_RECORD_LOCK) {
            $aIncludes[] = '<script type="text/javascript">
        $(document).ready(function(){
           RefreshRecordEditLock();
        });
        </script>';
        }

        return $aIncludes;
    }

    /**
     * {@inheritdoc}
     */
    public function GetHtmlFooterIncludes()
    {
        $aIncludes = parent::GetHtmlFooterIncludes();
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/components/select2.v4/js/select2.full.min.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<script>
            window.addEventListener("load", () => {
                coreui.Sidebar.getInstance(document.querySelector("#sidebar")).hide();
            });
        </script>';

        if ('cmp_loadmoduleinstance' === $this->aModuleConfig['view']) {
            $chooseModuleViewDialog = $this->getChooseModuleViewDialog();
            if (null !== $chooseModuleViewDialog && isset($chooseModuleViewDialog['html'])) {
                $aIncludes[] = $chooseModuleViewDialog['html'];
            }
        }

        return $aIncludes;
    }

    public function getChooseModuleViewDialog(): ?array
    {
        $returnVal = null;

        $dialogContent = '<div id="chooseModuleViewDialog" style="display:none;">
      <h2>'.TGlobal::OutHTML(ServiceLocator::get('translator')->trans('chameleon_system_core.template_engine.select_module_view'))."</h2>\n";
        if ($this->global->UserDataExists('instanceid') && '' !== $this->global->GetUserData('instanceid')) {
            $instanceId = $this->global->GetUserData('instanceid');
            $spotName = $this->global->GetUserData('spotName');

            $moduleListTableConf = new TCMSTableConf();
            $moduleListTableConf->LoadFromField('name', 'cms_tpl_module_instance');
            $editor = new TCMSTableEditorManager();
            $editor->Init($moduleListTableConf->id, $instanceId);

            $returnVal = [];
            $returnVal['bOpenDialog'] = false;
            $returnVal['bIsTableLocked'] = $editor->IsRecordLocked();

            $submitButton = TCMSRender::DrawButton(ServiceLocator::get('translator')->trans('chameleon_system_core.template_engine.select_instance'), "javascript:$('#loadmoduleclass').submit();", 'fas fa-check');

            $dialogContent .= '<form name="loadmoduleclass" id="loadmoduleclass" method="post"
action="/cms/frontend?__modulechooser=true&esdisablelinks=true&esdisablefrontendjs=true&__previewmode=true" accept-charset="UTF-8">'."\n".'
    <input type="hidden" name="pagedef" value="'.$this->sPageId.'"/>  '."\n".'
    <input type="hidden" name="id" value="'.$this->sPageId.'"/> '."\n".'
    <input type="hidden" name="instanceid" value="'.$instanceId.'"/>  '."\n".'
    <input type="hidden" name="__modulechooser" value="true"/>  '."\n".'
    <input type="hidden" name="spotname" value="'.$spotName.'"/> '."\n".'
    <input type="hidden" name="module_fnc['.$spotName.']" value="SetInstance"/>'."\n";

            if ($this->global->UserDataExists('bLoadCopy') && '1' === $this->global->GetUserData('bLoadCopy')) {
                $dialogContent .= '<input type="hidden" name="bLoadCopy" value="1"/>';
            }

            $previewLanguageId = $this->getBackendSession()->getCurrentEditLanguageId();
            $dialogContent .= '<input type="hidden" name="previewLanguageId" value="'.TGlobal::OutHTML($previewLanguageId).'"/>  '."\n".'';

            $cmsTplModuleInstance = TdbCmsTplModuleInstance::GetNewInstance($instanceId);

            $lastUsedTemplate = '';
            $viewList = null;
            if (is_object($cmsTplModuleInstance)) {
                $cmsTplModule = $cmsTplModuleInstance->GetFieldCmsTplModule();
                if (!empty($cmsTplModuleInstance->fieldTemplate)) {
                    $lastUsedTemplate = $cmsTplModuleInstance->fieldTemplate;
                }
                if (null !== $cmsTplModule) {
                    /** @var $viewList TIterator */
                    $viewList = $cmsTplModule->GetViews();
                }
            }
            $count = 0;

            $listContent = '';

            if (null !== $viewList && $viewList->Length() > 0) {
                if ($viewList->Length() > 1) {
                    $returnVal['bOpenDialog'] = true;
                }
                $listContent .= '<div style="padding: 5px;"><select name="template" style="min-width:200px">'."\n";

                $views = [];
                while ($sViewName = $viewList->Next()) {
                    $views[$sViewName] = '';
                }
                ksort($views);
                $lastUsedTemplateInArray = array_key_exists($lastUsedTemplate, $views);
                if (false !== $lastUsedTemplateInArray) {
                    $views[$lastUsedTemplate] = ' selected';
                }

                foreach ($views as $sViewName => $selected) {
                    if ($count < 1 && !$lastUsedTemplateInArray) {
                        $selected = ' selected';
                    }
                    ++$count;
                    $listContent .= '<option'.$selected.' value="'.TGlobal::OutHTML($sViewName).'">'.TGlobal::OutHTML($sViewName).'</option>'."\n";
                }
                $listContent .= '</select></div>'."\n";
            } else {
                $dialogContent .= '<input type="hidden" name="template" value="'.$lastUsedTemplate.'"/>';
            }

            if (0 === $count) {
                $submitButton = '';
            }

            $dialogContent .= "<div class=\"cleardiv\" style=\"margin-bottom: 10px;\">&nbsp;</div>\n".$listContent.'<div style="padding-top: 10px;">
          '.$submitButton.'
          </div>
        </form>
      ';
        }
        $dialogContent .= '</div>';
        $returnVal['html'] = $dialogContent;

        return $returnVal;
    }

    private function getInputFilter(): InputFilterUtilInterface
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }

    private function getBackendSession(): BackendSessionInterface
    {
        return ServiceLocator::get('chameleon_system_cms_backend.backend_session');
    }

    private function getBreadcrumbService(): BackendBreadcrumbServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.service.backend_breadcrumb');
    }

    private function getRedirectService(): ICmsCoreRedirect
    {
        return ServiceLocator::get('chameleon_system_core.redirect');
    }
}

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
use ChameleonSystem\CoreBundle\ServiceLocator;

class TCMSTableEditorPage extends TCMSTableEditor
{
    /**
     * this is the root node of all page trees.
     */
    public const ROOT_NODE_ID = 99;

    protected function DeleteRecordReferences()
    {
        $iModuleInstanceTableID = TTools::GetCMSTableId('cms_tpl_module_instance');
        /** @var $oTableEditor TCMSTableEditorManager */
        $oTableEditor = new TCMSTableEditorManager();

        $sQuery = "SELECT * FROM `cms_tpl_page_cms_master_pagedef_spot` WHERE `cms_tpl_page_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."'";
        $oInstanceConnections = TdbCmsTplPageCmsMasterPagedefSpotList::GetList($sQuery);
        while ($oInstanceConnection = $oInstanceConnections->Next()) {
            $oTableEditor->Init($iModuleInstanceTableID, $oInstanceConnection->fieldCmsTplModuleInstanceId);
            $oTableEditor->AllowDeleteByAll(true);
            $oTableEditor->Delete($oInstanceConnection->fieldCmsTplModuleInstanceId);

            $oTableEditor->Init($iModuleInstanceTableID, $oInstanceConnection->id);
            $oTableEditor->AllowDeleteByAll(true);
            $oTableEditor->Delete($oInstanceConnection->id);
        }

        $this->DeleteRecordReferencesTree();
        parent::DeleteRecordReferences();
    }

    protected function DeleteRecordReferencesTree()
    {
        $query = "SELECT * FROM `cms_tree_node`
                 WHERE `tbl` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->oTableConf->sqlData['name'])."'
                   AND `contid` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."'";
        $iTableID = TTools::GetCMSTableId('cms_tree_node');
        $oTableEditor = new TCMSTableEditorManager();
        /** @var $oTableEditor TCMSTableEditorManager */
        $oCmsTreeNodeList = TdbCmsTreeNodeList::GetList($query);
        /** @var $oCmsTreeNodeList TdbCmsTreeNodeList */
        while ($oCmsTreeNode = $oCmsTreeNodeList->Next()) {
            /* @var $oCmsTreeNode TdbCmsTreeNode */
            $oTableEditor->Init($iTableID, $oCmsTreeNode->id);
            $oTableEditor->Delete($oCmsTreeNode->id);
        }
        $this->getCacheService()->callTrigger($this->oTableConf->sqlData['name'], $this->sId);
    }

    /**
     * gets called after save if all posted data was valid.
     *
     * @param TIterator $oFields holds an iterator of all field classes from DB table with the posted values or default if no post data is present
     * @param TCMSRecord $oPostTable holds the record object of all posted data
     */
    protected function PostSaveHook($oFields, $oPostTable)
    {
        $this->UpdatePageNaviBreadCrumb();
        parent::PostSaveHook($oFields, $oPostTable);
    }

    /**
     * update the page navi breadcrumb.
     */
    public function UpdatePageNaviBreadCrumb()
    {
        $breadcrumbs = self::GetNavigationBreadCrumbs($this->sId);
        $this->SaveField('tree_path_search_string', $breadcrumbs);
    }

    /**
     * makes it possible to modify the contents written to database after the copy
     * is commited.
     */
    protected function OnAfterCopy()
    {
        // Dont add tree nodes from source page on copy tree or copy tree connections because these connections already exists
        if (false === TCMSTableEditorTree::IsCopyTreeMode() && false === TCMSTableEditorTreeConnection::IsCopyTreeConnectionMode()) {
            $query = "SELECT * FROM `cms_tree_node` WHERE `contid` = '".$this->sSourceId."' AND `tbl` = 'cms_tpl_page'";
            $oCmsTreeNodeList = TdbCmsTreeNodeList::GetList($query);
            /** @var $oCmsTreeNodeList TdbCmsTreeNodeList */
            $oTableEditor = new TCMSTableEditorManager();
            /** @var $oTableEditor TCMSTableEditorManager */
            $iTableID = TTools::GetCMSTableId('cms_tree_node');
            while ($oCmsTreeNode = $oCmsTreeNodeList->Next()) {
                /* @var $oCmsTreeNode TdbCmsTreeNode */
                $oTableEditor->Init($iTableID, $oCmsTreeNode->id);
                $oTableEditor->oTableEditor->bPreventPageCopy = true;
                $oTableEditor->DatabaseCopy(false, [], $this->bIsCopyAllLanguageValues);
                $oTableEditor->SaveField('contid', $this->sId);
            }
        }
        parent::OnAfterCopy();
        $breadcrumbs = self::GetNavigationBreadCrumbs($this->sId);
        $this->SaveField('tree_path_search_string', $breadcrumbs);
    }

    /**
     * returns the Breadcrumb navigations as plaintext.
     *
     * @param int $id - id of the page to update
     *
     * @return string
     */
    public static function GetNavigationBreadCrumbs($id)
    {
        $oPage = TdbCmsTplPage::GetNewInstance();
        $oPage->Load($id);
        $oNodes = $oPage->GetTreeNodesObjects(false);
        $path = '';

        /** @var $oNode TdbCmsTree */
        while ($oNode = $oNodes->Next()) {
            // replace old <li> separators by /
            $sPathCache = str_replace(' </li><li> ', '/', $oNode->fieldPathcache);
            $sPathCache = str_replace('</li><li>', '/', $sPathCache);
            $path .= $sPathCache."\n";
        }

        return $path;
    }

    /**
     * add table-specific buttons to the editor (add them directly to $this->oMenuItems).
     */
    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();

        // add custom message to delete
        $oMenuItemSave = $this->oMenuItems->FindItemWithProperty('sItemKey', 'delete');
        $deleteMessage = addslashes(ServiceLocator::get('translator')->trans('chameleon_system_core.template_engine.confirm_page_delete'));
        $oMenuItemSave->sOnClick = "CHAMELEON.CORE.MTTableEditor.DeleteRecordWithCustomConfirmMessage('$deleteMessage');";

        $oMenuItem = new TCMSTableEditorMenuItem();
        $oMenuItem->sDisplayName = ServiceLocator::get('translator')->trans('chameleon_system_core.list.page_settings');
        $oMenuItem->sItemKey = 'pagesettings';
        $oMenuItem->sIcon = 'far fa-edit';
        $oMenuItem->href = PATH_CMS_CONTROLLER.'?pagedef=tableeditor&tableid=70&id='.TGlobal::OutHTML($this->oTable->id);
        $this->oMenuItems->AddItem($oMenuItem);

        $bPageDefExists = (!empty($this->oTable->sqlData['cms_master_pagedef_id']));

        if ($bPageDefExists) {
            $oMenuItem = new TCMSTableEditorMenuItem();
            $oMenuItem->sDisplayName = ServiceLocator::get('translator')->trans('chameleon_system_core.template_engine.action_edit_template');
            $oMenuItem->sItemKey = 'templateengine';
            $oMenuItem->sIcon = 'fa fa-pen-square';
            $oMenuItem->href = PATH_CMS_CONTROLLER.'?pagedef=templateengine&_mode=edit_content&id='.TGlobal::OutHTML($this->oTable->id);
            $oMenuItem->setButtonStyle('btn btn-sm w-100 btn-primary');
            $this->oMenuItems->AddItem($oMenuItem);

            $oMenuItem = new TCMSTableEditorMenuItem();
            $oMenuItem->sDisplayName = ServiceLocator::get('translator')->trans('chameleon_system_core.template_engine.action_page_preview');
            $oMenuItem->sItemKey = 'pagepreview';
            $oMenuItem->sIcon = 'far fa-eye';
            $oMenuItem->href = PATH_CMS_CONTROLLER.'?pagedef=templateengine&_mode=preview_content&id='.TGlobal::OutHTML($this->oTable->id);
            $this->oMenuItems->AddItem($oMenuItem);
        }

        $oMenuItem = new TCMSTableEditorMenuItem();
        $oMenuItem->sDisplayName = ServiceLocator::get('translator')->trans('chameleon_system_core.template_engine.action_page_templates');
        $oMenuItem->sItemKey = 'pagelayouts';
        $oMenuItem->sIcon = 'fas fa-code';
        $oMenuItem->sOnClick = "openLayoutManager('".TGlobal::OutJS($this->oTable->id)."');";
        $this->oMenuItems->AddItem($oMenuItem);
    }

    /**
     * adds table-specific buttons to the editor (add them directly to $this->oMenuItems)
     * will only be loadd in read only mode instead of GetCustomMenuItems();.
     */
    protected function GetCustomReadOnlyMenuItem()
    {
        parent::GetCustomReadOnlyMenuItem();

        $oMenuItem = new TCMSTableEditorMenuItem();
        $oMenuItem->sDisplayName = ServiceLocator::get('translator')->trans('chameleon_system_core.list.page_settings');
        $oMenuItem->sItemKey = 'pagesettings';
        $oMenuItem->sIcon = 'fa fa-pen-square';
        $oMenuItem->href = PATH_CMS_CONTROLLER.'?pagedef=tableeditor&tableid=70&id='.TGlobal::OutHTML($this->oTable->id);
        $oMenuItem->setButtonStyle('btn btn-sm w-100 btn-primary');
        $this->oMenuItems->AddItem($oMenuItem);

        $bPageDefExists = (!empty($this->oTable->sqlData['cms_master_pagedef_id']));

        if ($bPageDefExists) {
            $oMenuItem = new TCMSTableEditorMenuItem();
            $oMenuItem->sDisplayName = ServiceLocator::get('translator')->trans('chameleon_system_core.template_engine.action_edit_template');
            $oMenuItem->sItemKey = 'templateengine';
            $oMenuItem->sIcon = 'far fa-edit';
            $oMenuItem->href = PATH_CMS_CONTROLLER.'?pagedef=templateengine&_mode=edit_content&id='.TGlobal::OutHTML($this->oTable->id);
            $this->oMenuItems->AddItem($oMenuItem);

            $oMenuItem = new TCMSTableEditorMenuItem();
            $oMenuItem->sDisplayName = ServiceLocator::get('translator')->trans('chameleon_system_core.template_engine.action_page_preview');
            $oMenuItem->sItemKey = 'pagepreview';
            $oMenuItem->sIcon = 'far fa-eye';
            $oMenuItem->href = PATH_CMS_CONTROLLER.'?pagedef=templateengine&_mode=preview_content&id='.TGlobal::OutHTML($this->oTable->id);
            $this->oMenuItems->AddItem($oMenuItem);
        }
    }

    /**
     * save a searchable list of breadcrumb navigations to one page or all pages.
     *
     * @param string $sPageID
     */
    public static function UpdateCmsListNaviCache($sPageID = null)
    {
        $query = 'SELECT * FROM `cms_tpl_page`';
        if (!is_null($sPageID) && !empty($sPageID)) {
            $query .= " WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sPageID)."'";
        }

        $oCmsTplPageList = TdbCmsTplPageList::GetList($query);
        $databaseConnection = self::getDbConnection();
        $fieldTranslationUtil = self::getFieldTranslationUtil();
        $fieldToUpdate = $databaseConnection->quoteIdentifier($fieldTranslationUtil->getTranslatedFieldName('cms_tpl_page', 'tree_path_search_string'));
        $statement = $databaseConnection->prepare("UPDATE `cms_tpl_page` SET $fieldToUpdate = :treePathSearchString WHERE `id` = :id");
        /** @var $oCmsTplPageList TdbCmsTplPageList */
        while ($oCmsTplPage = $oCmsTplPageList->Next()) {
            /** @var $oCmsTplPage TdbCmsTplPage */
            $fullHTMLTrees = self::GetNavigationBreadCrumbs($oCmsTplPage->id);

            // we don`t want to use a table editor here, to prevent a workflow transaction with ALL pages
            $statement->executeQuery([
                'treePathSearchString' => $fullHTMLTrees,
                'id' => $oCmsTplPage->id,
            ]);
        }
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
        $portal = $this->oTable->GetPortal();

        if (null === $portal) {
            return '';
        }

        $domain = $portal->GetPrimaryDomain();
        /** @var BackendSessionInterface $backendSession */
        $backendSession = ServiceLocator::get('chameleon_system_cms_backend.backend_session');

        return 'https://'.$domain.PATH_CUSTOMER_FRAMEWORK_CONTROLLER.'?pagedef='.urlencode($this->sId).'&esdisablelinks=true&__previewmode=true&previewLanguageId='.urlencode($backendSession->getCurrentEditLanguageId());
    }

    /**
     * @return ChameleonSystem\CoreBundle\Util\FieldTranslationUtil
     */
    private static function getFieldTranslationUtil()
    {
        return ServiceLocator::get('chameleon_system_core.util.field_translation');
    }

    /**
     * @return Doctrine\DBAL\Connection
     */
    private static function getDbConnection()
    {
        return ServiceLocator::get('database_connection');
    }
}

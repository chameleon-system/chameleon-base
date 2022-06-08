<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\CoreEvents;
use ChameleonSystem\CoreBundle\Event\ChangeActiveLanguagesForPortalEvent;
use ChameleonSystem\CoreBundle\Event\ChangeUseSlashInSeoUrlsEvent;
use ChameleonSystem\CoreBundle\Event\LocaleChangedEvent;
use ChameleonSystem\CoreBundle\Service\PageServiceInterface;
use ChameleonSystem\DatabaseMigration\DataModel\LogChangeDataModel;
use ChameleonSystem\DatabaseMigration\Query\MigrationQueryData;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TCMSTableEditorPortal extends TCMSTableEditor
{
    /**
     * the portal being copied (only set on a copy call).
     *
     * @var TCMSPortal
     */
    protected $oOriginalPortal = null;

    /**
     * {@inheritdoc}
     */
    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();

        /** @var $oMenuItem TCMSTableEditorMenuItem */
        $oMenuItem = new TCMSTableEditorMenuItem();
        $oMenuItem->sItemKey = 'editpagetree';
        $oMenuItem->sDisplayName = TGlobal::Translate('chameleon_system_core.table_editor_portal.action_edit_tree');
        $oMenuItem->sIcon = 'fas fa-sitemap';
        $oMenuItem->sOnClick = "javascript:var navId = '".TGlobal::OutHTML($this->oTable->sqlData['main_node_tree'])."';if (document.cmseditform.main_node_tree) navId = document.cmseditform.main_node_tree.value; if (navId>0 || (navId != '' && navId != '0')) CreateModalIFrameDialogCloseButton('".PATH_CMS_CONTROLLER."?pagedef=navigationTreePlain&table=cms_tpl_page&noassign=1&rootID='+navId+'&isInIframe=1', 0,0,'".TGlobal::Translate('chameleon_system_core.cms_module_page_tree.headline')."'); else alert('".TGlobal::Translate('chameleon_system_core.table_editor_portal.error_navigation_node_required')."');";
        $this->oMenuItems->AddItem($oMenuItem);

        // Add language activator
        if (true === $this->allowShowActivateLanguageButton()) {
            $sFunction = 'ActivateLanguage';
            $sText = TGlobal::Translate('chameleon_system_core.table_editor_portal.action_tmp_enable_inactive_language_for_me');
            $sPostFunction = 'OpenPageWithActiveLanguages';
            if (true === $this->oTable->GetActivateAllPortalLanguages()) {
                $sFunction = 'DeActivateLanguage';
                $sText = TGlobal::Translate('chameleon_system_core.table_editor_portal.action_disable_tmp_enabled_languages');
                $sPostFunction = 'ReloadActivePage';
            }
            $oMenuItem = new TCMSTableEditorMenuItem();
            $oMenuItem->sItemKey = 'updateTranslationFields';
            $oMenuItem->sDisplayName = $sText;
            $oMenuItem->sIcon = 'fas fa-globe-americas';

            $sCallURL = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript(
                array(
                     'pagedef' => 'tableeditor',
                     '_fnc' => $sFunction,
                     'id' => $this->sId,
                     'tableid' => $this->oTableConf->id,
                     'module_fnc' => array('contentmodule' => 'ExecuteAjaxCall'),
                     '_noModuleFunction' => 'true',
                )
            );
            $oMenuItem->sOnClick = " GetSynchronouslyAjaxCall('".$sCallURL."',".$sPostFunction.');';
            $this->oMenuItems->AddItem($oMenuItem);
        }
    }

    /**
     * Checks if its allowed to show activet inactive languages button.
     *
     * @return bool
     */
    protected function allowShowActivateLanguageButton()
    {
        $bAllow = $this->oTable->hasInactiveLanguages();

        return $bAllow;
    }

    /**
     * copy all portal properties.
     *
     * for copy of "property_navigations"  check $this->CopyNaviTree()
     *
     * @param TCMSField $oField
     * @param string    $sourceRecordID
     */
    public function CopyPropertyRecords($oField, $sourceRecordID)
    {
        $aExclude = $this->GetPropertyRecordExcludes();

        if (!in_array(trim($oField->name), $aExclude)) {
            parent::CopyPropertyRecords($oField, $sourceRecordID);
        }
    }

    /**
     * return array with field names that should be excluded in CopyPropertyRecords() method.
     *
     * @return array
     */
    protected function GetPropertyRecordExcludes()
    {
        return array('property_navigations', 'cms_portal_divisions');
    }

    /**
     * makes it possible to modify the contents fetched from database before the copy
     * is commited.
     */
    protected function OnBeforeCopy()
    {
        parent::OnBeforeCopy();

        /** @var $oSourcePortal TCMSPortal */
        $this->oOriginalPortal = new TCMSPortal();
        $this->oOriginalPortal->Load($this->sSourceId);

        $this->oTable->sqlData['is_default'] = '0';
        $this->oTable->sqlData['home_node_id'] = '';
        $this->oTable->sqlData['name'] = $this->oTable->sqlData['name'].' Copy';

        $sNewMainNodeId = $this->CreateNewMainNode();
        if (!is_null($sNewMainNodeId)) {
            $this->oTable->sqlData['main_node_tree'] = $sNewMainNodeId;
        }
    }

    /**
     * return null if you won't change the main node of the new portal.
     *
     * @return string | null
     */
    protected function CreateNewMainNode()
    {
        /** @var $oTreeTableConf TCMSTableConf */
        $oTreeTableConf = new TCMSTableConf();
        $oTreeTableConf->LoadFromField('name', 'cms_tree');

        /** @var $oTreeNodeEditor TCMSTableEditorTree */
        $oTreeNodeEditor = new TCMSTableEditorManager();
        $oTreeNodeEditor->Init($oTreeTableConf->id);
        $aTreeNodeData = [
            'parent_id' => TCMSTreeNode::TREE_ROOT_ID,
            'name' => $this->GetNewRootNodeName(),
        ];
        $oTreeNodeEditor->ForceHiddenFieldWriteOnSave(true);
        $oTreeNodeEditor->Save($aTreeNodeData);

        return $oTreeNodeEditor->sId;
    }

    protected function GetNewRootNodeName()
    {
        return $this->oTable->sqlData['name'];
    }

    /**
     * called after inserting a new record.
     *
     * @param TIterator $oFields - the fields inserted
     */
    protected function PostInsertHook(&$oFields)
    {
        parent::PostInsertHook($oFields);

        $oUser = &TCMSUser::GetActiveUser();
        $this->linkPortalToUser($oUser);

        TCacheManager::PerformeTableChange('cms_user', $oUser->id);
    }

    /**
     * copy the tree, the navigations, the divisons, the pages, the modules, etc...
     */
    protected function OnAfterCopy()
    {
        $this->SetSessionCopiedPortalId();
        parent::OnAfterCopy();

        $oUser = &TCMSUser::GetActiveUser();
        $this->linkPortalToUser($oUser);

        $this->CopyNaviTree();
        $this->UnsetSessionCopiedPortalId();
        TCacheManager::PerformeTableChange('cms_user', $oUser->id);
    }

    /**
     * @param TCMSUser $oUser
     *
     * @throws ErrorException
     * @throws TPkgCmsException_Log
     */
    protected function linkPortalToUser(TCMSUser $oUser)
    {
        $query = "INSERT INTO `cms_user_cms_portal_mlt`
                        SET `source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oUser->id)."',
                            `target_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."'";
        MySqlLegacySupport::getInstance()->query($query);

        $editLanguage = $this->getLanguageService()->getActiveEditLanguage();
        $migrationQueryData = new MigrationQueryData('cms_user_cms_portal_mlt', $editLanguage->fieldIso6391);
        $migrationQueryData
            ->setFields(array(
                'source_id' => $oUser->id,
                'target_id' => $this->sId,
            ))
        ;
        $aQuery = array(new LogChangeDataModel($migrationQueryData, LogChangeDataModel::TYPE_INSERT));

        TCMSLogChange::WriteTransaction($aQuery);
    }

    /**
     * Save copied portal id in Session.
     * Was needed to connect a copied page with copied portal.
     */
    protected function SetSessionCopiedPortalId()
    {
        $_SESSION['sCopiedPortalId'] = $this->sId;
    }

    /**
     * Get copied portal id. If no portal was copied return false.
     * Was needed to connect a copied page with copied portal.
     *
     * @return string
     */
    public static function GetCopiedPortalId()
    {
        $sCopiedPortalId = false;
        if (array_key_exists('sCopiedPortalId', $_SESSION)) {
            $sCopiedPortalId = $_SESSION['sCopiedPortalId'];
        }

        return $sCopiedPortalId;
    }

    /**
     *  Unset copied portal id in session after portal copy was completed.
     */
    protected function UnsetSessionCopiedPortalId()
    {
        if ((array_key_exists('sCopiedPortalId', $_SESSION))) {
            unset($_SESSION['sCopiedPortalId']);
        }
    }

    /**
     * Copy portal navigation includes all trees tree_nodes and pages.
     */
    protected function CopyNaviTree()
    {
        $aPortalSystemPages = $this->GetPortalSystemPagesAsArray();
        /** @var $oPortalNaviTableConf TCMSTableConf */
        $oPortalNaviTableConf = new TCMSTableConf();
        $oPortalNaviTableConf->LoadFromField('name', 'cms_portal_navigation');

        //$this->oTable->sqlData['main_node_tree']
        // now we need to copy the navigations and division along with the corresponding subtrees
        // NOTE: we ONLY copy items that are connected directly below the primary source node!

        /** @var $oSourceNavigations TCMSRecordList */
        $oSourceNavigations = new TCMSRecordList();
        $oSourceNavigations->sTableName = 'cms_portal_navigation';
        $query = "SELECT `cms_portal_navigation`.*
                  FROM `cms_portal_navigation`
            INNER JOIN `cms_tree` ON `cms_portal_navigation`.`tree_node` = `cms_tree`.`id`
                 WHERE `cms_portal_navigation`.`cms_portal_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sSourceId)."'
                   AND `cms_tree`.`parent_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->oOriginalPortal->sqlData['main_node_tree'])."'
              ORDER BY `cms_tree`.`entry_sort`
               ";
        $oSourceNavigations->Load($query);

        while ($oSourceNavi = &$oSourceNavigations->Next()) {
            // copy root node and subtrees
            // and create navi using new tree node
            // create subnavi
            /** @var $oNaviTreeNode TCMSTreeNode */
            $oNaviTreeNode = new TCMSTreeNode();
            $oNaviTreeNode->Load($oSourceNavi->sqlData['tree_node']);

            $iNewNaviNode = $this->CopyOneNode($oNaviTreeNode->sqlData, $this->oTable->sqlData['main_node_tree'], $aPortalSystemPages);

            /** @var $oSubNaviEditor TCMSTableEditorPortalNavigation */
            $oSubNaviEditor = new TCMSTableEditorManager();
            $oSubNaviEditor->Init($oPortalNaviTableConf->id);
            $aSourceNaviData = array('name' => $oSourceNavi->sqlData['name'], 'tree_node' => $iNewNaviNode, 'cms_portal_id' => $this->sId);
            $oSubNaviEditor->ForceHiddenFieldWriteOnSave(true);
            $oSubNaviEditor->Save($aSourceNaviData);

            $this->CopySubtree($oSourceNavi->sqlData['tree_node'], $iNewNaviNode, $aPortalSystemPages);
        }
    }

    /**
     * Get all portal system pages as array.
     * Was needed on portal copy to set the tree connection of the system pages to the copied trees.
     *
     * @return array
     */
    protected function GetPortalSystemPagesAsArray()
    {
        $aPortalSystemPages = array();
        $oSystemPageList = $this->oTable->GetFieldCmsPortalSystemPageList();
        while ($oSystemPage = $oSystemPageList->Next()) {
            if (array_key_exists($oSystemPage->fieldCmsTreeId, $aPortalSystemPages)) {
                if (!is_array($aPortalSystemPages[$oSystemPage->fieldCmsTreeId])) {
                    $oPortalSystemPage = $aPortalSystemPages[$oSystemPage->fieldCmsTreeId];
                    $aPortalSystemPages[$oSystemPage->fieldCmsTreeId] = array();
                    $aPortalSystemPages[$oSystemPage->fieldCmsTreeId][] = $oPortalSystemPage;
                }
                $aPortalSystemPages[$oSystemPage->fieldCmsTreeId][] = $oSystemPage;
            } else {
                $aPortalSystemPages[$oSystemPage->fieldCmsTreeId] = $oSystemPage;
            }
        }

        return $aPortalSystemPages;
    }

    /**
     * Copy complete navigation sub tree .
     *
     * @param string $iSourceId
     * @param string $iTargetId
     * @param array  $aPortalSystemPages
     */
    protected function CopySubtree($iSourceId, $iTargetId, $aPortalSystemPages)
    {
        // get all source children...
        $query = "SELECT * FROM `cms_tree` WHERE `parent_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($iSourceId)."' ORDER BY `entry_sort`";
        $sourceRes = MySqlLegacySupport::getInstance()->query($query);

        while ($aSourceChildNode = MySqlLegacySupport::getInstance()->fetch_assoc($sourceRes)) {
            $iNewNodeId = $this->CopyOneNode($aSourceChildNode, $iTargetId, $aPortalSystemPages);
            // now work the children
            $this->CopySubtree($aSourceChildNode['id'], $iNewNodeId, $aPortalSystemPages);
        }
    }

    /**
     * Copy a tree (includes tree connections and pages).
     * Return copied tree id.
     *
     * @param string $aSourceNode
     * @param string $targetParentId
     * @param array  $aPortalSystemPages
     *
     * @return string
     */
    protected function CopyOneNode($aSourceNode, $targetParentId, $aPortalSystemPages)
    {
        $oTreeManager = TTools::GetTableEditorManager('cms_tree', $aSourceNode['id']);
        $oNewTree = $oTreeManager->DatabaseCopy(false, array('parent_id' => $targetParentId), $this->bIsCopyAllLanguageValues);
        $this->SetPortalSystemPageNodeToCopiedNode($aPortalSystemPages, $aSourceNode['id'], $oNewTree->id);
        //Move Tree-Paths...
        if (isset($this->oOriginalPortal->sqlData['home_node_id'])) {
            if ($aSourceNode['id'] == $this->oOriginalPortal->sqlData['home_node_id']) {
                $this->UpdateNodeIdOfPage($oTreeManager->sId, 'home_node_id');
            }
        }
        if (isset($this->oOriginalPortal->sqlData['page_not_found_node'])) {
            if ($aSourceNode['id'] == $this->oOriginalPortal->sqlData['page_not_found_node']) {
                $this->UpdateNodeIdOfPage($oTreeManager->sId, 'page_not_found_node');
            }
        }
        if (isset($this->oOriginalPortal->sqlData['article_page_node'])) {
            if ($aSourceNode['id'] == $this->oOriginalPortal->sqlData['article_page_node']) {
                $this->UpdateNodeIdOfPage($oTreeManager->sId, 'article_page_node');
            }
        }
        if (isset($this->oOriginalPortal->sqlData['article_search_page_node'])) {
            if ($aSourceNode['id'] == $this->oOriginalPortal->sqlData['article_search_page_node']) {
                $this->UpdateNodeIdOfPage($oTreeManager->sId, 'article_search_page_node');
            }
        }

        // copy division if one was attached to the source...
        $this->CopyNodeDivision($aSourceNode, $oTreeManager->sId);

        return $oTreeManager->sId;
    }

    /**
     * Set copied tree id to given portal system pages.
     *
     * @param array  $aPortalSystemPages
     * @param string $sSourceNodeId
     * @param string $sCopiedNodeId
     */
    protected function SetPortalSystemPageNodeToCopiedNode($aPortalSystemPages, $sSourceNodeId, $sCopiedNodeId)
    {
        if (array_key_exists($sSourceNodeId, $aPortalSystemPages)) {
            if (is_array($aPortalSystemPages[$sSourceNodeId])) {
                foreach ($aPortalSystemPages[$sSourceNodeId] as $oPortalSystemPage) {
                    $this->SaveCopiedTreeAtPortalSystemPageNode($oPortalSystemPage, $sCopiedNodeId);
                }
            } else {
                $this->SaveCopiedTreeAtPortalSystemPageNode($aPortalSystemPages[$sSourceNodeId], $sCopiedNodeId);
            }
        }
    }

    /**
     * Set given tree id to given portal system page.
     *
     * @param TdbCmsPortalSystemPage $oPortalSystemPage
     * @param string                 $sCopiedNodeId
     */
    protected function SaveCopiedTreeAtPortalSystemPageNode($oPortalSystemPage, $sCopiedNodeId)
    {
        $oPortalSystemPageManager = TTools::GetTableEditorManager($oPortalSystemPage->table, $oPortalSystemPage->id);
        $oPortalSystemPageManager->AllowEditByAll(true);
        $oPortalSystemPage->sqlData['cms_tree_id'] = $sCopiedNodeId;
        $oPortalSystemPageManager->Save($oPortalSystemPage->sqlData);
        $oPortalSystemPageManager->AllowEditByAll(false);
    }

    /**
     * saves the home node id via SaveField.
     *
     * @param string $sHomeNodeId
     */
    protected function UpdateNodeIdOfPage($iNotFoundNodeId, $sNodeName = '')
    {
        if (!empty($sNodeName)) {
            $this->SaveField($sNodeName, $iNotFoundNodeId);
        }
    }

    /**
     * copies the division of a source tree node to a target tree node.
     *
     * @param array  $aSourceNode   - sqlData of the source node
     * @param string $sTargetNodeId - id of the target tree node
     */
    protected function CopyNodeDivision($aSourceNode, $sTargetNodeId)
    {
        /** @var $oCmsDivision TdbCmsDivision */
        $oCmsDivision = TdbCmsDivision::GetNewInstance();
        if ($oCmsDivision->LoadFromField('cms_tree_id_tree', $aSourceNode['id'])) {
            $oDivisionTableConf = &$oCmsDivision->GetTableConf();

            /** @var $oTableManager TCMSTableEditorManager */
            $oTableManager = new TCMSTableEditorManager();
            $oTableManager->Init($oDivisionTableConf->id, null);
            $aDefaultData = $oCmsDivision->sqlData;
            unset($aDefaultData['id']);
            $aDefaultData['cms_portal_id'] = $this->sId;
            $aDefaultData['cms_tree_id_tree'] = $sTargetNodeId;
            $oTableManager->Save($aDefaultData);
        }
    }

    /**
     * Activate languages in the frontend for the current session.
     *
     * @return bool
     */
    public function ActivateLanguage()
    {
        $this->oTable->SetActivateAllPortalLanguages();

        return true;
    }

    /**
     * Deactivate temporarily activated languages in the frontend for the current session.
     *
     * @return bool
     */
    public function DeActivateLanguage()
    {
        $this->oTable->SetActivateAllPortalLanguages(false);

        return true;
    }

    public function GetHtmlHeadIncludes()
    {
        /* @var $oPortal TdbCmsPortal */
        $oPortal = $this->oTable;
        $aIncludes = parent::GetHtmlHeadIncludes();
        try {
            $portalLink = $this->getPageService()->getLinkToPortalHomePageAbsolute(array(), $oPortal);
        } catch (Exception $e) {
            $portalLink = '#';
        }
        $aIncludes[] = "<script type=\"text/javascript\">
      function OpenPageWithActiveLanguages(data){
        window.open('$portalLink','');
        window.location.href=window.location.href
      }

      function ReloadActivePage(data){
        window.location.href=window.location.href
      }
      </script>";

        return $aIncludes;
    }

    /**
     * set public methods here that may be called from outside.
     */
    public function DefineInterface()
    {
        parent::DefineInterface();
        $this->methodCallAllowed[] = 'ActivateLanguage';
        $this->methodCallAllowed[] = 'DeActivateLanguage';
    }

    /**
     * {@inheritdoc}
     */
    public function Save(&$postData, $bDataIsInSQLForm = false)
    {
        $defaultLanguageBeforeChange = $this->oTable->fieldCmsLanguageId;
        $languagesBeforeChange = $this->oTable->GetMLTIdList('cms_language');
        $useSlashInSeoUrlsBeforeChange = $this->oTable->fieldUseSlashInSeoUrls;

        $returnVal = parent::Save($postData, $bDataIsInSQLForm);

        $eventDispatcher = $this->getEventDispatcher();
        $this->dispatchChangeDefaultLanguageEvent($eventDispatcher, $defaultLanguageBeforeChange);
        $this->dispatchChangeActiveLanguagesEvent($eventDispatcher, $languagesBeforeChange);
        $this->dispatchChangeUseSlashInSeoUrlsEvent($eventDispatcher, $useSlashInSeoUrlsBeforeChange);

        return $returnVal;
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param string                   $defaultLanguageBeforeChange
     */
    private function dispatchChangeDefaultLanguageEvent(EventDispatcherInterface $eventDispatcher, $defaultLanguageBeforeChange)
    {
        $defaultLanguageAfterChange = $this->oTable->fieldCmsLanguageId;
        if ($defaultLanguageBeforeChange !== $defaultLanguageAfterChange) {
            $languageService = $this->getLanguageService();
            $languageIsoAfterChange = $languageService->getLanguageIsoCode($defaultLanguageAfterChange);
            $languageIsoBeforeChange = $languageService->getLanguageIsoCode($defaultLanguageBeforeChange);

            $event = new LocaleChangedEvent($languageIsoAfterChange, $languageIsoBeforeChange);
            $eventDispatcher->dispatch($event, CoreEvents::CHANGE_DEFAULT_LANGUAGE_FOR_PORTAL);
        }
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param array                    $languagesBeforeChange
     */
    private function dispatchChangeActiveLanguagesEvent(EventDispatcherInterface $eventDispatcher, array $languagesBeforeChange)
    {
        $languagesAfterChange = $this->oTable->GetMLTIdList('cms_language');
        if (array_diff($languagesBeforeChange, $languagesAfterChange) !== array_diff($languagesAfterChange, $languagesBeforeChange)) {
            $event = new ChangeActiveLanguagesForPortalEvent($this->oTable, $languagesBeforeChange, $languagesAfterChange);
            $eventDispatcher->dispatch($event, CoreEvents::CHANGE_ACTIVE_LANGUAGES_FOR_PORTAL);
        }
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param bool                     $useSlashInSeoUrlsBeforeChange
     */
    private function dispatchChangeUseSlashInSeoUrlsEvent(EventDispatcherInterface $eventDispatcher, $useSlashInSeoUrlsBeforeChange)
    {
        $useSlashInSeoUrlsAfterChange = $this->oTable->fieldUseSlashInSeoUrls;
        if ($useSlashInSeoUrlsBeforeChange !== $useSlashInSeoUrlsAfterChange) {
            $eventDispatcher->dispatch(new ChangeUseSlashInSeoUrlsEvent(), CoreEvents::CHANGE_USE_SLASH_IN_SEO_URLS_FOR_PORTAL);
        }
    }

    /**
     * @return PageServiceInterface
     */
    private function getPageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.page_service');
    }
}

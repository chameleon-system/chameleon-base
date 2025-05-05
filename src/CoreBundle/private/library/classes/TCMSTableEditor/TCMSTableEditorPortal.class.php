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
use ChameleonSystem\CoreBundle\Event\ChangeActiveLanguagesForPortalEvent;
use ChameleonSystem\CoreBundle\Event\ChangeUseSlashInSeoUrlsEvent;
use ChameleonSystem\CoreBundle\Event\LocaleChangedEvent;
use ChameleonSystem\CoreBundle\Event\TreeIdMapCompletedEvent;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\UrlNormalization\UrlNormalizationUtil;
use ChameleonSystem\DatabaseMigration\DataModel\LogChangeDataModel;
use ChameleonSystem\DatabaseMigration\Query\MigrationQueryData;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use esono\pkgCmsCache\CacheInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TCMSTableEditorPortal extends TCMSTableEditor
{
    protected array $treeIdMap = [];

    /**
     * the portal being copied (only set on a copy call).
     *
     * @var TCMSPortal
     */
    protected $oOriginalPortal;

    /**
     * {@inheritdoc}
     */
    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();

        /** @var $oMenuItem TCMSTableEditorMenuItem */
        $oMenuItem = new TCMSTableEditorMenuItem();
        $oMenuItem->sItemKey = 'editpagetree';
        $oMenuItem->sDisplayName = ServiceLocator::get('translator')->trans('chameleon_system_core.table_editor_portal.action_edit_tree');
        $oMenuItem->sIcon = 'fas fa-sitemap';
        $oMenuItem->sOnClick = "javascript:var navId = '".TGlobal::OutHTML($this->oTable->sqlData['main_node_tree'])."';if (document.cmseditform.main_node_tree) navId = document.cmseditform.main_node_tree.value; if (navId>0 || (navId != '' && navId != '0')) CreateModalIFrameDialogCloseButton('".PATH_CMS_CONTROLLER."?pagedef=navigationTreePlain&table=cms_tpl_page&noassign=1&rootID='+navId+'&isInIframe=1', 0,0,'".ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_page_tree.headline')."'); else alert('".ServiceLocator::get('translator')->trans('chameleon_system_core.table_editor_portal.error_navigation_node_required')."');";
        $this->oMenuItems->AddItem($oMenuItem);

        // Add language activator
        if (true === $this->allowShowActivateLanguageButton()) {
            $sFunction = 'ActivateLanguage';
            $sText = ServiceLocator::get('translator')->trans('chameleon_system_core.table_editor_portal.action_tmp_enable_inactive_language_for_me');
            $sPostFunction = 'OpenPageWithActiveLanguages';
            if (true === $this->oTable->GetActivateAllPortalLanguages()) {
                $sFunction = 'DeActivateLanguage';
                $sText = ServiceLocator::get('translator')->trans('chameleon_system_core.table_editor_portal.action_disable_tmp_enabled_languages');
                $sPostFunction = 'ReloadActivePage';
            }
            $oMenuItem = new TCMSTableEditorMenuItem();
            $oMenuItem->sItemKey = 'updateTranslationFields';
            $oMenuItem->sDisplayName = $sText;
            $oMenuItem->sIcon = 'fas fa-globe-americas';

            $sCallURL = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript(
                [
                     'pagedef' => 'tableeditor',
                     '_fnc' => $sFunction,
                     'id' => $this->sId,
                     'tableid' => $this->oTableConf->id,
                     'module_fnc' => ['contentmodule' => 'ExecuteAjaxCall'],
                     '_noModuleFunction' => 'true',
                ]
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
     * @param string $sourceRecordID
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
        return ['property_navigations', 'cms_portal_divisions'];
    }

    /**
     * makes it possible to modify the contents fetched from database before the copy
     * is commited.
     */
    protected function OnBeforeCopy()
    {
        parent::OnBeforeCopy();

        /* @var $oSourcePortal TCMSPortal */
        $this->oOriginalPortal = new TCMSPortal();
        $this->oOriginalPortal->Load($this->sSourceId);

        $this->oTable->sqlData['is_default'] = '0';
        $this->oTable->sqlData['home_node_id'] = '';
        $this->oTable->sqlData['name'] = $this->oTable->sqlData['name'].' Copy';
    }

    /**
     * return null if you won't change the main node of the new portal.
     *
     * @return string|null
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
    protected function PostInsertHook($oFields)
    {
        parent::PostInsertHook($oFields);

        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        $this->linkPortalToUser($securityHelper->getUser()?->getId());
        /** @var CacheInterface $cache */
        $cache = ServiceLocator::get('chameleon_system_cms_cache.cache');

        $cache->callTrigger('cms_user', $securityHelper->getUser()?->getId());
    }

    /**
     * copy the tree, the navigations, the divisons, the pages, the modules, etc...
     */
    protected function OnAfterCopy()
    {
        $this->SetSessionCopiedPortalId();
        parent::OnAfterCopy();

        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        $this->linkPortalToUser($securityHelper->getUser()?->getId());

        $this->CopyNaviTree();
        $this->UnsetSessionCopiedPortalId();
        /** @var CacheInterface $cache */
        $cache = ServiceLocator::get('chameleon_system_cms_cache.cache');

        $cache->callTrigger('cms_user', $securityHelper->getUser()?->getId());
    }

    /**
     * @throws ErrorException
     * @throws TPkgCmsException_Log
     */
    protected function linkPortalToUser(?string $userId)
    {
        if (null === $userId) {
            return;
        }
        $query = 'INSERT INTO `cms_user_cms_portal_mlt`
                        SET `source_id` = :userId,
                            `target_id` = :portalId';
        $this->getDatabaseConnection()->executeQuery($query, ['userId' => $userId, 'portalId' => $this->sId]);

        /** @var BackendSessionInterface $backendSession */
        $backendSession = ServiceLocator::get('chameleon_system_cms_backend.backend_session');

        $migrationQueryData = new MigrationQueryData('cms_user_cms_portal_mlt', $backendSession->getCurrentEditLanguageIso6391());
        $migrationQueryData
            ->setFields([
                'source_id' => $userId,
                'target_id' => $this->sId,
            ])
        ;
        $aQuery = [new LogChangeDataModel($migrationQueryData, LogChangeDataModel::TYPE_INSERT)];

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
        if (array_key_exists('sCopiedPortalId', $_SESSION)) {
            unset($_SESSION['sCopiedPortalId']);
        }
    }

    /**
     * Copy the portals cms_tree.
     *
     * @throws Exception
     */
    protected function CopyNaviTree()
    {
        $portalRootTreeId = $this->oOriginalPortal->sqlData['main_node_tree'];
        $portalRootNode = new TdbCmsTree();
        if (false === $portalRootNode->Load($portalRootTreeId)) {
            throw new Exception(sprintf('Failed to portal root tree (cms_tree) with id %s', $portalRootTreeId));
        }
        $systemPages = $this->GetPortalSystemPagesAsArray();
        $sourceNodeData = $portalRootNode->sqlData;
        $cmsConfig = TdbCmsConfig::GetInstance();
        if (null === $cmsConfig) {
            throw new Exception('No cms config found!');
        }

        $baseLanguageIso = $cmsConfig->GetFieldTranslationBaseLanguage()->fieldIso6391;
        $languages = $cmsConfig->GetFieldBasedTranslationLanguageArray();
        $languages[$baseLanguageIso] = 'base language';

        /** @var TranslatorInterface $translator */
        $translator = ServiceLocator::get('translator');
        /** @var LanguageServiceInterface $languageService */
        $languageService = ServiceLocator::get('chameleon_system_core.language_service');
        /** @var UrlNormalizationUtil $urlUtil */
        $urlUtil = ServiceLocator::get('chameleon_system_core.util.url_normalization');
        $newTreeId = $this->CopyOneNode($sourceNodeData, $portalRootNode->fieldParentId, $systemPages);

        $copyText = sprintf(
            ' [%s]',
            $translator->trans(
                'chameleon_system_core.cms_module_table_editor.copied_record_suffix'
            )
        );
        foreach ($languages as $languageIso => $languageName) {
            $languageObject = $languageService->getLanguageFromIsoCode($languageIso);
            if (null === $languageObject) {
                throw new Exception(sprintf('Failed to load language iso code %s from language table.', $languageIso));
            }
            $treeEditorManager = TTools::GetTableEditorManager('cms_tree', $newTreeId, $languageObject->id);
            $treeEditorManager->AllowEditByAll($this->bAllowEditByAll);
            /** @var TCMSTableEditorTree $treeTableEditor */
            $treeTableEditor = $treeEditorManager->oTableEditor;
            $newNameValue = $treeTableEditor->oTable->sqlData['name'].$copyText;
            $urlName = $urlUtil->normalizeUrl($treeTableEditor->oTable->sqlData['urlname'].$copyText);
            $treeTableEditor->SaveFields(['name' => $newNameValue, 'urlname' => $urlName], false);
            $treeTableEditor->UpdateSubtreePathCache($newTreeId);
        }

        $this->SaveField('main_node_tree', $newTreeId);
        $this->addToTreeIdMap($portalRootTreeId, $newTreeId);
        $this->CopySubtree($portalRootTreeId, $newTreeId, $systemPages);
        $this->dispatchTreeIdMapCompletedEvent();
    }

    /**
     * There are cases when you need to know the tree ids of the source and the copied tree node, to e.g. change a reference to the copied tree node in a proprietary bundle.
     * This dispatches an event that holds a map of all old tree ids to the new tree ids.
     *
     * @return EventDispatcherInterface
     */
    protected function dispatchTreeIdMapCompletedEvent()
    {
        $this->getEventDispatcher()->dispatch(new TreeIdMapCompletedEvent($this->treeIdMap), CoreEvents::TREE_ID_MAP_COMPLETED);
    }

    /**
     * Get all portal system pages as array.
     * Was needed on portal copy to set the tree connection of the system pages to the copied trees.
     *
     * @return array
     */
    protected function GetPortalSystemPagesAsArray()
    {
        $aPortalSystemPages = [];
        $oSystemPageList = $this->oTable->GetFieldCmsPortalSystemPageList();
        while ($oSystemPage = $oSystemPageList->Next()) {
            if (array_key_exists($oSystemPage->fieldCmsTreeId, $aPortalSystemPages)) {
                if (!is_array($aPortalSystemPages[$oSystemPage->fieldCmsTreeId])) {
                    $oPortalSystemPage = $aPortalSystemPages[$oSystemPage->fieldCmsTreeId];
                    $aPortalSystemPages[$oSystemPage->fieldCmsTreeId] = [];
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
     * @param array $aPortalSystemPages
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
     * @param array $aSourceNode
     * @param string $targetParentId
     * @param array $aPortalSystemPages
     *
     * @return string
     */
    protected function CopyOneNode($aSourceNode, $targetParentId, $aPortalSystemPages)
    {
        $oTreeManager = TTools::GetTableEditorManager('cms_tree', $aSourceNode['id']);
        $oNewTree = $oTreeManager->DatabaseCopy(false, ['parent_id' => $targetParentId], $this->bIsCopyAllLanguageValues);
        $this->SetPortalSystemPageNodeToCopiedNode($aPortalSystemPages, $aSourceNode['id'], $oNewTree->id);
        // Move Tree-Paths...
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
        $this->CopyNodeNavigations($aSourceNode, $oTreeManager->sId);

        $this->addToTreeIdMap($aSourceNode['id'], $oTreeManager->sId);

        return $oTreeManager->sId;
    }

    protected function addToTreeIdMap(string $sourceTreeId, string $copiedTreeId): void
    {
        $this->treeIdMap[$sourceTreeId] = $copiedTreeId;
    }

    /**
     * Set copied tree id to given portal system pages.
     *
     * @param array $aPortalSystemPages
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
     * @param string $sCopiedNodeId
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
     * @param array $aSourceNode - sqlData of the source node
     * @param string $sTargetNodeId - id of the target tree node
     */
    protected function CopyNodeDivision($aSourceNode, $sTargetNodeId)
    {
        /** @var $oCmsDivision TdbCmsDivision */
        $oCmsDivision = TdbCmsDivision::GetNewInstance();
        if ($oCmsDivision->LoadFromField('cms_tree_id_tree', $aSourceNode['id'])) {
            $oDivisionTableConf = $oCmsDivision->GetTableConf();

            /** @var $oTableManager TCMSTableEditorManager */
            $oTableManager = new TCMSTableEditorManager();
            $oTableManager->Init($oDivisionTableConf->id, null);
            $aDefaultData = $oCmsDivision->sqlData;
            unset($aDefaultData['id']);
            $aDefaultData['cms_portal_id'] = $this->sId;
            $aDefaultData['cms_tree_id_tree'] = $sTargetNodeId;
            $oTableManager->ForceHiddenFieldWriteOnSave(true);
            $oTableManager->Save($aDefaultData);
        }
    }

    /**
     * copies the navigations attached to a tree node to a new tree node.
     *
     * @param array $aSourceNode - sqlData of the source node
     * @param string $sTargetNodeId - id of the target tree node
     */
    protected function CopyNodeNavigations($aSourceNode, $sTargetNodeId)
    {
        // it's possible that more than one navigation is attached to a tree node
        // so we need to loop through all navigations attached to the source node
        $query = 'SELECT id FROM `cms_portal_navigation` WHERE `tree_node` = :tree_node_id';
        $navigationIds = $this->getDatabaseConnection()->fetchFirstColumn($query, ['tree_node_id' => $aSourceNode['id']]);
        if (empty($navigationIds)) {
            return;
        }

        foreach ($navigationIds as $navigationId) {
            $navigation = TdbCmsPortalNavigation::GetNewInstance();
            if ($navigation->Load($navigationId)) {
                $navigationTableConf = $navigation->GetTableConf();

                /** @var $oTableManager TCMSTableEditorManager */
                $oTableManager = new TCMSTableEditorManager();
                $oTableManager->Init($navigationTableConf->id, null);
                $oTableManager->AllowEditByAll(true);
                $aDefaultData = $navigation->sqlData;
                unset($aDefaultData['id']);
                $aDefaultData['cms_portal_id'] = $this->sId;
                $aDefaultData['tree_node'] = $sTargetNodeId;
                $oTableManager->ForceHiddenFieldWriteOnSave(true);
                $oTableManager->Save($aDefaultData);
            }
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
            $portalLink = $this->getPageService()->getLinkToPortalHomePageAbsolute([], $oPortal);
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
    public function Save($postData, $bDataIsInSQLForm = false)
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
     * @param string $defaultLanguageBeforeChange
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

    private function dispatchChangeActiveLanguagesEvent(EventDispatcherInterface $eventDispatcher, array $languagesBeforeChange)
    {
        $languagesAfterChange = $this->oTable->GetMLTIdList('cms_language');
        if (array_diff($languagesBeforeChange, $languagesAfterChange) !== array_diff($languagesAfterChange, $languagesBeforeChange)) {
            $event = new ChangeActiveLanguagesForPortalEvent($this->oTable, $languagesBeforeChange, $languagesAfterChange);
            $eventDispatcher->dispatch($event, CoreEvents::CHANGE_ACTIVE_LANGUAGES_FOR_PORTAL);
        }
    }

    /**
     * @param bool $useSlashInSeoUrlsBeforeChange
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
        return ServiceLocator::get('chameleon_system_core.page_service');
    }
}

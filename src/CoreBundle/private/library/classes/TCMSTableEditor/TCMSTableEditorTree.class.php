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
use ChameleonSystem\CoreBundle\Event\ChangeNavigationTreeNodeEvent;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\TableEditor\NestedSet\NestedSetHelperInterface;
use ChameleonSystem\CoreBundle\Util\FieldTranslationUtil;
use ChameleonSystem\CoreBundle\Util\UrlNormalization\UrlNormalizationUtil;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;

class TCMSTableEditorTree extends TCMSTableEditor
{
    /**
     * switch to prevent copy, new and delete buttons.
     *
     * @var bool
     */
    protected $editOnly = true;

    /**
     * called after inserting a new record.
     *
     * @param TIterator $oFields - the fields inserted
     */
    protected function PostInsertHook($oFields)
    {
        parent::PostInsertHook($oFields);
        // get the parent_id from oFields
        $parentId = 0;
        $oFields->GoToStart();
        $bFoundField = false;
        while (!$bFoundField && ($oField = $oFields->Next())) {
            /** @var $oField TCMSField */
            if ('parent_id' == $oField->name) {
                $parentId = $oField->data;
                $bFoundField = true;
            }
        }
        $oFields->GoToStart();
        $countQuery = 'SELECT MAX(`entry_sort`) AS newsort
                      FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($this->oTableConf->sqlData['name'])."`
                     WHERE `parent_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($parentId)."'
                       AND `id` <> '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."'
                     ";
        if ($counttemp = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($countQuery))) {
            $entry_sort = $counttemp['newsort'] + 1;
            $this->SaveField('entry_sort', $entry_sort);
        }

        $this->getNestedSetHelper()->newNode($this->oTable->id, $this->oTable->fieldParentId);
        $this->writeSqlLog();

        $insertedNode = new TdbCmsTree($this->sId);
        $event = new ChangeNavigationTreeNodeEvent([$insertedNode]);
        $this->getEventDispatcher()->dispatch($event, CoreEvents::ADD_NAVIGATION_TREE_NODE);
    }

    /**
     * {@inheritdoc}
     */
    protected function PostSaveHook($oFields, $oPostTable)
    {
        $fieldTranslationUtil = $this->getFieldTranslationUtil();
        $translatedUrlnameFieldName = $fieldTranslationUtil->getTranslatedFieldName($this->oTableConf->fieldName, 'urlname');

        $updatedNodes = [];
        $updatedNodes[] = new TdbCmsTree($this->sId);
        $oFields->GoToStart();
        $urlname = '';
        while ($oField = $oFields->Next()) {
            /** @var $oField TCMSField */
            if ('name' === $oField->name) {
                $urlname = $this->getUrlNormalizationUtil()->normalizeUrl($oField->data);
            }
            if ('urlname' === $oField->name && empty($oField->data)) {
                $this->AllowEditByAll(true);
                $this->SaveField($translatedUrlnameFieldName, $urlname);
                $this->AllowEditByAll(false);
            }
        }
        $oFields->GoToStart();
        parent::PostSaveHook($oFields, $oPostTable);
        // update subtree path cache
        $updatedNodes[] = $this->UpdateSubtreePathCache($this->sId);
        $this->HandleHiddenChangeForCacheTrigger($oPostTable);

        $this->getNestedSetHelper()->updateNode($this->oTable);
        $this->writeSqlLog();

        // update cache
        $this->getCacheService()->callTrigger($this->oTableConf->fieldName, $this->sId);

        $event = new ChangeNavigationTreeNodeEvent($updatedNodes);
        $this->getEventDispatcher()->dispatch($event, CoreEvents::UPDATE_NAVIGATION_TREE_NODE);
    }

    /**
     * If field hidden changed from yes to no we have to save a parent node is not hidden.
     * Because hidden nodes are not in navigation cache.
     *
     * @param TdbCmstree $oPostTable
     */
    protected function HandleHiddenChangeForCacheTrigger($oPostTable)
    {
        if (property_exists($this, 'oTablePreChangeData') && is_object($this->oTablePreChangeData) && true === $this->oTablePreChangeData->fieldHidden && false === $oPostTable->fieldHidden) {
            $this->SaveParentToTriggerNavigationCache($this->oTable);
        }
    }

    /**
     * Recursive function to save a parent that is not hidden to clear cache trigger for navigations.
     *
     * @param TdbCmsTree $oNode
     */
    protected function SaveParentToTriggerNavigationCache($oNode)
    {
        $oParentNode = $oNode->GetParentNode();
        if ($oParentNode && false === $oParentNode->fieldHidden) {
            $this->getCacheService()->callTrigger($oParentNode->table, $oParentNode->id);
        } else {
            $this->SaveParentToTriggerNavigationCache($oParentNode);
        }
    }

    /**
     * cache the tree path to each node of the given subtree.
     *
     * @var string
     */
    public function UpdateSubtreePathCache($sNodeId)
    {
        $updatedNodes = [];
        $oNode = $this->oTable;
        if (!empty($sNodeId) && $sNodeId != $this->oTable->id) {
            $oNode = TdbCmsTree::GetNewInstance();
            if (false == $oNode->Load($sNodeId)) {
                $oNode = false;
            }
        }
        if ($oNode) {
            $oNode->TriggerUpdateOfPathCache();
            $updatedNodes[] = $oNode;
        }

        return $updatedNodes;
    }

    /**
     * {@inheritdoc}
     */
    public function DatabaseCopy($bLanguageCopy = false, $aOverloadedFields = [], $bCopyAllLanguages = true)
    {
        $this->SetCopyTreeModeInSession();
        $result = parent::DatabaseCopy(
            $bLanguageCopy,
            $aOverloadedFields,
            $bCopyAllLanguages
        );
        $this->UnSetCopyTreeModeInSession();

        return $result;
    }

    /**
     * makes it possible to modify the contents written to database after the copy
     * is committed.
     * Copy page connections which belongs to the tree.
     */
    protected function OnAfterCopy()
    {
        parent::OnAfterCopy();
        $sQuery = "SELECT * FROM `cms_tree_node` WHERE `cms_tree_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->oSourceTable->id)."'";
        $oPageConnections = TdbCmsTreeNodeList::GetList($sQuery);
        while ($oPageConnection = $oPageConnections->Next()) {
            $oTreeManager = TTools::GetTableEditorManager('cms_tree_node', $oPageConnection->id);
            $oTreeManager->DatabaseCopy(false, ['cms_tree_id' => $this->sId], $this->bIsCopyAllLanguageValues);
        }
        $this->getNestedSetHelper()->initializeTree();
        $this->writeSqlLog();
    }

    /**
     * Set session variable to show following copy actions that a tree was copied.
     * Is needed to prevent some actions on copy page which adds new tree connections.
     */
    protected function SetCopyTreeModeInSession()
    {
        if ((!array_key_exists('bCopyTreeMode', $_SESSION)) || (array_key_exists('bCopyTreeMode', $_SESSION) && !is_bool($_SESSION['bCopyTreeMode']))) {
            $_SESSION['bCopyTreeMode'] = true;
        }
    }

    /**
     * Check if tree was copied in previous copy actions.
     *
     * @return bool
     */
    public static function IsCopyTreeMode()
    {
        $bPreventCopyTreeNodeConnectionsOnPageCopy = false;
        if (array_key_exists('bCopyTreeMode', $_SESSION) && true === $_SESSION['bCopyTreeMode']) {
            $bPreventCopyTreeNodeConnectionsOnPageCopy = true;
        }

        return $bPreventCopyTreeNodeConnectionsOnPageCopy;
    }

    /**
     * Reset copy tree mode.
     * Do this after tree was copied completely.
     */
    protected function UnSetCopyTreeModeInSession()
    {
        if (array_key_exists('bCopyTreeMode', $_SESSION)) {
            unset($_SESSION['bCopyTreeMode']);
        }
    }

    /**
     * returns an iterator with the menuitems for the current table. if you want to add your own
     * items, overwrite the GetCustomMenuItems (NOT GetMenuItems)
     * the iterator will always be reset to start.
     *
     * @return TIterator
     */
    public function GetMenuItems()
    {
        if (is_null($this->oMenuItems)) {
            $this->oMenuItems = new TIterator();
            // std menuitems...
            /** @var SecurityHelperAccess $securityHelper */
            $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

            $tableInUserGroup = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_ACCESS, $this->oTableConf->fieldName);
            if ($tableInUserGroup) {
                // edit
                if ($securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT, $this->oTableConf->sqlData['name'])) {
                    $oMenuItem = new TCMSTableEditorMenuItem();
                    $oMenuItem->sDisplayName = ServiceLocator::get('translator')->trans('chameleon_system_core.action.save');
                    $oMenuItem->sIcon = 'far fa-save';
                    $oMenuItem->sOnClick = 'SaveTreeNodeAjax();';
                    $this->oMenuItems->AddItem($oMenuItem);
                }
                // now add custom items
                $this->GetCustomMenuItems();
            }
        } else {
            $this->oMenuItems->GoToStart();
        }

        return $this->oMenuItems;
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();

        $aIncludes[] = '<style type="text/css">
      .tableeditcontainer {
        margin: 5px 0px 0px 0px;
      }

      .tableeditcontainer .leftTD {
        min-width: 150px;
      }
      </style>';

        $oGlobal = TGlobal::instance();

        $aIncludes[] = "<script type=\"text/javascript\">
      function SaveTreeNodeAjax() {
        document.cmseditform.elements['module_fnc[contentmodule]'].value = 'ExecuteAjaxCall';
        document.cmseditform._fnc.value = 'AjaxSave';

        PostAjaxForm('cmseditform', function(data,statusText) {
          if(SaveViaAjaxCallback(data,statusText)) {
            parent.updateTreeNode(document.cmseditform);
          }
        });
      }
      </script>";

        return $aIncludes;
    }

    /**
     * allows subclasses to overwrite default values.
     *
     * @param TIterator $oFields
     */
    public function _OverwriteDefaults($oFields)
    {
        parent::_OverwriteDefaults($oFields);
        $oGlobal = TGlobal::instance();

        $oFields->GoToStart();
        while ($oField = $oFields->Next()) {
            /** @var $oField TCMSField */
            if ('parent_id' == $oField->name) {
                $oField->data = $oGlobal->GetUserData('parent_id');
            }
        }
        $oFields->GoToStart();
    }

    /**
     * deletes the record and all language childs; updates all references to this record.
     *
     * @param int $sId
     */
    public function Delete($sId = null)
    {
        $this->getNestedSetHelper()->deleteNode($this->oTable->id);

        $deletedNodes = [];

        $oNode = new TdbCmsTree();
        $oNode->Load($sId);
        $deletedNodes[] = $oNode;
        $oPages = $oNode->GetAllLinkedPages();
        parent::Delete($sId);
        // and delete all children as well
        $query = "SELECT * FROM `cms_tree` WHERE `parent_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sId)."'";
        $oCmsTreeList = TdbCmsTreeList::GetList($query);
        $oTreeEditor = new TCMSTableEditorManager();
        while ($oCmsTree = $oCmsTreeList->Next()) {
            $deletedNodes[] = $oCmsTree;
            $oTreeEditor->Init($this->oTableConf->id, $oCmsTree->id);
            $oTreeEditor->Delete($oCmsTree->id);
        }
        $oPageTableConf = new TCMSTableConf();
        $oPageTableConf->LoadFromField('name', 'cms_tpl_page');
        while ($oPage = $oPages->Next()) {
            $oPageEditor = new TCMSTableEditorManager();
            $oPageEditor->Init($oPageTableConf->id, $oPage->id);
            $oPageEditor->oTableEditor->UpdatePageNaviBreadCrumb();
        }
        $this->writeSqlLog();

        $event = new ChangeNavigationTreeNodeEvent($deletedNodes);
        $this->getEventDispatcher()->dispatch($event, CoreEvents::DELETE_NAVIGATION_TREE_NODE);
    }

    /**
     * {@inheritdoc}
     */
    public function GetObjectShortInfo($postData = [])
    {
        $oRecordData = parent::GetObjectShortInfo();

        $oRecordData->pageID = $this->oTable->GetLinkedPage();

        return $oRecordData;
    }

    /**
     * Delete record reference from records connected with [tablename]_id
     * Delete connected tree nodes instead of deleting connection id.
     */
    public function DeleteIdConnectedRecordReferences()
    {
        $sSelect = "SELECT * FROM `cms_tree_node` WHERE `cms_tree_node`.`cms_tree_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."'";
        $oTreeNodeList = TdbCmsTreeNodeList::GetList($sSelect);
        while ($oTreeNode = $oTreeNodeList->Next()) {
            $oTableEditorManager = TTools::GetTableEditorManager('cms_tree_node', $oTreeNode->id);
            $oTableEditorManager->Delete();
        }
        TCMSTableEditorPage::UpdateCmsListNaviCache();
        parent::DeleteIdConnectedRecordReferences();
    }

    private function writeSqlLog()
    {
        $command = <<<COMMAND
TCMSLogChange::initializeNestedSet('{$this->oTable->table}', 'parent_id', 'entry_sort');
COMMAND;
        TCMSLogChange::WriteSqlTransactionWithPhpCommands('update nested set for table '.$this->oTable->table, [$command]);
    }

    private function getFieldTranslationUtil(): FieldTranslationUtil
    {
        return ServiceLocator::get('chameleon_system_core.util.field_translation');
    }

    /**
     * @return NestedSetHelperInterface
     */
    protected function getNestedSetHelper()
    {
        $factory = ServiceLocator::get('chameleon_system_core.table_editor_nested_set_helper_factory');

        return $factory->createNestedSetHelper($this->oTable->table, 'parent_id', 'entry_sort');
    }

    private function getUrlNormalizationUtil(): UrlNormalizationUtil
    {
        return ServiceLocator::get('chameleon_system_core.util.url_normalization');
    }
}

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
use ChameleonSystem\CoreBundle\Event\ChangeNavigationTreeConnectionEvent;
use ChameleonSystem\CoreBundle\Service\RegistryService;
use ChameleonSystem\CoreBundle\ServiceLocator;

class TCMSTableEditorTreeConnection extends TCMSTableEditor
{
    /**
     * set this to true if you want to prevent cms_tpl_page update on delete requests.
     *
     * @var bool
     */
    public $bPreventPageUpdate = false;
    /**
     * set this to true if you want to prevent cms_tpl_page copies on tree connection copies.
     *
     * @var bool
     */
    public $bPreventPageCopy = false;

    /**
     * {@inheritdoc}
     */
    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();

        $oGlobal = TGlobal::instance();

        $aIncludes[] = "<script type=\"text/javascript\">
      function SaveTreeNodeAjax() {
        document.cmseditform.elements['module_fnc[contentmodule]'].value = 'ExecuteAjaxCall';
        document.cmseditform._fnc.value = 'AjaxSave';

        PostAjaxForm('cmseditform', function(data,statusText) {
          if(SaveViaAjaxCallback(data,statusText)) {
            var nodeID = '".$oGlobal->GetUserData('id')."';
            parent.updateTreeNode(document.cmseditform);
            parent.CloseModalIFrameDialog();
          }
        });
      }
      </script>";

        return $aIncludes;
    }

    /**
     * {@inheritdoc}
     */
    protected function OnAfterCopy()
    {
        parent::OnAfterCopy();
        $oGlobal = TGlobal::instance();
        $bPreventTemplateEngineRedirect = false;
        $sPreventTemplateEngineRedirect = $oGlobal->GetUserData('preventTemplateEngineRedirect');
        if (!empty($sPreventTemplateEngineRedirect) && '1' === $sPreventTemplateEngineRedirect) {
            $bPreventTemplateEngineRedirect = true;
        }

        if (!empty($this->oTable->fieldContid) && !$bPreventTemplateEngineRedirect && !$this->bPreventPageCopy) {
            $sPageID = $this->oTable->fieldContid;

            $registryService = $this->getRegistryService();

            // Get Page Id if page was copied in previous copy
            // if page was not copied before copy page
            $sCopyPageId = $registryService->get($sPageID);
            if (is_null($sCopyPageId)) {
                $this->SetCopyTreeConnectionModeInSession();
                $iTableID = TTools::GetCMSTableId('cms_tpl_page');
                $oTableEditor = new TCMSTableEditorManager();
                $oTableEditor->Init($iTableID, $sPageID);
                $sCopiedPortalId = TCMSTableEditorPortal::GetCopiedPortalId();
                $aPageOncopyOverwriteParameter = [];
                if ($sCopiedPortalId) {
                    $aPageOncopyOverwriteParameter['cms_portal_id'] = $sCopiedPortalId;
                }
                if ($this->oTable->sqlData['active'] && true === TCMSTableEditorTree::IsCopyTreeMode()) {
                    $aPageOncopyOverwriteParameter['primary_tree_id_hidden'] = $this->oTable->sqlData['cms_tree_id'];
                    $oRecordData = $oTableEditor->DatabaseCopy(false, $aPageOncopyOverwriteParameter, $this->bIsCopyAllLanguageValues);
                } else {
                    $oRecordData = $oTableEditor->DatabaseCopy(false, $aPageOncopyOverwriteParameter, $this->bIsCopyAllLanguageValues);
                    if (false === TCMSTableEditorTree::IsCopyTreeMode()) {
                        $this->SaveField('active', 0);
                    }
                }
                $sNewPageID = $oRecordData->id;
                $this->SaveField('contid', $sNewPageID);
                $registryService->set($sPageID, $sNewPageID);
                TCMSTableEditorPage::UpdateCmsListNaviCache($sNewPageID);
                $this->UnSetCopyTreeConnectionModeInSession();
            } else {
                $this->SaveField('contid', $sCopyPageId);
                TCMSTableEditorPage::UpdateCmsListNaviCache($sCopyPageId);
            }
            // update tree search string cache
        }
    }

    /**
     * Set session variable to show following copy actions that a tree connection was copied.
     * Was used to prevent some actions on copy page which adds new tree connections.
     */
    protected function SetCopyTreeConnectionModeInSession()
    {
        if ((!array_key_exists('bCopyTreeConnectionMode', $_SESSION)) || (array_key_exists('bCopyTreeConnectionMode', $_SESSION) && !is_bool($_SESSION['bCopyTreeConnectionMode']))) {
            $_SESSION['bCopyTreeConnectionMode'] = true;
        }
    }

    /**
     * Check if tree connection was copied in previous copy actions.
     *
     * @return bool
     */
    public static function IsCopyTreeConnectionMode()
    {
        $bPreventCopyTreeNodeConnectionsOnPageCopy = false;
        if (array_key_exists('bCopyTreeConnectionMode', $_SESSION) && true === $_SESSION['bCopyTreeConnectionMode']) {
            $bPreventCopyTreeNodeConnectionsOnPageCopy = true;
        }

        return $bPreventCopyTreeNodeConnectionsOnPageCopy;
    }

    /**
     * Reset copy tree connection mode.
     * Do this after tree connection was copied completely.
     */
    protected function UnSetCopyTreeConnectionModeInSession()
    {
        if (array_key_exists('bCopyTreeConnectionMode', $_SESSION)) {
            unset($_SESSION['bCopyTreeConnectionMode']);
        }
    }

    protected function GetCopyPageId($sSourcePageId, $sCopyPageId)
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function PostInsertHook($oFields)
    {
        parent::PostInsertHook($oFields);

        $oGlobal = TGlobal::instance();
        $oFields->GoToStart();
        while ($oField = $oFields->Next()) {
            /** @var $oField TCMSField */
            if ('tbl' == $oField->oDefinition->sqlData['name']) {
                $this->SaveField($oField->oDefinition->sqlData['name'], 'cms_tpl_page');
            } elseif ('cms_tree_id' == $oField->oDefinition->sqlData['name']) {
                $sCmsTreeId = $oGlobal->GetUserData('cms_tree_id');
                if (!empty($sCmsTreeId)) {
                    $this->SaveField($oField->oDefinition->sqlData['name'], $sCmsTreeId);
                }
            } elseif ('contid' == $oField->oDefinition->sqlData['name']) {
                $sRecordId = $oGlobal->GetUserData('contid');
                if (!empty($sRecordId)) {
                    $this->SaveField($oField->oDefinition->sqlData['name'], $sRecordId, true);
                }
            } elseif ('active' == $oField->oDefinition->sqlData['name']) {
                $sActive = $oGlobal->GetUserData('active');
                if (!empty($sActive) && '1' == $sActive) {
                    $this->SaveField($oField->oDefinition->sqlData['name'], '1');
                }
            }
        }
        $oFields->GoToStart();

        /**
         * @var TdbCmsTreeNode $newTreeConnection
         */
        $newTreeConnection = $this->oTable;
        $event = new ChangeNavigationTreeConnectionEvent($newTreeConnection);
        $this->getEventDispatcher()->dispatch($event, CoreEvents::ADD_NAVIGATION_TREE_CONNECTION);
    }

    /**
     * updates the primary_tree_id_hidden field of the connected cms_tpl_page record.
     *
     * @param string $sTreeNodeID
     * @param string $sConnectedPageID
     *
     * @return bool
     */
    protected function UpdatePrimaryTreeNodeOfConnectedPage($sTreeNodeID, $sConnectedPageID)
    {
        $bPageUpdated = false;
        if (!empty($sTreeNodeID) && !empty($sConnectedPageID)) {
            // set primary node in cms_tpl_page if missing
            $oTableEditor = TTools::GetTableEditorManager('cms_tpl_page', $sConnectedPageID);
            if (empty($oTableEditor->oTableEditor->oTable->sqlData['primary_tree_id_hidden'])) {
                $bPageUpdated = $oTableEditor->SaveField('primary_tree_id_hidden', $sTreeNodeID);
            }
        }

        return $bPageUpdated;
    }

    /**
     * {@inheritdoc}
     */
    protected function _OverwriteDefaults($oFields)
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
     * {@inheritdoc}
     */
    protected function PostSaveHook($oFields, $oPostTable)
    {
        parent::PostSaveHook($oFields, $oPostTable);

        $oFields->GoToStart();
        while ($oField = $oFields->Next()) {
            if ('contid' === $oField->name && !empty($oField->data)) {
                TCMSTableEditorPage::UpdateCmsListNaviCache($oField->data);
                $this->UpdatePrimaryTreeNodeOfConnectedPage($this->oTable->sqlData['cms_tree_id'], $oField->data);
                break;
            }
        }
        $oFields->GoToStart();

        /**
         * @var TdbCmsTreeNode $changedTreeConnection
         */
        $changedTreeConnection = $this->oTable;
        $event = new ChangeNavigationTreeConnectionEvent($changedTreeConnection);
        $this->getEventDispatcher()->dispatch($event, CoreEvents::UPDATE_NAVIGATION_TREE_CONNECTION);
    }

    /**
     * {@inheritdoc}
     */
    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();

        $oMenuItem = new TCMSTableEditorMenuItem();
        $oMenuItem->sItemKey = 'BackToList';
        $oMenuItem->sDisplayName = ServiceLocator::get('translator')->trans('chameleon_system_core.table_editor_tree_connection.action_show_list');
        $oMenuItem->sIcon = 'far fa-list-alt';
        $oMenuItem->sOnClick = "location.href='".PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript(['pagedef' => 'tablemanagerframe', 'id' => $this->oTableConf->sqlData['id'], 'sRestrictionField' => 'cms_tree_id', 'sRestriction' => $this->oTable->sqlData['cms_tree_id']])."'";
        $this->oMenuItems->AddItemToStart($oMenuItem);
    }

    /**
     * {@inheritdoc}
     */
    protected function DeleteRecordReferences()
    {
        $this->DeleteRecordReferenceInPage();
        parent::DeleteRecordReferences();
    }

    /**
     * If connected page and tree connection has the same tree connection
     * reset the page tree connection.
     */
    protected function DeleteRecordReferenceInPage()
    {
        if (!empty($this->oTable->fieldContid)) {
            $oConnectedPage = TdbCmsTplPage::GetNewInstance();
            if ($oConnectedPage->Load($this->oTable->fieldContid)) {
                if ($oConnectedPage->fieldPrimaryTreeIdHidden == $this->oTable->fieldCmsTreeId) {
                    $oTableEditorManager = TTools::GetTableEditorManager('cms_tpl_page', $oConnectedPage->id);
                    $oTableEditorManager->oTableEditor->bPreventPreGetSQLHookOnFields = true;
                    $oTableEditorManager->SaveField('primary_tree_id_hidden', '', true);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function Delete($sId = null)
    {
        if ($sId === $this->sId) {
            $deletedTreeConnection = $this->oTable;
        } else {
            $deletedTreeConnection = new TdbCmsTreeNode();
            $deletedTreeConnection->Load($sId);
        }
        parent::Delete($sId);

        $event = new ChangeNavigationTreeConnectionEvent($deletedTreeConnection);
        $this->getEventDispatcher()->dispatch($event, CoreEvents::DELETE_NAVIGATION_TREE_CONNECTION);
    }

    private function getRegistryService(): RegistryService
    {
        return ServiceLocator::get('chameleon_system_core.service.registry_service');
    }
}

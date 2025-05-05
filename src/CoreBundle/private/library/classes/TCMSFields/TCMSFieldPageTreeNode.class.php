<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\TreeServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\UrlUtil;

class TCMSFieldPageTreeNode extends TCMSFieldTreeNode
{
    /**
     * @var string
     */
    protected $treeNodeSelectModulePagedef = 'navigationTreeSingleSelect';

    /**
     * @var string
     */
    protected $treeNodeSelectAdditionalNodesPagedef = 'navigationTreePlain';

    public function GetHTML()
    {
        $primaryPath = $this->GetHTMLPrimaryTree();
        $additionalPaths = $this->GetHTMLSecondaryTree();
        $selectPrimaryTreeNodeButton = TCMSRender::DrawButton(ServiceLocator::get('translator')->trans('chameleon_system_core.field_page_tree_node.assign_primary_node'), "javascript:loadTreeNodeSelection('".TGlobal::OutJS($this->name)."',document.cmseditform.".TGlobalBase::OutHTML($this->name).'.value);', 'fas fa-check');
        $selectAdditionalTreeNodesButton = TCMSRender::DrawButton(ServiceLocator::get('translator')->trans('chameleon_system_core.field_page_tree_node.assign_secondary_nodes'), 'javascript:openFullTree();', 'fas fa-check-double');

        $viewRenderer = $this->getViewRenderer();
        $viewRenderer->AddSourceObject('fieldName', $this->name);
        $viewRenderer->AddSourceObject('fieldValue', $this->_GetHTMLValue());
        $viewRenderer->AddSourceObject('primaryPath', $primaryPath);
        $viewRenderer->AddSourceObject('additionalPaths', $additionalPaths);
        $viewRenderer->AddSourceObject('selectPrimaryTreeNodeButton', $selectPrimaryTreeNodeButton);
        $viewRenderer->AddSourceObject('selectAdditionalTreeNodesButton', $selectAdditionalTreeNodesButton);

        return $viewRenderer->Render('TCMSFieldPageTreeNode/treeNodes.html.twig', null, false);
    }

    /**
     * Returns constructed tree items for primary navigation references (main connection and inheritance source).
     *
     * @return string
     */
    protected function GetHTMLPrimaryTree()
    {
        $tree = null;
        if (!empty($this->data)) {
            $tree = $this->getTreeService()->getById($this->data);
        }

        return $this->getRenderedTreeNodePath($tree);
    }

    /**
     * Returns constructed tree items for secondary navigation references (additionals in view / no inheritance).
     *
     * @return string
     */
    protected function GetHTMLSecondaryTree()
    {
        $sAdditionalPaths = '';

        /**
         * @var TdbCmsTreeList $oTreeList
         */
        $oTreeList = $this->oTableRow->GetTreeNodesObjects(false);
        while ($oTree = $oTreeList->Next()) {
            if ($oTree->id != $this->data) {
                $sAdditionalPaths .= $this->getRenderedTreeNodePath($oTree);
            }
        }

        return $sAdditionalPaths;
    }

    /**
     * Constructs and renders a tree path in printable form or an optional fallback.
     *
     * @return string
     */
    private function getRenderedTreeNodePath(?TdbCmsTree $tree = null)
    {
        // If no tree node was supplied and rendering empty paths was requested, return fallback.
        if (null === $tree) {
            return ServiceLocator::get('translator')->trans('chameleon_system_core.field_page_tree_node.no_node_assigned');
        }
        // Retrieve portal through referenced linked page.
        $path = $tree->GetTextPathToNode('/', false, true);

        // Add portal name to path.
        $linkedPageObject = $tree->GetLinkedPageObject();
        if (false !== $linkedPageObject) {
            $portal = $linkedPageObject->GetPortal();
            if (null !== $portal) {
                $path = $portal->GetName().'/'.$path;
            }
        }
        // Form rendered path from slash separated path string.
        $treeSubPath = str_replace('/', '</li><li class="breadcrumb-item">', $path);

        return sprintf('<ol class="breadcrumb pl-0"><li class="breadcrumb-item"><i class="fas fa-sitemap"></i></li><li class="breadcrumb-item">%s</li></ol>', $treeSubPath);
    }

    /**
     * {@inheritdoc}
     */
    public function PreGetSQLHook()
    {
        $bReturnValue = parent::PreGetSQLHook();
        // Dont manage connections on copy tree or copy tree connections because manage connections add new tree connections but on copy these connections already exists
        if (false === TCMSTableEditorTree::IsCopyTreeMode() && false === TCMSTableEditorTreeConnection::IsCopyTreeConnectionMode()) {
            $this->ManageTreeConnections();
        }

        return $bReturnValue;
    }

    /**
     * {@inheritdoc}
     */
    public function GetSQL()
    {
        $sTreeConnectionId = '';
        if (!is_null($this->recordId)) {
            $sTreeConnectionId = $this->GetCurrentPrimaryTreeHiddenId();
        }

        return $sTreeConnectionId;
    }

    /**
     * Retrieves the actual primary hidden tree id.
     * Gets the next secondary tree id if page was saved with empty tree id hidden.
     *
     * @return string
     */
    protected function GetCurrentPrimaryTreeHiddenId()
    {
        $sActualTreeHiddenId = parent::GetSQL();
        $bFoundTreeHidden = false;
        $aConnectedTreeNodes = $this->oTableRow->GetPageTreeNodes();
        if (is_array($aConnectedTreeNodes) && count($aConnectedTreeNodes) > 0) {
            $sOriginalTreeConnectionData = $this->GetOriginalTreeConnectionData();
            foreach ($aConnectedTreeNodes as $sConnectedTreeNodeId) {
                if (!$bFoundTreeHidden && empty($sActualTreeHiddenId) && $sConnectedTreeNodeId != $sOriginalTreeConnectionData) {
                    $sActualTreeHiddenId = $sConnectedTreeNodeId;
                    $bFoundTreeHidden = true;
                }
            }
        }

        return $sActualTreeHiddenId;
    }

    /**
     * If original reference is available delete them and create new references.
     */
    protected function ManageTreeConnections()
    {
        $sTableId = TTools::GetCMSTableId('cms_tree_node');
        $sOriginalTreeConnectionData = $this->GetOriginalTreeConnectionData();
        if (false !== $sOriginalTreeConnectionData && $sOriginalTreeConnectionData != $this->data) { // tree id changed, so delete the old one
            $oAlreadyAvailableTreeConnectionList = $this->GetAlreadyAvailableTreeConnections($sOriginalTreeConnectionData);
            if (!is_null($oAlreadyAvailableTreeConnectionList)) {
                $this->DeleteTreeConnection($oAlreadyAvailableTreeConnectionList->Current(), $sTableId);
            }

            if (!empty($this->data) && is_null($this->GetAlreadyAvailableTreeConnections($this->data))) {
                $this->SaveNewTreeConnection($sTableId);
            }
        } else {
            // check if tree connection is available
            if (!empty($this->data) && is_null($this->GetAlreadyAvailableTreeConnections($this->data))) {
                $this->SaveNewTreeConnection($sTableId);
            }
        }
    }

    /**
     * Get the original tree connection id. Return false if the original record can't be loaded or the original tree connection id is empty.
     *
     * @return string|bool
     */
    protected function GetOriginalTreeConnectionData()
    {
        $sOriginalTreeConnectionData = false;
        $oOriginal = new TCMSRecord();
        $oOriginal->table = $this->sTableName;
        if ($oOriginal->Load($this->recordId) && !empty($oOriginal->sqlData[$this->oDefinition->sqlData['name']])) {
            $sOriginalTreeConnectionData = $oOriginal->sqlData[$this->oDefinition->sqlData['name']];
        }

        return $sOriginalTreeConnectionData;
    }

    /**
     * Retrieves existing tree connections as list for the given tree id.
     * Returns null if given tree id was empty or no connections were found.
     *
     * @param string $sTreeId
     *
     * @return TdbCmsTreeNodeList
     *
     * @throws ErrorException
     * @throws TPkgCmsException_Log
     */
    protected function GetAlreadyAvailableTreeConnections($sTreeId)
    {
        $oAlreadyAvailableTreeConnectionList = null;
        if (!empty($sTreeId)) {
            $query = "SELECT * FROM `cms_tree_node`
                   WHERE `contid` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->recordId)."'
                     AND `tbl` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sTableName)."'
                     AND `cms_tree_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sTreeId)."'
                     ";

            /**
             * Note! we cant use the Tdb class here, because this would filter a newly created tree connection
             * if workflow is enabled and the transaction is created by the node itself.
             *
             * @var $oCmsTreeNodeList TCMSRecordList
             */
            $oCmsTreeNodeList = new TCMSRecordList();
            $oCmsTreeNodeList->Load($query);
            if (0 != $oCmsTreeNodeList->Length()) {
                $oAlreadyAvailableTreeConnectionList = $oCmsTreeNodeList;
            }
        }

        return $oAlreadyAvailableTreeConnectionList;
    }

    /**
     * Delete given tree connection.
     *
     * @param TdbCmsTreeNode $oCmsTreeNode
     * @param string $sTableId
     */
    protected function DeleteTreeConnection($oCmsTreeNode, $sTableId)
    {
        $oTableEditor = new TCMSTableEditorManager();
        $oTableEditor->Init($sTableId, $oCmsTreeNode->id);
        $oTableEditor->oTableEditor->bPreventPageUpdate = true;
        $oTableEditor->Delete($oCmsTreeNode->id);
    }

    /**
     * Saves new tree connection from actual record.
     *
     * @param string $sTableId
     */
    protected function SaveNewTreeConnection($sTableId)
    {
        $oTableEditor = new TCMSTableEditorManager();
        $oTableEditor->Init($sTableId, null);
        $aData = ['contid' => $this->recordId, 'tbl' => $this->sTableName, 'cms_tree_id' => $this->data, 'active' => '1'];
        $oTableEditor->Save($aData);
    }

    /**
     * {@inheritdoc}
     */
    public function GetCMSHtmlHeadIncludes()
    {
        $aIncludes = parent::GetCMSHtmlHeadIncludes();

        $navigationTreeUrl = $this->getNavigationTreeModuleUrl();

        $aIncludes[] = "<script type=\"text/javascript\">
        function loadTreeNodeSelection(fieldName,id) {
          if(document.getElementById('cms_portal_id').options != undefined) {
            var portalID = document.getElementById('cms_portal_id').options[document.getElementById('cms_portal_id').selectedIndex].value;
            var portalName = document.getElementById('cms_portal_id').options[document.getElementById('cms_portal_id').selectedIndex].text;
          } else {
            var portalID = document.getElementById('cms_portal_id').value;
          }

          if(portalID != '0' && portalID != '') {
            // change selectBox to hidden field
            if(document.getElementById('cms_portal_id').options != undefined) {
              $('#cms_portal_id').remove();
              $('#tooltipcms_portal_id_content').parent('td').prepend('<input type=\"hidden\" name=\"cms_portal_id\" id=\"cms_portal_id\" value=\"' + portalID + '\" />' + portalName);
              $('#tooltipcms_portal_id_content').siblings('.switchToRecordBox').remove();
            }
            CreateModalIFrameDialogCloseButton('".PATH_CMS_CONTROLLER.'?pagedef='.TGlobal::OutJS($this->treeNodeSelectModulePagedef)."&id=' + id + '&fieldName=' + fieldName + '&portalID=' + portalID + '&currentPageId=".$this->oTableRow->id."', 0, 0, '".ServiceLocator::get('translator')->trans('chameleon_system_core.field_page_tree_node.assign_primary_node')."');
          } else {
            toasterMessage('".ServiceLocator::get('translator')->trans('chameleon_system_core.field_page_tree_node.error_no_portal_selected')."','WARNING');
          }
        }

        function openFullTree() {
          if(document.getElementById('cms_portal_id').options != undefined) {
            var portalID = document.getElementById('cms_portal_id').options[document.getElementById('cms_portal_id').selectedIndex].value;
            var portalName = document.getElementById('cms_portal_id').options[document.getElementById('cms_portal_id').selectedIndex].text;
            if(portalID != '0' && portalID != '') {
              $('#cms_portal_id').remove();
              $('#tooltipcms_portal_id_content').parent('td').prepend('<input type=\"hidden\" name=\"cms_portal_id\" id=\"cms_portal_id\" value=\"' + portalID + '\" />' + portalName);
              $('#tooltipcms_portal_id_content').siblings('.switchToRecordBox').remove();
              CreateModalIFrameDialogCloseButton('".$navigationTreeUrl."',0,0,'".ServiceLocator::get('translator')->trans('chameleon_system_core.field_page_tree_node.assign_secondary_nodes')."');
            } else {
              toasterMessage('".ServiceLocator::get('translator')->trans('chameleon_system_core.field_page_tree_node.error_no_portal_selected')."','WARNING');
            }
          } else {
            var portalID = document.getElementById('cms_portal_id').value;
            if(portalID != '0' && portalID != '') {
              CreateModalIFrameDialogCloseButton('".$navigationTreeUrl."',0,0,'".ServiceLocator::get('translator')->trans('chameleon_system_core.field_page_tree_node.assign_secondary_nodes')."');
            } else {
              toasterMessage('".ServiceLocator::get('translator')->trans('chameleon_system_core.field_page_tree_node.error_no_portal_selected')."','WARNING');
            }
          }
        }
      </script>";

        return $aIncludes;
    }

    private function getNavigationTreeModuleUrl(): string
    {
        $urlUtilService = $this->getUrlUtilService();

        return PATH_CMS_CONTROLLER.'?'.$urlUtilService->getArrayAsUrl([
                'pagedef' => $this->treeNodeSelectAdditionalNodesPagedef,
                'table' => 'cms_tpl_page',
                'rootID' => TCMSTreeNode::TREE_ROOT_ID,
                'id' => $this->oTableRow->id,
                'isInIframe' => '1',
                'fieldName' => $this->name,
                'primaryTreeNodeId' => $this->oTableRow->fieldPrimaryTreeIdHidden,
            ], '', '&');
    }

    private function getTreeService(): TreeServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.tree_service');
    }

    private function getViewRenderer(): ViewRenderer
    {
        return ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
    }

    private function getUrlUtilService(): UrlUtil
    {
        return ServiceLocator::get('chameleon_system_core.util.url');
    }
}

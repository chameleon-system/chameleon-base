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

class TCMSFieldPageTreeNode extends TCMSFieldTreeNode
{
    public function GetHTML()
    {
        $sPath = $this->GetHTMLPrimaryTree();
        $sAdditionalPaths = $this->GetHTMLSecondaryTree();
        $html = '';
        $html .= '<input type="hidden" id="'.TGlobalBase::OutHTML($this->name).'" name="'.TGlobalBase::OutHTML($this->name).'" value="'.TGlobalBase::OutHTML($this->data).'" />
      <span id="'.TGlobalBase::OutHTML($this->name).'posDummy"></span>';
        $html .= '<h1>'.TGlobal::Translate('chameleon_system_core.field_page_tree_node.primary_node').':</h1><div id="'.TGlobalBase::OutHTML($this->name).'_path">'.$sPath.'</div>';
        $html .= '<div class="cleardiv">&nbsp;</div>';
        $html .= TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.field_page_tree_node.assign_primary_node'), "javascript:loadTreeNodeSelection('".TGlobal::OutJS($this->name)."',document.cmseditform.".TGlobalBase::OutHTML($this->name).'.value);', URL_CMS.'/images/icons/page_navigation.gif');
        $html .= '<div class="cleardiv">&nbsp;</div>';

        $html .= '<h1>'.TGlobal::Translate('chameleon_system_core.field_page_tree_node.secondary_nodes').':</h1><div id="'.TGlobalBase::OutHTML($this->name).'_additional_paths">'.$sAdditionalPaths.'</div>';
        $html .= '<div class="cleardiv">&nbsp;</div>';

        $html .= TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.field_page_tree_node.assign_secondary_nodes'), 'javascript:openFullTree();', URL_CMS.'/images/icons/page_navigation.gif');
        $html .= '<div class="cleardiv">&nbsp;</div>';

        return $html;
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
     * Returns constructed tree items for secondary navigtion references (additionals in view / no inheritance).
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
     * @param TdbCmsTree|null $tree
     *
     * @return string
     */
    private function getRenderedTreeNodePath(TdbCmsTree $tree = null)
    {
        // If no tree node was supplied and rendering empty paths was requested, return fallback.
        if (null === $tree) {
            return TGlobal::Translate('chameleon_system_core.field_page_tree_node.no_node_assigned');
        }
        // Retrieve portal through referenced linked page.
        $portal = $tree->GetLinkedPageObject()->GetPortal();
        $path = $tree->GetTextPathToNode('/', false, true);
        if (null !== $portal) {
            $path = $portal->GetName().'/'.$path;
        }
        // Form rendered path from slash separated path string.
        $treeSubPath = str_replace('/', '</div></li><li><div class="treesubpath">', $path);
        $renderedPath = sprintf('<div class="treeField"><ul><li><div class="treesubpath">%s</div></li></ul>', $treeSubPath);
        if (isset($tree)) {
            $dateInformation = $this->GetPageTreeConnectionDateInformationHTML($tree->id, $this->oTableRow->id);
            $renderedPath .= sprintf('<div class="dateinfo">%s</div>', $dateInformation);
        }
        $renderedPath .= '</div>';

        return $renderedPath;
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
     * @param string         $sTableId
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
        $aData = array('contid' => $this->recordId, 'tbl' => $this->sTableName, 'cms_tree_id' => $this->data, 'active' => '1');
        $oTableEditor->Save($aData);
    }

    /**
     * Returns an array of all js, css, or other header includes that are required
     * in the cms for this field. each include should be in one line, and they
     * should always be typed the same way so that no includes are included mor than once.
     *
     * @return array
     */
    public function GetCMSHtmlHeadIncludes()
    {
        $aIncludes = parent::GetCMSHtmlHeadIncludes();

        $url = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript(array('pagedef' => 'CMSModulePageTreePlain', 'table' => 'cms_tpl_page', 'rootID' => '99', 'id' => $this->oTableRow->id));
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
            CreateModalIFrameDialogCloseButton('".PATH_CMS_CONTROLLER."?pagedef=treenodeselect&id=' + id + '&fieldName=' + fieldName + '&portalID=' + portalID);
          } else {
            toasterMessage('".TGlobal::Translate('chameleon_system_core.field_page_tree_node.error_no_portal_selected')."','WARNING');
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
              CreateModalIFrameDialogCloseButton('".$url."',0,0,'".TGlobal::Translate('chameleon_system_core.field_page_tree_node.assign_secondary_nodes')."');
            } else {
              toasterMessage('".TGlobal::Translate('chameleon_system_core.field_page_tree_node.error_no_portal_selected')."','WARNING');
            }
          } else {
            var portalID = document.getElementById('cms_portal_id').value;
            if(portalID != '0' && portalID != '') {
              CreateModalIFrameDialogCloseButton('".$url."',0,0,'".TGlobal::Translate('chameleon_system_core.field_page_tree_node.assign_secondary_nodes')."');
            } else {
              toasterMessage('".TGlobal::Translate('chameleon_system_core.field_page_tree_node.error_no_portal_selected')."','WARNING');
            }
          }
        }
      </script>";

        return $aIncludes;
    }

    /**
     * @return TreeServiceInterface
     */
    private function getTreeService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.tree_service');
    }
}

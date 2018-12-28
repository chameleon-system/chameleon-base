<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * shows the CMS media manager component.
 *
 * @deprecated since 6.2.0 - Chameleon has a new media manager
/**/
class CMSMediaManager extends TCMSModelBase
{
    /**
     * the SQL table name that holds the tree.
     *
     * @var string
     */
    protected $sTreeTable = 'cms_media_tree';

    /**
     * the tree widget class object.
     *
     * @var TCMSTreeWidget
     */
    protected $oTreeWidget = null;

    public function Init()
    {
        parent::Init();
        $this->oTreeWidget = $this->LoadTreeWidget();
    }

    public function &Execute()
    {
        $this->data = parent::Execute();

        $recordID = $this->global->GetUserData('recordID');
        $this->data['recordID'] = $recordID;

        $tableID = $this->global->GetUserData('tableID');
        $this->data['tableID'] = $tableID;

        $fieldName = $this->global->GetUserData('fieldName');
        $this->data['fieldName'] = $fieldName;

        $this->GetTableName();
        $this->data['maxUploadSize'] = TTools::getUploadMaxSize();

        /** @var $oImageTableConf TCMSTableConf */
        $oImageTableConf = new TCMSTableConf();
        $oImageTableConf->LoadFromField('name', 'cms_media');
        $this->data['id'] = $oImageTableConf->sqlData['id'];
        $this->data['cmsident'] = $oImageTableConf->sqlData['cmsident'];

        /** @var $oMediaTreeTableConf TCMSTableConf */
        $oMediaTreeTableConf = new TCMSTableConf();
        $oMediaTreeTableConf->LoadFromField('name', 'cms_media_tree');
        $this->data['mediaTreeTableID'] = $oMediaTreeTableConf->sqlData['id'];

        // root tree node
        $this->data['iRootNodeId'] = 1;
        $this->data['sRootNodeName'] = TGlobal::Translate('chameleon_system_core.cms_module_media_manager.root_folder');

        $this->data['sListURL'] = $this->GetListURL();

        $this->data['sModuleSpotName'] = $this->sModuleSpotName;

        return $this->data;
    }

    /**
     * initialises the tree widget class.
     *
     * @return TCMSTreeWidget
     */
    protected function LoadTreeWidget()
    {
        $oTreeWidget = new TCMSTreeWidget();
        $sPageDef = $this->global->GetUserData('pagedef');
        $oTreeWidget->Init($this->sTreeTable, $sPageDef, $this->sModuleSpotName);
        $oTreeWidget->SetContextMenuView('contextMenuAssets');
        $oTreeWidget->SetRootNodeName(TGlobal::Translate('chameleon_system_core.cms_module_media_manager.root_folder'));

        return $oTreeWidget;
    }

    public function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = array('MoveNode', 'RenameNode', 'GetChildren', 'DeleteNode', 'CheckConnectionsAndDelete', 'CheckConnections', 'CheckDirItemsConnectionsAndDelete', 'PasteFiles', 'DeleteFile');
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    protected function GetListURL()
    {
        $aExternalListParams = $this->GetAdditionalListParams();
        if (!is_array($aExternalListParams)) {
            $aExternalListParams = array();
        }
        $aExternalListParams['pagedef'] = 'tableManagerMediaManager';
        $aExternalListParams['id'] = $this->data['id'];
        $sListURL = PATH_CMS_CONTROLLER.'?'.str_replace('&amp;', '&', TTools::GetArrayAsURL($aExternalListParams));

        return $sListURL;
    }

    /**
     * loads additional list parameters like iMaxWidth, iMaxHeight, sAllowedFileTypes.
     *
     * @return array
     */
    protected function GetAdditionalListParams()
    {
        $aAdditionalListParams = array('iMaxWidth', 'iMaxHeight', 'sAllowedFileTypes');

        $aExternalListParams = array();

        foreach ($aAdditionalListParams as $sListParam) {
            if ($this->global->UserDataExists($sListParam)) {
                $aExternalListParams[$sListParam] = $this->global->GetUserData($sListParam);
            }
        }

        return $aExternalListParams;
    }

    /**
     * loads table name for given table ID.
     */
    protected function GetTableName()
    {
        $tableID = $this->global->GetUserData('tableID');
        if (!empty($tableID)) {
            /** @var $oCmsTblConf TdbCmsTblConf */
            $oCmsTblConf = TdbCmsTblConf::GetNewInstance();
            $oCmsTblConf->Load($tableID);
            $this->data['tableName'] = $oCmsTblConf->sqlData['name'];
        } else {
            $this->data['tableName'] = false;
        }
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        // first the includes that are needed for the all fields
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery/jQueryUI/ui.core.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery-form-4.2.2/jquery.form.min.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery/BlockUI/jquery.blockUI.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<link href="'.TGlobal::GetPathTheme().'/css/table.css" rel="stylesheet" type="text/css" />'; // we need this for the list of connections by delete events
        $aIncludes[] = '<script type="text/javascript">
      var messageNewFolder = \''.TGlobal::OutJS(TGlobal::Translate('chameleon_system_core.cms_module_media_manager.action_new_folder')).'\';
      var messageFileDeleted = \''.TGlobal::OutJS(TGlobal::Translate('chameleon_system_core.cms_module_media_manager.msg_delete_success')).'\';
      var messageNoFolderChoosen = \''.TGlobal::OutJS(TGlobal::Translate('chameleon_system_core.cms_module_media_manager.error_folder_name_missing')).'\';
      var messageUploadSuccess = \''.TGlobal::OutJS(TGlobal::Translate('chameleon_system_core.cms_module_media_manager.msg_upload_success')).'\';
      var messageUploadError = \''.TGlobal::OutJS(TGlobal::Translate('chameleon_system_core.cms_module_media_manager.error_upload_error')).'\';
      var messageUploadMore = \''.TGlobal::OutJS(TGlobal::Translate('chameleon_system_core.cms_module_media_manager.upload_more_files')).'\';
      var messageErrorNoUploadData = \''.TGlobal::OutJS(TGlobal::Translate('chameleon_system_core.cms_module_media_manager.error_no_file_selected')).'\';
      var messageUploadNotAllowedHere = \''.TGlobal::OutJS(TGlobal::Translate('chameleon_system_core.cms_module_media_manager.error_upload_to_root_folder_not_permitted')).'\';
      var messageUploadButtonTitle = \''.TGlobal::OutJS(TGlobal::Translate('chameleon_system_core.cms_module_media_manager.action_upload')).'\';
      var messageChooseFolderNow = \''.TGlobal::OutJS(TGlobal::Translate('chameleon_system_core.cms_module_media_manager.msg_select_move_target')).'\';
      var messageErrorMoveNoFiles = \''.TGlobal::OutJS(TGlobal::Translate('chameleon_system_core.cms_module_media_manager.error_no_move_source_selected')).'\';
      var messageErrorPasteNoFiles = \''.TGlobal::OutJS(TGlobal::Translate('chameleon_system_core.cms_module_media_manager.error_no_past_source_selected')).'\';
      var messageUploadSuccessSingle = \''.TGlobal::OutJS(TGlobal::Translate('chameleon_system_core.cms_module_media_manager.msg_single_file_upload_success')).'\';
      </script>';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/cms.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/modules/mediaManager.js').'" type="text/javascript"></script>';

        $aTreeIncludes = $this->oTreeWidget->GetHtmlHeadIncludes();
        $aIncludes = array_merge($aIncludes, $aTreeIncludes);

        return $aIncludes;
    }

    /**
     * loads sub nodes of a tree node
     * is called via ajax.
     *
     * @return array
     */
    public function GetChildren()
    {
        $sNodeID = $this->global->GetUserData('id');
        $aReturnVal = $this->oTreeWidget->GetChildren($sNodeID);

        return $aReturnVal;
    }

    /**
     * deletes a node.
     *
     * @return bool|string - returns the deleted node id on success
     */
    public function DeleteNode()
    {
        $sNodeID = $this->global->GetUserData('sNodeID');
        $bReturnVal = $this->oTreeWidget->DeleteNode($sNodeID);

        return $bReturnVal;
    }

    // -----------------------------------------------------------------------

    /**
     * renames a node
     * is called via ajax.
     *
     * @return stdClass
     */
    public function RenameNode()
    {
        $sNodeID = $this->global->GetUserData('sNodeID');
        $sNewTitle = trim($this->global->GetUserData('sNewTitle'));
        $oReturnObj = $this->oTreeWidget->RenameNode($sNodeID, $sNewTitle);

        return $oReturnObj;
    }

    /**
     * moves node and all subnodes
     * is called via ajax.
     *
     * @return stdClass
     */
    public function MoveNode()
    {
        $sNodeID = $this->global->GetUserData('sourceNodeID');
        $sNewParentNodeID = $this->global->GetUserData('targetNodeID');
        $iNewIndex = $this->global->GetUserData('position');
        $oReturnObj = $this->oTreeWidget->MoveNode($sNodeID, $sNewParentNodeID, $iNewIndex);

        return $oReturnObj;
    }

    protected function CheckDirItemsConnectionsAndDelete($sNodeId = false)
    {
        $returnVal = '';
        if ($this->global->UserDataExists('sNodeID') || !empty($sNodeId)) {
            $sDeleteNodeID = $sNodeId;
            if (empty($sDeleteNodeID)) {
                $sDeleteNodeID = $this->global->GetUserData('sNodeID');
            }
            $oMediaTree = TdbCmsMediaTree::GetNewInstance();
            if ($oMediaTree->Load($sDeleteNodeID)) {
                $oFileList = $oMediaTree->GetFilesInDirectory();
                while ($oFile = $oFileList->Next()) {
                    $oTableManager = TTools::GetTableEditorManager('cms_media', $oFile->id);
                    $aFoundConnections = $oTableManager->HandleExternalFunctionCall('FetchConnections', array($oFile->id));
                    $sFileConnectionInfoHtml = $this->RenderFileConnectionInfo($aFoundConnections, $oFile->id, false);
                    if (!empty($sFileConnectionInfoHtml)) {
                        $returnVal .= $sFileConnectionInfoHtml;
                    } else {
                        $this->DeleteFile($oFile->id);
                    }
                }
                $oMediaTreeChildList = $oMediaTree->GetChildren();
                while ($oMediaTreeChild = $oMediaTreeChildList->Next()) {
                    $returnVal .= $this->CheckDirItemsConnectionsAndDelete($oMediaTreeChild->id);
                }
                if (empty($returnVal)) {
                    $this->oTreeWidget->DeleteNode($sDeleteNodeID);
                }
            }
        }
        if (empty($sNodeId) && !empty($returnVal)) {
            $returnVal .= '
                <div style="padding-top: 25px;">
                '.TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.cms_module_media_manager.confirm_none_empty_folder_delete'), "javascript:DeleteMediaDir('".TGlobal::OutJS($sDeleteNodeID)."');", TGlobal::GetPathTheme().'/images/icons/accept.png', 'DeleteMediaDirComplete').'
                '.TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.action.abort'), 'javascript:CloseDeleteCheckDialog();', TGlobal::GetPathTheme().'/images/icons/action_stop.gif').'
                </div>
              ';
            $returnVal = array($sDeleteNodeID, $returnVal);
        } elseif (empty($sNodeId) && empty($returnVal)) {
            $returnVal = $sDeleteNodeID;
        }

        return $returnVal;
    }

    protected function RenderFileConnectionInfo($aFoundConnections, $sFileId, $bRenderDeleteButton = true, $bInfoOnFileDelete = true)
    {
        $foundConnectionsHTML = '';
        if (is_array($aFoundConnections) && count($aFoundConnections) > 0) {
            $oImage = new TCMSImage();
            $oImage->Load($sFileId);
            $oThumb = $oImage->GetThumbnail(100, 100);
            if ($bInfoOnFileDelete) {
                $sTitle = TGlobal::Translate('chameleon_system_core.cms_module_media_manager.action_delete');
                $sDescription = 'chameleon_system_core.cms_module_media_manager.msg_on_delete_file_in_use';
            } else {
                $sTitle = '';
                $sDescription = 'chameleon_system_core.cms_module_media_manager.msg_file_in_use';
            }

            $foundConnectionsHTML = '
        <div class="dialogHeadline">'.TGlobal::Translate('chameleon_system_core.cms_module_media_manager.file_id', array('%id%' => $sFileId)).' '.$sTitle.'</div>
        <div class="dialogContent">
          <div class="notice" style="margin-top:10px;margin-bottom:10px;">'.TGlobal::Translate('chameleon_system_core.cms_module_media_manager.msg_attention').'!<br />'.TGlobal::Translate($sDescription).'</div>
          <div style="float: left; width: 110px;">
           <img src="'.$oThumb->GetFullURL().'" style="border: 1px solid #000000;">
           <div>'.$oImage->aData['description'].'</div>
          </div>
          <div style="float:right;width:570px;">
            <table cellpadding="0" cellspacing="0" width="100%">
             <tr class="bg-primary">
               <td>'.TGlobal::Translate('chameleon_system_core.cms_module_media_manager.used_in_table').'</td>
               <td>ID</td>
               <td>'.TGlobal::Translate('chameleon_system_core.cms_module_media_manager.used_in_record').'</td>
               <td>'.TGlobal::Translate('chameleon_system_core.cms_module_media_manager.used_in_field').'</td>
             </tr>';

            $count = 0;
            foreach ($aFoundConnections as $oImage) {
                // print_r($oImage);
                if ($count % 2) {
                    $class = 'evenRowStyleNoHand';
                } else {
                    $class = 'oddRowStyleNoHand';
                }

                $foundConnectionsHTML .= "<tr class=\"{$class}\">
               <td>".$oImage->tableName.'</td>
               <td>'.$oImage->id.'</td>
               <td>'.$oImage->recordName."</td>
               <td>{$oImage->fieldTranslationName} - (".$oImage->fieldName.")</td>
              </tr>\n
            ";

                ++$count;
            }
            $foundConnectionsHTML .= '</table>
          </div>
            <div class="cleardiv">&nbsp;</div>';
            if ($bRenderDeleteButton) {
                $foundConnectionsHTML .= '
                <div style="padding-top: 25px;">
                '.TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.cms_module_media_manager.confirm_used_image'), "javascript:SendDeleteForm('".$sFileId."');", TGlobal::GetPathTheme().'/images/icons/accept.png').'
                '.TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.action.abort'), 'javascript:CloseDeleteCheckDialog();', TGlobal::GetPathTheme().'/images/icons/action_stop.gif').'
                </div>
            </div>
              ';
            } else {
                $foundConnectionsHTML .= '
                  <div style="padding-top: 25px;">
                  '.TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.action.close'), 'javascript:CloseModalIFrameDialog();', TGlobal::GetPathTheme().'/images/icons/action_stop.gif').'
                  </div>
              </div>';
            }
        }

        return $foundConnectionsHTML;
    }

    /**
     * check all connections for file deletion
     * delete file if not connected.
     */
    public function CheckConnectionsAndDelete()
    {
        $bDoNotDelete = false;
        $returnVal = array();
        $aTableBlackList = null;
        if ($this->global->UserDataExists('fileIDs')) {
            if ($this->global->UserDataExists('bDoNotDelete') && true == $this->global->GetUserData('bDoNotDelete')) {
                $bDoNotDelete = true;
                $aTableBlackList = array('pkg_custom_search', 'pkg_custom_search_result_item');
            }

            $aFileIDs = explode(',', $this->global->GetUserData('fileIDs'));
            $iTableID = TTools::GetCMSTableId('cms_media');
            $oTableEditor = new TCMSTableEditorManager();
            /** @var $oTableEditor TCMSTableEditorMedia */
            foreach ($aFileIDs as $fileID) {
                $oTableEditor->Init($iTableID, $fileID);
                $aFoundConnections = $oTableEditor->HandleExternalFunctionCall('FetchConnections', array($fileID, $aTableBlackList));
                $sFileConnectionInfoHtml = $this->RenderFileConnectionInfo($aFoundConnections, $fileID, !$bDoNotDelete, !$bDoNotDelete);
                if (!empty($sFileConnectionInfoHtml)) {
                    $returnVal[] = $sFileConnectionInfoHtml;
                } else {
                    if (!$bDoNotDelete) {
                        $this->DeleteFile($fileID);
                    }
                }
            }
        }

        return $returnVal;
    }

    /**
     * check all connections for media usages.
     */
    public function GetFileUsages()
    {
        $returnVal = array();
        if ($this->global->UserDataExists('fileIDs')) {
            $aFileIDs = explode(',', $this->global->GetUserData('fileIDs'));
            $iTableID = TTools::GetCMSTableId('cms_media');
            $oTableEditor = new TCMSTableEditorManager();
            /** @var $oTableEditor TCMSTableEditorMedia */
            foreach ($aFileIDs as $fileID) {
                $oTableEditor->Init($iTableID, $fileID);
                $aFoundConnections = $oTableEditor->HandleExternalFunctionCall('FetchConnections', array($fileID));
                $sFileConnectionInfoHtml = $this->RenderFileConnectionInfo($aFoundConnections, $fileID, false, false);
                if (!empty($sFileConnectionInfoHtml)) {
                    $returnVal[] = $sFileConnectionInfoHtml;
                }
            }
        }

        return $returnVal;
    }

    /**
     * delete file and all connections.
     *
     * @param string $fileID - null by default
     */
    public function DeleteFile($fileID = null)
    {
        $returnVal = false;

        if (is_null($fileID)) { // so the method was called via ajax
            $fileID = $this->global->GetUserData('fileID');
        }

        if (!empty($fileID)) {
            $iTableID = TTools::GetCMSTableId('cms_media');
            /** @var $oTableEditor TCMSTableEditorMedia */
            $oTableEditor = new TCMSTableEditorManager();
            $oTableEditor->Init($iTableID, $fileID);
            $oTableEditor->Delete($fileID);
            $returnVal = true;
        }

        return $returnVal;
    }

    /**
     * check all connections for file.
     *
     * @notice this method is not used currently
     *
     * @todo needs to be moved to tableEditor class to allow call from media details view
     */
    public function CheckConnections()
    {
        $this->fileID = $this->global->GetUserData('fileID');
        $returnVal = true;
        if (!empty($this->fileID)) {
            $iTableID = TTools::GetCMSTableId('cms_media');
            /** @var $oTableEditor TCMSTableEditorMedia */
            $oTableEditor = new TCMSTableEditorManager();
            $oTableEditor->Init($iTableID, $this->fileID);

            $aFoundConnections = $oTableEditor->HandleExternalFunctionCall('FetchConnections', array($this->fileID));
            if ($aFoundConnections) {
                $returnVal = '
          <table cellpadding="0" cellspacing="0" width="450">
           <tr class="bg-primary">
             <td>'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_media_manager.used_in_table')).'</td>
             <td>ID</td>
             <td>'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_media_manager.used_in_record')).'</td>
             <td>'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_media_manager.used_in_field')).'</td>
           </tr>';

                $count = 0;
                foreach ($aFoundConnections as $oImage) {
                    if ($count % 2) {
                        $class = 'evenRowStyleNoHand';
                    } else {
                        $class = 'oddRowStyleNoHand';
                    }

                    $returnVal .= "<tr class=\"{$class}\">
             <td>".$oImage->tableName.'</td>
             <td>'.$oImage->id.'</td>
             <td>'.$oImage->recordName."</td>
             <td>{$oImage->fieldTranslationName} - (".$oImage->fieldName.")</td>
            </tr>\n";

                    ++$count;
                }
                $returnVal .= "</table>\n";
            }
        }

        return $returnVal;
    }

    /**
     * paste marked files to selected folder.
     */
    public function PasteFiles()
    {
        $fileListParameter = $this->global->GetUserData('files');
        $treeNodeID = $this->global->GetUserData('nodeID');
        $fileList = explode(',', $fileListParameter);

        if (null === $treeNodeID || '' === $treeNodeID || 0 === count($fileList)) {
            return false;
        }
        foreach ($fileList as $fileId) {
            $fileId = trim($fileId);
            if ('' === $fileId) {
                continue;
            }
            $tableEditorManager = TTools::GetTableEditorManager('cms_media', $fileId);
            if (null !== $tableEditorManager->oTableEditor->oTable && false !== $tableEditorManager->oTableEditor->oTable->sqlData) {
                $tableEditorManager->SaveField('cms_media_tree_id', $treeNodeID, false);
            }
        }
        $returnData = array();
        $returnData['nodeID'] = $treeNodeID;

        return $returnData;
    }
}

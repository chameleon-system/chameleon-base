<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\i18n\TranslationConstants;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * shows the CMS Document Manager component.
 * /**/
class CMSDocumentManager extends TCMSModelBase
{
    protected $tableID;
    protected $recordID;
    protected $documentID;
    protected $fieldName;

    /**
     * the SQL table name that holds the tree.
     *
     * @var string
     */
    protected $sTreeTable = 'cms_document_tree';

    /**
     * the tree widget class object.
     *
     * @var TCMSTreeWidget
     */
    protected $oTreeWidget;

    public function Init()
    {
        parent::Init();

        if ($this->global->UserDataExists('recordID')) {
            $recordID = $this->global->GetUserData('recordID');
            $this->recordID = $recordID;

            $tableID = $this->global->GetUserData('tableID');
            $this->tableID = $tableID;

            $fieldName = $this->global->GetUserData('fieldName');
            $this->fieldName = $fieldName;
        }

        $this->oTreeWidget = $this->LoadTreeWidget();
    }

    public function Execute()
    {
        parent::Execute();

        // check if we got a record ID, if not... assume we are in standalone mode
        // (you cannot set connections in this mode)
        $this->GetTableName();

        if ($this->recordID) {
            $this->data['recordID'] = $this->recordID;
            $this->data['tableID'] = $this->tableID;
            $this->data['fieldName'] = $this->fieldName;
            $this->data['mltTable'] = $this->data['tableName'].'_'.$this->fieldName.'_cms_document_mlt';
            $this->data['standaloneMode'] = false;
        } else {
            $this->data['recordID'] = false;
            $this->data['tableID'] = false;
            $this->data['fieldName'] = false;
            $this->data['standaloneMode'] = true;
        }

        /** @var $oDocumentManagerTableConf TCMSTableConf */
        $oDocumentManagerTableConf = new TCMSTableConf();
        $oDocumentManagerTableConf->LoadFromField('name', 'cms_document');
        $this->data['id'] = $oDocumentManagerTableConf->sqlData['id'];
        $this->data['cmsident'] = $oDocumentManagerTableConf->sqlData['cmsident'];
        $this->data['treeTableID'] = TTools::GetCMSTableId('cms_document_tree');
        $this->data['maxUploadSize'] = TTools::getUploadMaxSize();
        $this->data['CKEditorFuncNum'] = '';

        if ($this->global->UserDataExists('mode') && 'wysiwyg' == $this->global->GetUserData('mode')) {
            $this->data['wysiwygMode'] = true;
            $this->data['CKEditorFuncNum'] = $this->global->GetUserData('CKEditorFuncNum');
        }

        return $this->data;
    }

    /**
     * initialises the tree widget class.
     *
     * @return TCMSTreeWidget
     */
    protected function LoadTreeWidget()
    {
        /** @var $oTreeWidget TCMSTreeWidget */
        $oTreeWidget = new TCMSTreeWidget();
        $sPageDef = $this->global->GetUserData('pagedef');
        $oTreeWidget->Init($this->sTreeTable, $sPageDef, $this->sModuleSpotName);
        $oTreeWidget->SetContextMenuView('contextMenuAssets');
        $oTreeWidget->SetRootNodeName(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_manager.root_node_name'));

        return $oTreeWidget;
    }

    /**
     * loads table name for given table ID.
     */
    protected function GetTableName()
    {
        $tableID = $this->global->GetUserData('tableID');
        if (!empty($tableID)) {
            $tableQuery = "SELECT `name` FROM `cms_tbl_conf` WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($tableID)."'";
            $tableResult = MySqlLegacySupport::getInstance()->query($tableQuery);
            $tableRow = MySqlLegacySupport::getInstance()->fetch_assoc($tableResult);
            $this->data['tableName'] = $tableRow['name'];
        } else {
            $this->data['tableName'] = false;
        }

        return $this->data['tableName'];
    }

    public function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = ['MoveNode', 'RenameNode', 'GetChildren', 'DeleteNode', 'GetWYSIWYGDocumentHTML', 'PasteFiles', 'assignConnection', 'removeConnection', 'DeleteFile', 'CheckConnectionsAndDelete', 'CheckDirItemsConnectionsAndDelete'];
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    /**
     * generates HTML of documents for use in WYSIWYG Editor.
     *
     * @return string
     */
    public function GetWYSIWYGDocumentHTML()
    {
        $aReturnData = [];

        $aItems = [];
        if ($this->global->UserDataExists('idList')) {
            $sIdList = $this->global->GetUserData('idList');
            if (',' == substr($sIdList, -1)) {
                $sIdList = substr($sIdList, 0, -1);
            }
            $aIdList = explode(',', $sIdList);
            foreach ($aIdList as $sId) {
                /** @var $oDownload TCMSDownloadFile */
                $oDownload = TdbCmsDocument::GetNewInstance($sId);
                $aItems[] = $oDownload->GetWysiwygDownloadLink();
            }
        }
        $aReturnData['aItems'] = $aItems;
        $aReturnData['CKEditorFuncNum'] = $this->global->GetUserData('CKEditorFuncNum');

        return $aReturnData;
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/modules/DocumentManager.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<link href="'.TGlobal::GetPathTheme().'/css/table.css" rel="stylesheet" type="text/css" />'; // we need this for the list of connections by delete events

        $aIncludes[] = '<script type="text/javascript">
      var _url_user_cms_public = "'.URL_USER_CMS_PUBLIC.'";
      var _cmsurl = "'.TGlobal::OutJS(URL_CMS).'";
      var _url_cms = "'.TGlobal::OutHTML(URL_CMS).'";
      var selectedFiles = new Array(); // selected files of a list
      var assignedSelectedFiles = new Array(); // selected files of a list
      var _actualRowID = null;

      var messageNewFolder = \''.TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_manager.msg_new_folder')).'\';
      var messageFileDeleted = \''.TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_manager.msg_removed_documents')).'\';
      var messageNoFolderChoosen = \''.TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_manager.msg_missing_folder_name')).'\';
      var messageUploadSuccess = \''.TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_manager.msg_upload_success')).'\';
      var messageUploadError = \''.TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_manager.msg_upload_error')).'\';
      var messageUploadMore = \''.TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_manager.msg_upload_additional_files')).'\';
      var messageErrorNoUploadData = \''.TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_manager.msg_no_file_selected')).'\';
      var messageUploadNotAllowedHere = \''.TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_manager.msg_no_upload_to_primary_folder_allowed')).'\';
      var messageUploadButtonTitle = \''.TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_manager.msg_upload')).'\';
      var messageChooseFolderNow = \''.TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_manager.msg_select_target_folder')).'\';
      var messageErrorMoveNoFiles = \''.TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_manager.msg_no_files_selected_for_move')).'\';
      var messageErrorPasteNoFiles = \''.TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_manager.msg_no_files_selected_for_insert')).'\';
      var messageUploadSuccessSingle = \''.TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_manager.msg_upload_single_file_success')).'\';
      </script>';

        $aTreeIncludes = $this->oTreeWidget->GetHtmlHeadIncludes();
        $aIncludes = array_merge($aIncludes, $aTreeIncludes);

        return $aIncludes;
    }

    /**
     * paste marked files to new folder.
     */
    public function PasteFiles()
    {
        $fileList = $this->global->GetUserData('documents'); // comma separated
        $treeNodeID = $this->global->GetUserData('nodeID');
        $aFileList = explode(',', $fileList);

        if (count($aFileList) > 0 && !empty($treeNodeID)) {
            /** @var $oDocumentTableConf TCMSTableConf */
            $oDocumentTableConf = new TCMSTableConf();
            $oDocumentTableConf->LoadFromField('name', 'cms_document');

            foreach ($aFileList as $fileId) {
                $fileId = trim($fileId);
                if (!empty($fileId)) {
                    /** @var $oDocumentEditor TCMSTableEditorManager */
                    $oDocumentEditor = new TCMSTableEditorManager();
                    $oDocumentEditor->Init($oDocumentTableConf->id, $fileId);
                    if (!is_null($oDocumentEditor->oTableEditor->oTable) && false !== $oDocumentEditor->oTableEditor->oTable->sqlData) {
                        $oDocumentEditor->SaveField('cms_document_tree_id', $treeNodeID, false);
                    }
                }
            }
            $returnData = [];
            $returnData['nodeID'] = $treeNodeID;

            return $returnData;
        } else {
            return false;
        }
    }

    /**
     * assigns document files to data entry.
     */
    public function assignConnection()
    {
        $returnVal = false;
        if (!empty($this->tableID) && !empty($this->recordID) && $this->global->UserDataExists('documentIDs')) {
            $documentIDs = $this->global->GetUserData('documentIDs');
            $returnVal = [];
            $returnVal['documentIDs'] = $documentIDs;
            $returnVal['fieldName'] = $this->fieldName;

            $aDocumentIds = explode(',', $documentIDs);
            $html = '';

            // save base record
            $oTableEditor = new TCMSTableEditorManager();
            /* @var $oTableEditor TCMSTableEditorManager */
            $oTableEditor->Init($this->tableID, $this->recordID);

            foreach ($aDocumentIds as $documentID) {
                $oTableEditor->AddMLTConnection($this->fieldName, $documentID);
                $html .= $this->GetDocumentRowHTML($documentID);
            }

            $returnVal['html'] = $html;
        }

        return $returnVal;
    }

    /**
     * removes connections between document files and the given data entry.
     */
    public function removeConnection()
    {
        $returnVal = false;
        if (!empty($this->tableID) && !empty($this->recordID) && $this->global->UserDataExists('documentIDs')) {
            $documentIDs = $this->global->GetUserData('documentIDs');
            $returnVal = [];
            $returnVal['documentIDs'] = $documentIDs;
            $returnVal['fieldName'] = $this->fieldName;
            $aDocumentIds = explode(',', $documentIDs);

            // save base record
            $oTableEditor = new TCMSTableEditorManager();
            /* @var $oTableEditor TCMSTableEditorManager */
            $oTableEditor->Init($this->tableID, $this->recordID);

            foreach ($aDocumentIds as $documentID) {
                $oTableEditor->RemoveMLTConnection($this->fieldName, $documentID);
            }
        }

        return $returnVal;
    }

    /**
     * fetches and returns the HTML to show in the edit field.
     */
    protected function GetDocumentRowHTML($documentID)
    {
        $downloadDocument = new TCMSDownloadFile();
        $downloadDocument->Load($documentID);

        $translator = $this->getTranslator();

        $deleteButtonTemplate = '<button type="button" class="btn btn-danger btn-sm" onClick="if(confirm(\'%s\')){removeDocument(\'%s\',\'%s\',\'%s\',\'%s\')}">
                                    <i class="far fa-trash-alt mr-2"></i>%s
                                </button>';
        $deleteButton = sprintf($deleteButtonTemplate,
            TGlobal::OutJS($translator->trans('chameleon_system_core.field_download.confirm_removal', [], TranslationConstants::DOMAIN_BACKEND)),
            TGlobal::OutJS($this->fieldName),
            TGlobal::OutJS($documentID),
            TGlobal::OutJS($this->recordID),
            TGlobal::OutJS($this->tableID),
            TGlobal::OutHTML($translator->trans('chameleon_system_core.field_download.remove', [], TranslationConstants::DOMAIN_BACKEND))
        );

        $html = '
         <div id="documentManager_'.$this->fieldName.'_'.$documentID.'">
           <table class="table table-striped">
            <tr>
             <td>'.$downloadDocument->getDownloadHtmlTag().'</td>
             <td>
             '.$deleteButton.'
             </td>
           </tr>
          </table>
         </div>
          ';

        return $html;
    }

    protected function RenderFileConnectionInfo($aFoundConnections, $sFileId, $bInfoOnFileDelete = true)
    {
        $foundConnectionsHTML = '';
        if (is_array($aFoundConnections) && count($aFoundConnections) > 0) {
            $oFile = new TCMSDownloadFile();
            /* @var $oFile TCMSDownloadFile */
            $oFile->Load($sFileId);
            if ($bInfoOnFileDelete) {
                $sTitle = 'chameleon_system_core.document_manager.delete_document';
                $sNotice = '<div class="alert alert-warning">'.\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_manager.attention').'<br />'.\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_manager.action_delete_confirm_connections').'</div>';
            } else {
                $sTitle = 'chameleon_system_core.document_manager.document_id';
                $sNotice = '<div class="alert alert-info">'.\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_manager.attention').'<br />'.\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_manager.connection_list').'</div>';
            }
            $foundConnectionsHTML = '
        <div class="card">
            <div class="card-header">
                '.\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans($sTitle, [
                    '%id%' => $sFileId,
                ]).'
            </div>
            <div class="card-body">
                <div>
                  <span class="pr-2">'.$oFile->GetPlainFileTypeIcon().'</span>
                  <span class="">'.$oFile->GetName().'</span>
                </div>
                '.$sNotice.'
               <table class="table table-bordered">
                    <tr class="bg-primary">
                        <th>ID</th>
                        <th>'.\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_manager.connected_item_column_name').'</th>
                        <th>'.\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_manager.connected_item_column_table').'</th>
                        <th>'.\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_manager.connected_item_column_field').'</th>
                    </tr>';

            $count = 0;
            foreach ($aFoundConnections as $tableName => $aFields) {
                foreach ($aFields as $field => $value) {
                    /** @var $oTableRecordList TCMSRecordList */
                    $oTableRecordList = $value;
                    /** @var $oRecord TCMSRecord */
                    while ($oRecord = $oTableRecordList->Next()) {
                        if ($count % 2) {
                            $class = 'evenRowStyleNoHand';
                        } else {
                            $class = 'oddRowStyleNoHand';
                        }

                        $foundConnectionsHTML .= "<tr class=\"{$class}\">
                         <td>".$oRecord->id.'</td>
                         <td>'.$oRecord->GetName().'</td>
                         <td>'.$tableName.'</td>
                         <td>'.$field."</td>
                        </tr>\n";
                    }
                }
                ++$count;
            }
            $foundConnectionsHTML .= '
                </table>

                     ';
            if ($bInfoOnFileDelete) {
                $foundConnectionsHTML .= TCMSRender::DrawButton(\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_manager.confirm_delete_with_connections'), "javascript:SendDelete('".$sFileId."');", 'far fa-check-circle').'
                                      '.TCMSRender::DrawButton(\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_manager.abort_delete'), 'javascript:CloseDeleteCheckDialog();', 'far fa-times-circle');
            } else {
                $foundConnectionsHTML .= TCMSRender::DrawButton(\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_manager.close'), 'javascript:CloseModalIFrameDialog();', 'far fa-times-circle');
            }
            $foundConnectionsHTML .= '  </div>
                                       </div>';
        }

        return $foundConnectionsHTML;
    }

    /**
     * check all connections for file deletion
     * delete file if not connected.
     */
    public function CheckConnectionsAndDelete()
    {
        $returnVal = [];
        $bDoNotDelete = false;
        $aTableBlackList = null;
        if ($this->global->UserDataExists('documentIDs')) {
            $aFileIDs = explode(',', $this->global->GetUserData('documentIDs'));
            if ($this->global->UserDataExists('bDoNotDelete') && true == $this->global->GetUserData('bDoNotDelete')) {
                $bDoNotDelete = true;
                $aTableBlackList = ['pkg_custom_search', 'pkg_custom_search_result_item'];
            }
            $iTableID = TTools::GetCMSTableId('cms_document');
            $oTableEditor = new TCMSTableEditorManager();
            /* @var $oTableEditor TCMSTableEditorDocument */
            foreach ($aFileIDs as $fileID) {
                $oTableEditor->Init($iTableID, $fileID);
                $aFoundConnections = $oTableEditor->HandleExternalFunctionCall('FetchConnections', [$fileID, $aTableBlackList]);
                $sFileConnectionInfoHtml = $this->RenderFileConnectionInfo($aFoundConnections, $fileID, !$bDoNotDelete);
                if (!empty($sFileConnectionInfoHtml)) {
                    $returnVal[] = $sFileConnectionInfoHtml;
                } else {
                    if (!$bDoNotDelete) {
                        // no connections so delete file
                        $this->DeleteFile($fileID);
                    }
                }
            }
        }

        return $returnVal;
    }

    /**
     * delete file and all connections.
     *
     * @param string $fileID
     *
     * @return bool - returns true if file was deleted successfully
     */
    public function DeleteFile($fileID = null)
    {
        $returnVal = false;
        if (is_null($fileID)) { // so the method was called via ajax
            $fileID = $this->global->GetUserData('documentID');
        }

        if (!is_null($fileID) && !empty($fileID) && TTools::RecordExists('cms_document', 'id', $fileID)) {
            $iTableID = TTools::GetCMSTableId('cms_document');
            $oTableEditor = new TCMSTableEditorManager();
            /* @var $oTableEditor TCMSTableEditorDocument */
            $oTableEditor->Init($iTableID, $fileID);
            $oTableEditor->Delete($fileID);
            $returnVal = true;
        }

        return $returnVal;
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

    /**
     * check all connections for file deletion
     * delete file if not connected.
     */
    public function CheckDirItemsConnectionsAndDelete()
    {
        return $this->DeleteNode();
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslator()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
    }
}

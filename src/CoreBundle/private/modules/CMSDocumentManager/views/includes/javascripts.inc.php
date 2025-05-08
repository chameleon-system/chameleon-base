<script type="text/javascript">
    /**
     * add new jstree node
     *
     */
    function CreateNode(obj) {
        var id = obj.attr("id").replace("node", "");
        var url = '<?php echo PATH_CMS_CONTROLLER; ?>?tableid=<?php echo $data['treeTableID']; ?>&amp;pagedef=tableeditorPopup&amp;module_fnc%5Bcontentmodule%5D=Insert&amp;parent_id=' + id;
        CreateModalIFrameDialogCloseButton(url);
    }

    /*
     * show file details edit dialog
     *
     */
    function editFileDetails(id, row) {
        url = '<?php echo PATH_CMS_CONTROLLER; ?>?tableid=<?php echo $data['id']; ?>&pagedef=tableeditorPopup&id=' + id;
        CreateModalIFrameDialogCloseButton(url);
    }

    function RefreshOpenerHeader() {
    }

    function showFileList(nodeID) {
        randomKey = parseInt(Math.random() * (1000 - 1 + 1));
        let url = "<?php echo PATH_CMS_CONTROLLER; ?>?pagedef=tableManagerDocumentManager&id=<?php echo $data['id']; ?><?php if (!empty($data['recordID'])) {
            echo '&mltTable='.$data['mltTable'].'&recordID='.$data['recordID'];
        } ?>&_listName=cmstablelistObj<?php echo $data['cmsident']; ?>";
        if (nodeID && nodeID !== '1') {
            url += '&sRestrictionField=cms_document_tree_id&sRestriction=' + nodeID;
        }
        url += '&randkey=' + randomKey;

        document.getElementById('fileList').src = url;

        let nodeTreeNode = document.getElementById('node' + nodeID);
        let folderName = nodeTreeNode.querySelector('a')?.textContent?.trim();

        if (folderName) {
            document.getElementById('selectedFolderTitle').textContent = folderName;
        }
    }


    /*
     * save selected files to paste them to a directory
     *
     */
    function cutSelectedFiles() {
        if (selectedFiles.length > 0) {
            toasterMessage('<?php echo TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_manager.msg_select_target_folder')); ?>', 'WARNING');
        } else {
            toasterMessage('<?php echo TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_manager.msg_no_files_selected_for_move')); ?>', 'WARNING');
        }
    }

    /**
     * calls the document manager via ajax and moved selected files to the selected directory target
     *
     * @param string selectedNodeID
     */
    function PasteFiles(selectedNodeID) {
        if (selectedFiles.length != 0) {
            var filesCommaList = selectedFiles.join(",");
            var url = '<?php echo PATH_CMS_CONTROLLER; ?>?pagedef=CMSDocumentManager&module_fnc[contentmodule]=ExecuteAjaxCall&_fnc=PasteFiles&documents=' + filesCommaList + '&nodeID=' + selectedNodeID;
            GetAjaxCall(url, PasteFilesResponse);
        } else {
            toasterMessage('<?php echo TGlobal::OutJS(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.document_manager.msg_no_files_selected_for_insert')); ?>', 'WARNING');
        }
    }

    /**
     * resets the selectedFiles array
     *
     * @param object data
     * @param string responseMessage
     */
    function PasteFilesResponse(data, responseMessage) {
        CloseModalIFrameDialog();
        if (data) {
            // reset selection array
            selectedFiles = [];
            // reload file list
            showFileList(data.nodeID);
        }
    }

    function queueCompleteCallback() {
        //var uploadTitle = window.frames[1].document.getElementById('uploadname').value;
        var selectedTreeNodeID = $('#treePlacer .jstree-clicked').parent().attr("id").replace("node", "");
        showFileList(selectedTreeNodeID);
        CloseModalIFrameDialog();
    }

    /*
    * delete document and remove existing connections
    */
    function deleteSelectedItem() {

        if (selectedFiles.length != 0) {
            var url = "<?php echo PATH_CMS_CONTROLLER; ?>?pagedef=CMSDocumentManager&module_fnc[contentmodule]=ExecuteAjaxCall&_fnc=CheckConnectionsAndDelete&documentIDs=" + selectedFiles;
            GetAjaxCall(url, deleteSelectedItemResponse);
        }
        //refresh parent header
        RefreshOpenerHeader();
    }

    function deleteSelectedItemResponse(data, responseMessage) {
        CloseModalIFrameDialog();

        // reset file selection
        selectedFiles = new Array();

        if (data.length > 0) {
            // save connected data HTML to global variable
            _connectedDataHTML = data;
            ShowConnectionDialog(0);
        } else {
            DeleteFormCallback();
        }
    }

    function ShowConnectionDialog(index) {
        CreateModalIFrameDialogFromContent(_connectedDataHTML[index], 0, 0, CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.delete_document'));
    }

    function CloseDeleteCheckDialog() {
        CloseModalIFrameDialog();
        // check if more connection dialogs are in the pipeline
        _connectedDataCount++;

        if (_connectedDataHTML.length > _connectedDataCount) {
            ShowConnectionDialog(_connectedDataCount);
        } else {
            _connectedDataHTML = new Array();
            _connectedDataCount = 0;
            showFileList(treeTmpNodeID);
        }
    }
</script>

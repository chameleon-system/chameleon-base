<script type="text/javascript">
    /**
     * add new jstree node
     *
     */
    function CreateNode(obj) {
        var id = obj.attr("id").replace("node", "");
        var url = '<?=PATH_CMS_CONTROLLER; ?>?tableid=<?=$data['treeTableID']; ?>&amp;pagedef=tableeditorPopup&amp;module_fnc%5Bcontentmodule%5D=Insert&amp;parent_id=' + id;
        CreateModalIFrameDialogCloseButton(url);
    }

    /*
     * show file details edit dialog
     *
     */
    function editFileDetails(id, row) {
        url = '<?=PATH_CMS_CONTROLLER; ?>?tableid=<?=$data['id']; ?>&pagedef=tableeditorPopup&id=' + id;
        CreateModalIFrameDialogCloseButton(url);
    }

    function RefreshOpenerHeader() {
    }

    function showFileList(nodeID) {
        randomKey = parseInt(Math.random() * (1000 - 1 + 1));
        var url = "<?=PATH_CMS_CONTROLLER; ?>?pagedef=tableManagerDocumentManager&id=<?=$data['id']; ?><?php if (!empty($data['recordID'])) {
    echo '&mltTable='.$data['mltTable'].'&recordID='.$data['recordID'];
} ?>&_listName=cmstablelistObj<?=$data['cmsident']; ?>";
        if (nodeID && nodeID != '1') {
            url += '&sRestrictionField=cms_document_tree_id&sRestriction=' + nodeID;
        }
        url += '&randkey=' + randomKey;

        //refresh parent header

        document.getElementById('fileList').src = url;
    }

    /*
     * save selected files to paste them to a directory
     *
     */
    function cutSelectedFiles() {
        if (selectedFiles.length > 0) {
            toasterMessage('<?php echo TGlobal::OutJS(TGlobal::Translate('chameleon_system_core.cms_module_media_manager.msg_select_move_target')); ?>', 'WARNING');
        } else {
            toasterMessage('<?php echo TGlobal::OutJS(TGlobal::Translate('chameleon_system_core.cms_module_media_manager.error_no_move_source_selected')); ?>', 'WARNING');
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
            var url = '<?=PATH_CMS_CONTROLLER; ?>?pagedef=CMSDocumentManager&module_fnc[contentmodule]=ExecuteAjaxCall&_fnc=PasteFiles&documents=' + filesCommaList + '&nodeID=' + selectedNodeID;
            GetAjaxCall(url, PasteFilesResponse);
        } else {
            toasterMessage('<?php echo TGlobal::OutJS(TGlobal::Translate('chameleon_system_core.cms_module_media_manager.error_no_past_source_selected')); ?>', 'WARNING');
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
            var url = "<?=PATH_CMS_CONTROLLER; ?>?pagedef=CMSDocumentManager&module_fnc[contentmodule]=ExecuteAjaxCall&_fnc=CheckConnectionsAndDelete&documentIDs=" + selectedFiles;
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

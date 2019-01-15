<?php
/**
 * @deprecated since 6.2.0 - Chameleon has a new media manager
 **/
?>
<script type="text/javascript">

    /**
     * add new jstree node
     *
     */
    function CreateNode(obj) {
        var id = obj.attr("id").replace("node", "");
        var url = '<?=PATH_CMS_CONTROLLER; ?>?tableid=<?=$data['mediaTreeTableID']; ?>&amp;pagedef=tableeditorPopup&amp;module_fnc%5Bcontentmodule%5D=Insert&amp;parent_id=' + id;
        CreateModalIFrameDialogCloseButton(url);
    }

    /**
     * calls the media manager via ajax and moved selected files to the selected directory target
     *
     * @param string selectedNodeID
     */
    function PasteFiles(selectedNodeID) {
        if (selectedFiles.length != 0) {
            var filesCommaList = selectedFiles.join(",");
            var url = '<?=PATH_CMS_CONTROLLER; ?>?pagedef=CMSMediaManager&module_fnc[<?=$sModuleSpotName; ?>]=ExecuteAjaxCall&_fnc=PasteFiles&files=' + filesCommaList + '&nodeID=' + selectedNodeID;
            GetAjaxCall(url, PasteFilesResponse);
            showFileList(selectedNodeID);
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
        var uploadTitle = window.frames[1].document.getElementById('uploadname').value;
        var selectedTreeNodeID = $('#treePlacer .jstree-clicked').parent().attr("id").replace("node", "");
        showFileList(selectedTreeNodeID, uploadTitle);
        CloseModalIFrameDialog();
    }

    function RefreshOpenerHeader() {
    }

    function showFileList(nodeID, uploadTitle) {
        treeTmpNodeID = nodeID; // set actual tree node for later use global
        if (typeof nodeID == 'undefined' || nodeID == '1') { // root node should not restrict to the tree node id
            var url = "<?=$sListURL; ?>&_listName=cmstablelistObj<?=$data['cmsident']; ?>";
        } else {
            var url = "<?=$sListURL; ?>&sRestrictionField=cms_media_tree_id&sRestriction=" + nodeID + "&_listName=cmstablelistObj<?=$data['cmsident']; ?>";
            if (uploadTitle != '' && typeof uploadTitle != 'undefined') {
                url += '&_search_word=' + uploadTitle;
            }
        }
        randomKey = parseInt(Math.random() * (1000 - 1 + 1));
        url += '&randkey=' + randomKey;

        //refresh parent header
        document.getElementById('fileList').src = url;
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

    /*
    * delete image/video and check for existing connections first
    */
    function deleteSelectedItem() {
        if (selectedFiles.length != 0) {
            if (confirm('<?=TGlobal::Translate('chameleon_system_core.action.confirm_delete_selection'); ?>')) {
                var url = "<?=PATH_CMS_CONTROLLER; ?>?pagedef=CMSMediaManager&module_fnc[<?=$sModuleSpotName; ?>]=ExecuteAjaxCall&_fnc=CheckConnectionsAndDelete&fileIDs=" + selectedFiles + "&tableID=<?=$data['tableID']; ?>";
                GetAjaxCall(url, deleteSelectedItemResponse);
            }
        }
    }


    /*
    * show file details edit dialog
    *
    */
    function editFileDetails(id, row) {
        url = '<?=PATH_CMS_CONTROLLER; ?>?tableid=<?=$data['id']; ?>&pagedef=tableeditorPopup&id=' + id;
        CreateModalIFrameDialogCloseButton(url);
    }

</script>
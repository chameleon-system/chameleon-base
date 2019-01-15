var _connectedDataHTML = new Array(), // array of popup HTML with list of connected data entries
    _connectedDataCount = 0, // selected files of a list
    treeTmpNodeID, // make actual nodeID available global
    CKEditorFuncNum;

function setCKEditorFuncNum(newCKEditorFuncNum) {
    CKEditorFuncNum = newCKEditorFuncNum;
}

function SendDelete(fileID) {

    var url = window.location.pathname + "?pagedef=CMSDocumentManager&module_fnc[contentmodule]=ExecuteAjaxCall&_fnc=DeleteFile&documentID=" + fileID;
    GetAjaxCall(url, DeleteFormCallback);
}

function DeleteFormCallback() {
    toasterMessage(messageFileDeleted, 'SUCCESS');
    CloseDeleteCheckDialog();
}

function SendDeleteForm(fileID) {

    document.getElementById('documentID').value = fileID;
    document.getElementById('_fnc').value = 'DeleteFile';
    PostAjaxFormTransparent('ajaxForm', DeleteFormCallback);
}

/*
 * add and remove file IDs from selectedFiles Array
 */
function ChangeFileSelection(id) {
    var count = 0;
    var idFound = false;
    var newArray = new Array();

    // loop through already selected files and remove id if found
    for (count in selectedFiles) {
        if (selectedFiles[count] == id) {
            idFound = true;
        } else {
            newArray[count] = selectedFiles[count];
        }
    }
    selectedFiles = newArray;

    if (!idFound) { // no id was removed so we want to add it
        selectedFiles[selectedFiles.length] = id;
    }
}

/*
 * add and remove file IDs from selectedFiles Array
 */
function ChangeAssignedFileSelection(id) {
    var count = 0;
    var idFound = false;
    var newArray = new Array();

    // loop through already selected files and remove id if found
    for (count in assignedSelectedFiles) {
        if (assignedSelectedFiles[count] == id) {
            idFound = true;
        } else {
            newArray[count] = assignedSelectedFiles[count];
        }
    }
    assignedSelectedFiles = newArray;

    if (!idFound) { // no id was removed so we want to add it
        assignedSelectedFiles[assignedSelectedFiles.length] = id;
    }
}

function chooseSelectedFiles() {
    if (window.opener && window.opener.open && !window.opener.closed) {
        if (selectedFiles.length > 0) {
            CHAMELEON.CORE.showProcessingModal();
            var sSelectedFiles = selectedFiles.join(",");
            if (typeof window.opener.assignDocuments == 'function' || typeof window.opener.assignDocuments == 'object') {
                window.opener.assignDocuments(_fieldName, sSelectedFiles, _recordID, _tableID);
            }
            // reset array
            selectedFiles = new Array();
            CloseModalIFrameDialog();
        }
    }
}

function removeSelectedFiles() {
    if (window.opener && window.opener.open && !window.opener.closed) {
        if (assignedSelectedFiles.length > 0) {
            CHAMELEON.CORE.showProcessingModal();
            var sAssignedSelectedFiles = assignedSelectedFiles.join(",");
            if (typeof window.opener.removeDocument == 'function' || typeof window.opener.removeDocument == 'object') {
                window.opener.removeDocument(_fieldName, sAssignedSelectedFiles, _recordID, _tableID);
            }
            //refresh parent header
            RefreshOpenerHeader();

            assignedSelectedFiles = new Array();
            CloseModalIFrameDialog();
        }
    }
}

function reloadFilesList() {
    var selectedTreeNodeID = $("#treePlacer").jstree("get_selected").attr("id").replace("node", "");
    showFileList(selectedTreeNodeID);
}

function reloadSelectedFilesList() {
    var iFrameObj = document.getElementById('selectedFileList');
    if (iFrameObj && typeof(iFrameObj) == 'object') {
        iFrameObj.src = iFrameObj.src;
    }
}

function CleanDelete() {
    reloadFilesList();
    CloseModalIFrameDialog();
}
// ------------------------------------------------------------------------
/**
 * open upload dialog
 *
 */
function UploadFiles(selectedNodeID) {
    CreateModalIFrameDialogCloseButton(window.location.pathname + '?pagedef=CMSUniversalUploader&amp;mode=document&amp;queueCompleteCallback=queueCompleteCallback&amp;treeNodeID=' + selectedNodeID, 0, 0, 'Upload');

    //refresh parent header
    RefreshOpenerHeader();
}
// ------------------------------------------------------------------------

// ------------------------------------------------------------------------
/**
 * import documents from local FTP directory
 *
 */
function UploadFilesFromLocal(selectedNodeID) {
    CreateModalIFrameDialogCloseButton(window.location.pathname + '?pagedef=CMSDocumentLocalImport&amp;nodeID=' + selectedNodeID, 0, 0, 'FTP Import');

    //refresh parent header
    RefreshOpenerHeader();
}
// ------------------------------------------------------------------------


/**
 * insert the selected documents in the focused WYSIWYG text field
 */
function InsertDocumentsInWYSIWYG() {
    if (selectedFiles.length > 0) {
        // loop through already selected files and remove id if found
        var count = 0,
            idList = '';
        for (count in selectedFiles) {
            idList += selectedFiles[count] + ',';
        }

        // reset array
        selectedFiles = new Array();
        GetAjaxCall(window.location.pathname + "?pagedef=CMSDocumentManager&module_fnc[contentmodule]=ExecuteAjaxCall&_fnc=GetWYSIWYGDocumentHTML&idList=" + idList + "&CKEditorFuncNum=" + CKEditorFuncNum, InsertDocumentsInWYSIWYGFinal);
    }
}

/**
 * called after ajax success of InsertDocumentsInWYSIWYG()
 * inserts the documents HTML and closes the modal dialog
 *
 * @param object data
 * @param string statusText
 */
function InsertDocumentsInWYSIWYGFinal(data, statusText) {
    CloseModalIFrameDialog();
    if (data) {
        var activeEditorInstanceName = parent.opener.window.CHAMELEON.CORE.CKEditor.activeInstanceName,
            editorInstance = parent.opener.window.CKEDITOR.instances[activeEditorInstanceName],
            aItems = data['aItems'],
            placeholderTag = editorInstance.config.placeholderTag || '[[$CONTENT$]]';

        for (var i = 0; i < aItems.length; i++) {
            var text = placeholderTag.replace('$CONTENT$', aItems[i]);
            parent.opener.window.CKEDITOR.plugins.chameleon_download_placeholder.createPlaceholder(editorInstance, null, text);
        }

        window.close();
    }
}


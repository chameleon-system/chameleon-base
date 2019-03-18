/**
 * @deprecated since 6.2.0 - Chameleon has a new media manager
 */
var treeNodeEditDialog;
var treeNodeUploadDialog;
var fileDetailDialog;
var deleteCheckDialog;
var treeTmpNode; // make actual node object available global
var treeTmpNodeID; // make actual nodeID available global
var selectedFiles = new Array(); // selected files of a list
var _actualRowID = null;
var _connectedDataHTML = new Array(); // array of popup HTML with list of connected data entries
var _connectedDataCount = 0;      // selected files of a list


function SendDeleteForm(fileID) {
    document.getElementById('fileID').value = fileID;
    document.getElementById('_fnc').value = 'DeleteFile';
    PostAjaxFormTransparent('ajaxForm', DeleteFormCallback);
}


function DeleteFormCallback() {
    toasterMessage(messageFileDeleted, 'SUCCESS');
    CloseDeleteCheckDialog();
}

// ------------------------------------------------------------------------
/**
 * open upload dialog
 *
 */
function UploadFiles(selectedNodeID) {
    CreateModalIFrameDialogCloseButton(window.location.pathname + '?pagedef=CMSUniversalUploader&amp;mode=media&amp;treeNodeID=' + selectedNodeID + '&amp;queueCompleteCallback=queueCompleteCallback', 0, 0, CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.media_upload'));

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
    CreateModalIFrameDialogCloseButton(window.location.pathname + '?pagedef=CMSMediaLocalImport&amp;nodeID=' + selectedNodeID, 0, 0, 'FTP Import');

    //refresh parent header
    RefreshOpenerHeader();
}
// ------------------------------------------------------------------------

// ------------------------------------------------------------------------
/**
 * import documents from local FTP directory
 * @deprecated since 6.2.0 - Viddler is no longer supported.
 */
function UploadFilesToViddler(selectedNodeID) {

}
// ------------------------------------------------------------------------


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

function ShowConnectionDialog(index) {
    CreateModalIFrameDialogFromContent(_connectedDataHTML[index], 0, 0, CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.media_delete'));
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

/*
 * save selected files to paste them to a directory
 *
 */
function cutSelectedFiles() {
    if (selectedFiles.length > 0) {
        toasterMessage(messageChooseFolderNow, 'WARNING');
    } else {
        toasterMessage(messageErrorMoveNoFiles, 'WARNING');
    }
}

function replaceIt(string, searchString, replaceString) {
    returnVal = "" + string;
    while (returnVal.indexOf(searchString) > -1) {
        pos = returnVal.indexOf(searchString);
        returnVal = "" + (returnVal.substring(0, pos) + replaceString +
            returnVal.substring((pos + searchString.length), returnVal.length));
    }

    return returnVal;
}
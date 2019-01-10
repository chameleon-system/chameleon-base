if (typeof CHAMELEON === "undefined" || !CHAMELEON) {
    var CHAMELEON = {};
}
CHAMELEON.CORE = CHAMELEON.CORE || {};
CHAMELEON.CORE.MTTableEditor = CHAMELEON.CORE.MTTableEditor || {};
CHAMELEON.CORE.MTTableEditor.bCmsContentChanged = false;
var _currentFieldName = null;
var _currentPosition = null;
var _currentTableId = null;
var _currentRecordId = null;
var _imageSelectWindow = null;
var _documentManagerWindow = null;
var _mediaManagerWindow = null;

var _cmsurl = "";
var _trans_icon = '';
var _trans_link = '';
var _trans_password = '';
var _trans_drag = '';


/*
 * get file usages
 */
function GetUsages(tableId, type) {
    var sPageDef = '';
    var sModuleSpotName = '';
    var sType = '';
    if (type == null || type == 'media') { //@deprecated since 6.2.0 - we don't need this part anymore once the old media manager has been removed
        sPageDef = 'CMSMediaManager';
        sModuleSpotName = 'content';
        sType = 'fileIDs'
    }
    if (type == 'document') {
        sPageDef = 'CMSDocumentManager';
        sModuleSpotName = 'contentmodule';
        sType = 'documentIDs';
    }
    var url = window.location.pathname + "?pagedef=" + sPageDef + "&module_fnc[" + sModuleSpotName
        + "]=ExecuteAjaxCall&_fnc=CheckConnectionsAndDelete&bDoNotDelete=true&" + sType + "=" + tableId;
    GetAjaxCall(url, ShowUsagesResponse);
}

function ShowUsagesResponse(data, responseMessage) {
    CloseModalIFrameDialog();
    if (data.length > 0) {
        // save connected data HTML to global variable
        _connectedDataHTML = data;
        ShowUsageDialog(0, _connectedDataHTML);
    } else {
        toasterMessage(CHAMELEON.CORE.i18n.Translate('chameleon_system_core.table_editor.has_no_usages'), 'ERROR');
    }
}

function ShowUsageDialog(index, _connectedDataHTML) {
    CreateModalIFrameDialogFromContentWithoutClose(_connectedDataHTML[index], 700, 600, CHAMELEON.CORE.i18n.Translate('chameleon_system_core.table_editor.usages'));
}

function ExecutePostCommand(command) {
    document.cmseditform.elements['module_fnc[contentmodule]'].value = command;
    if (command == 'Save') {
        if ("undefined" != typeof CKEDITOR) {
            $.map(CKEDITOR.instances, function (instance, instanceName) {
                $("#" + instanceName).val(instance.getData());
            });
        }

        // remove "something changed" message, because now the data was saved
        window.onbeforeunload = function () {
        };
        // reattach the message binding
        SetChangedDataMessage();
    }
    CHAMELEON.CORE.showProcessingDialog();
    document.cmseditform.submit();
}

// use this to call "command" on the table editor object (make sure to add "command" to the list of allowed methods in the table editor via DefineInterface
function ExecutePostCommandOnTableEditorObject(command) {
    document.cmseditform.elements['_noModuleFunction'].value = 'true';
    ExecutePostCommand(command);
    document.cmseditform.elements['_noModuleFunction'].value = 'false';
}

function AddFormElementToEditForm(sName, sValue) {
    var sNewElement = '<input type="hidden" name="' + sName + '" value="' + sValue + '" />';
    $('#cmseditform').append(sNewElement);
}

function GoToRecordBySelectBox(tableID, fieldID) {
    var recordID = document.getElementById(fieldID).options[document.getElementById(fieldID).selectedIndex].value;
    if (recordID != '' && recordID != '0') {
        document.location.href = window.location.pathname + '?pagedef=tableeditor&id=' + recordID + '&tableid=' + tableID;
    }
}

function GoToRecordByHiddenId(tableID, fieldID) {
    GoToRecordByHiddenIdWithTarget(tableID, fieldID, 'document');
}

function GoToRecordByHiddenIdWithTarget(tableID, fieldID, target) {
    var recordID = document.getElementById(fieldID).value;
    if (recordID != '' && recordID != '0') {
        var sURL = window.location.pathname + '?pagedef=tableeditor&id=' + recordID + '&tableid=' + tableID;
        if (target == 'document') {
            document.location.href = sURL;
        } else if (target == 'parent') {
            parent.location.href = sURL;
        } else if (target == 'top') {
            top.location.href = sURL;
        }
    }
}

function SetImageResponse(data, responseMessage) {
    var _currentFieldName = getCMSRegistryEntry('_currentFieldName');
    var _currentPosition = getCMSRegistryEntry('_currentPosition');

    if (data) {
        $('#cmseditform #' + _currentFieldName).val(data.fieldvalue);
        var delButton = document.getElementById('cmsimagefielditem_clearbutton_' + _currentFieldName + _currentPosition);
        var noImageDiv = document.getElementById('cmsimagefielditem_noimagediv_' + _currentFieldName + _currentPosition);
        var imageDiv = document.getElementById('cmsimagefielditem_imagediv_' + _currentFieldName + _currentPosition);
        delButton.style.visibility = 'visible';
        imageDiv.innerHTML = data.sImage;
        imageDiv.style.display = 'block';
        noImageDiv.style.display = 'none';

        if (data.isFlashVideo) {
            InitVideoPlayer(data.uniqueID, data.FLVPlayerURL, data.maxThumbWidth, data.FLVPlayerHeight);
        } else {
            initLightBox();
        }
    }

    CloseModalIFrameDialog();
}

/*
 * sets the choosen image for the current slot
 */
function _SetImage(imageID, customCallback) {
    var _currentFieldName = getCMSRegistryEntry('_currentFieldName');
    var _currentPosition = getCMSRegistryEntry('_currentPosition');
    var form = document.getElementById('cmseditform');
    var _currentTableId = form.querySelector('[name=tableid]').value;
    var _currentRecordId = form.querySelector('[name=id]').value;
    var fieldValue = form.querySelector('[name=' + _currentFieldName + ']').value;

    var url = window.location.pathname + "?pagedef=CMSImageManager&module_fnc[module]=ExecuteAjaxCall&_fnc=SetImage&imageid=" + imageID + "&imagefieldname=" + _currentFieldName + "&tableid=" + _currentTableId + "&id=" + _currentRecordId + "&position=" + _currentPosition + "&imagefieldvalue=" + fieldValue;
    if (typeof customCallback === 'undefined') {
        customCallback = SetImageResponse;
    }
    GetAjaxCall(url, customCallback);
}

/*
 * reset choosen document in a TCMSFieldDocument
 */
function _ResetDocument(_currentFieldName, defaultText, defaultValue) {
    document.getElementById(_currentFieldName).value = defaultValue;
    var currentDivID = _currentFieldName + 'currentFile';
    document.getElementById(currentDivID).innerHTML = defaultText;
}

function _SetDocumentResponse(data, responseMessage) {
    if (data) {
        var _currentFieldName = getCMSRegistryEntry('_currentFieldName');
        var currentDivID = _currentFieldName + 'currentFile';
        if (data != '') {
            document.getElementById(currentDivID).innerHTML = data;
        }
    }
    CloseModalIFrameDialog();
}

/*
 * sets the choosen document in a TCMSFieldDocument
 */
function _SetDocument(documentID) {
    var _currentFieldName = getCMSRegistryEntry('_currentFieldName');
    var form = document.getElementById('cmseditform');
    form.querySelector('[name=' + _currentFieldName + ']').value = documentID;

    var url = window.location.pathname + "?pagedef=CMSUniversalUploader&module_fnc[contentmodule]=ExecuteAjaxCall&_fnc=GetDownloadHTML&documentID=" + documentID;
    GetAjaxCall(url, _SetDocumentResponse);
}

function ClearImageResponse(data, responseMessage) {
    if (data) {
        var form = document.getElementById('cmseditform');
        form.querySelector('[name=' + data.fieldname + ']').value = data.imageFieldContent;

        _ResetImage(data.fieldname, data.imagePosition);
        CloseModalIFrameDialog();
    }
}

/*
 * removes image connection
 */
function ClearImage(tableid, recordid, fieldname, imagePosition) {
    var form = document.getElementById('cmseditform');
    var fieldValue = form.querySelector('[name=' + fieldname + ']').value;

    var url = window.location.pathname + "?pagedef=CMSImageManager&module_fnc[module]=ExecuteAjaxCall&_fnc=ClearImage&imagefieldname=" + fieldname + "&tableid=" + tableid + "&id=" + recordid + "&position=" + imagePosition + "&imagefieldvalue=" + fieldValue;
    GetAjaxCall(url, ClearImageResponse);
}

/*
 * resets image connection box
 */
function _ResetImage(fieldname, position) {
    delButton = document.getElementById('cmsimagefielditem_clearbutton_' + fieldname + position);
    noImageDiv = document.getElementById('cmsimagefielditem_noimagediv_' + fieldname + position);
    imageDiv = document.getElementById('cmsimagefielditem_imagediv_' + fieldname + position);
    delButton.style.visibility = 'hidden';
    imageDiv.style.display = 'none';
    noImageDiv.style.display = 'block';
}

/*
 * changes color preview box of colorChooser
 */
function changeColorPreview(previewDivID, hex) {
    if (hex.length == 6) {
        document.getElementById(previewDivID).style.backgroundColor = '#' + hex;
    }
}

function loadHomeTreeNodeSelection(fieldName, id) {
    //var portalID = document.getElementById('main_node_tree').value;
    var portalID = document.cmseditform.id.value;

    if (portalID != '0' && portalID != '') {
        CreateModalIFrameDialogCloseButton(window.location.pathname + '?pagedef=treenodeselect&id=' + id + '&fieldName=' + fieldName + '&portalID=' + portalID, 400, 500);
    } else {
        toasterMessage(CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.error_portal_required'), 'WARNING');
    }
}

function removeDocumentResponse(data, responseMessage) {
    CloseModalIFrameDialog();
    if (data) {
        var aDocumentIDs = data.documentIDs.split(',');
        var count = aDocumentIDs.length;
        for (var i = 0; i < count; i++) {
            documentID = aDocumentIDs[i];
        }
        $('#documentManager_' + data.fieldName + '_' + documentID).remove();
        if (_documentManagerWindow && _documentManagerWindow != null) {
            _documentManagerWindow.reloadSelectedFilesList();
            _documentManagerWindow.reloadFilesList();
        }
    }
}

/*
 * document manager field: removes document connection
 */
function removeDocument(fieldName, documentID, recordID, tableID) {
    var url = window.location.pathname + "?pagedef=CMSDocumentManager&module_fnc[contentmodule]=ExecuteAjaxCall&_fnc=removeConnection&tableID=" + tableID + "&recordID=" + recordID + "&documentIDs=" + documentID + "&fieldName=" + fieldName;
    GetAjaxCall(url, removeDocumentResponse);
}

function assignDocumentResponse(data, responseMessage) {
    CloseModalIFrameDialog();
    if (data) {
        $('#documentManager_' + data.fieldName + '_anchor').append(data.html);
        if (_documentManagerWindow && _documentManagerWindow != null) {
            _documentManagerWindow.reloadSelectedFilesList();
            _documentManagerWindow.reloadFilesList();
        }
    }
}

/*
 * document manager field: adds document connection
 */
function assignDocuments(fieldName, documentIDs, recordID, tableID) {
    var url = window.location.pathname + "?pagedef=CMSDocumentManager&module_fnc[contentmodule]=ExecuteAjaxCall&_fnc=assignConnection&tableID=" + tableID + "&recordID=" + recordID + "&documentIDs=" + documentIDs + "&fieldName=" + fieldName;
    GetAjaxCall(url, assignDocumentResponse);
}

/*
 * document manager field: reload changed document properties
 */
function editDocument(fieldName, documentID, html) {
    if (document.getElementById('documentManager_' + fieldName + '_' + documentID)) {
        $('#documentManager_' + fieldName + '_' + documentID).remove();
        $('#documentManager_' + fieldName + '_anchor').append(html);
    }
}

/*
 * document manager field: opens document manager popup
 */
function loadDocumentManager(recordID, tableID, fieldName) {
    _documentManagerWindow = window.open(window.location.pathname + '?pagedef=CMSDocumentManager&recordID=' + recordID + '&tableID=' + tableID + '&fieldName=' + fieldName, '_blank', 'width=1000,height=700,resizable=yes,scrollbars=no');
}

function addMLTConnectionResponse(data, responseMessage) {
    if (data) {
        var iframe = data.fieldName + '_iframe';
        var iframeObj = document.getElementById(iframe);

        // reload the iframe that shows the already connected entries
        iframeObj.src = iframeObj.src;

        // reload the iframe that shows the entries the user can choose from
        var chooserIframeName = 'dialog_list_iframe';
        var chooserIframeObj = document.getElementById(chooserIframeName);
        if (chooserIframeObj) {
            chooserIframeObj.src = chooserIframeObj.src;
        }
        jQuery.unblockUI();
        return true;
    } else {
        CloseModalIFrameDialog();
        return false;
    }
}

/*
 * MLT field: adds MLT connection
 */
function addMLTConnection(sourceTable, fieldName, sourceID, targetID) {
    var mltTable = sourceTable + '_' + fieldName;
    var url = window.location.pathname + "?pagedef=CMSFieldMLTRPC&module_fnc[module]=ExecuteAjaxCall&_fnc=assignConnection&mltTable=" + mltTable + "&sourceID=" + sourceID + "&targetID=" + targetID + "&sourceTable=" + sourceTable + "&fieldName=" + fieldName;
    GetAjaxCall(url, addMLTConnectionResponse);
}

function removeMLTConnectionResponse(data, responseMessage) {
    if (data) {
        var iframe = data.fieldName + '_iframe';
        var iframeObj = document.getElementById(iframe);
        iframeObj.src = iframeObj.src + '&randkey=' + Math.floor(Math.random() * 100 + 1);
    }

    CloseModalIFrameDialog();
}


/*
 * MLT field: removes MLT connection
 */
function removeMLTConnection(sourceTable, fieldName, sourceID, targetID) {
    var mltTable = sourceTable + '_' + fieldName;
    var url = window.location.pathname + "?pagedef=CMSFieldMLTRPC&module_fnc[module]=ExecuteAjaxCall&_fnc=removeConnection&mltTable=" + mltTable + "&sourceID=" + sourceID + "&targetID=" + targetID + "&sourceTable=" + sourceTable + "&fieldName=" + fieldName;
    GetAjaxCall(url, removeMLTConnectionResponse);
    return true;
}

/*
 * media manager field: opens media manager popup
 */
function loadMediaManager(recordID, tableID, fieldName) {
    _mediaManagerWindow = window.open(window.location.pathname + '?pagedef=CMSMediaManager&recordID=' + recordID + '&tableID=' + tableID + '&fieldName=' + fieldName, '_blank', 'width=1000,height=700,resizable=yes,scrollbars=no');
}


/*
 * Position field: loads list of Positions
 */
function loadPositionList(tableID, tableSQLName, fieldName, recordID, sRestriction, sRestrictionField) {
    // if the restrictionfield is given, but the value is missing, watch out for the field
    if (sRestriction && sRestrictionField != '') {
        sRestriction = $('#' + sRestrictionField).val();
    }

    if (typeof (sRestriction) == 'undefined') sRestriction = '';

    var url = window.location.pathname + '?pagedef=CMSFieldPositionRPC&_rmhist=false&module_fnc[contentmodule]=GetSortElements';
    url += '&tableID=' + tableID;
    url += '&tableSQLName=' + tableSQLName;
    url += '&fieldName=' + fieldName;
    url += '&recordID=' + recordID;
    url += '&sRestriction=' + sRestriction;
    url += '&sRestrictionField=' + sRestrictionField;

    CreateModalIFrameDialogCloseButton(url, 500, 550, CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.change_position'));
}

/*
 * MLT field positions: loads list of Positions
 */
function loadMltPositionList(tableSQLName, sRestriction, sRestrictionField) {
    // if the restrictionfield is given, but the value is missing, watch out for the field
    var url = window.location.pathname + '?pagedef=CMSFieldMLTRPC&_rmhist=false&module_fnc[contentmodule]=GetSortElements';
    url += '&tableSQLName=' + tableSQLName;
    url += '&sRestriction=' + sRestriction;
    url += '&sRestrictionField=' + sRestrictionField;

    CreateModalIFrameDialogCloseButton(url, 500, 550, CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.change_position'));
}

/*
 * MLT field: show/hide MLT content
 */
function showMLTField(objID, outerObjID, url) {
    var mltID = document.getElementById(objID);
    top.currentMltId = mltID.id;

    var $objID = jQuery('#' + objID);
    if ($objID.is(':hidden')) {
        mltID.src = url;
        $objID.show();
    } else {
        $objID.hide();
    }
}

function setTableEditorListFieldState(triggerDiv, requestURL) {
    triggerDiv = $(triggerDiv);
    var state = 0;
    if (triggerDiv.data('fieldstate') != '1') {
        state = 1;
    }
    triggerDiv.data('fieldstate', state);
    GetAjaxCallTransparent(requestURL + '&state=' + state);
}

/*
 * extended list field: resets field value to default value
 */
function resetExtendedListField(fieldName, defaultValue, defaultPreview) {
    document.getElementById(fieldName).value = defaultValue;
    document.getElementById(fieldName + 'CurrentSelection').innerHTML = defaultPreview;
}

/*
 * extended multi table list field: resets field value to default value
 */
function resetExtendedMultiTableListField(fieldName, defaultValue, defaultPreview) {
    document.getElementById(fieldName).value = defaultValue;
    document.getElementById(fieldName + '_table_name').value = '';
    document.getElementById(fieldName + 'CurrentSelection').innerHTML = defaultPreview;
}

/*
 * tableEditor: method to lock a record
 */
function RefreshRecordEditLock() {
    if ($('#cmseditform').length > 0) {
        document.cmseditform.elements['module_fnc[contentmodule]'].value = 'ExecuteAjaxCall';
        document.cmseditform._fnc.value = 'RefreshLock';

        PostAjaxFormTransparent('cmseditform', CheckRefreshReturn);
    }
}

function CheckRefreshReturn(data) {
    if (typeof (data) == 'object' && data instanceof Array && data.length == 2) {
        if (data[0] == 'logedoutajax') {
            document.location.href = data[1]
        }
    }
    window.setTimeout("RefreshRecordEditLock()", 30000);
}

function PublishViaAjaxCallback(data, statusText) {
    CloseModalIFrameDialog();

    if (data != false && data != null) {
        if (data.error) {
            top.toasterMessage('Fehler: ' + data.error, 'ERROR');
        } else {
            if (data.message && data.message != '') {
                top.toasterMessage(data.message, 'MESSAGE');
            } else {
                top.toasterMessage(CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.msg_published'), 'MESSAGE');
                setTimeout('ReloadMainPage()', 2000);

            }
            if (data.name && data.name != '' && document.getElementById('breadcrumbLastNode')) document.getElementById('breadcrumbLastNode').innerHTML = data.name;
        }
    } else {
        top.toasterMessage(CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.error_publish'), 'ERROR');
    }
}

function ReloadMainPage() {
    window.parent.location.href = window.location.pathname + '?pagedef=main';
}

/*
 * tableEditor: save edit table via ajax
 */
function SaveViaAjaxCustomCallback(customCallbackFunction, closeAfterSave) {
    if (customCallbackFunction == 'undefined') customCallbackFunction = SaveViaAjaxCallback;

    document.cmseditform.elements['module_fnc[contentmodule]'].value = 'ExecuteAjaxCall';
    document.cmseditform._fnc.value = 'AjaxSave';

    PostAjaxForm('cmseditform', eval(customCallbackFunction));
    if (closeAfterSave == true && typeof parent != 'undefined') parent.setTimeout("parent.CloseModalIFrameDialog()", 3000);
}

/*
 * tableEditor: save table single field via ajax
 */
function SaveFieldViaAjaxCustomCallback(customCallbackFunction) {
    if (customCallbackFunction == 'undefined') {
        customCallbackFunction = SaveViaAjaxCallback;
    }

    document.cmseditform.elements['module_fnc[contentmodule]'].value = 'ExecuteAjaxCall';
    document.cmseditform._fnc.value = 'AjaxSaveField';

    PostAjaxForm('cmseditform', eval(customCallbackFunction));
}


/*
 * tableEditor: save edit table via ajax
 */

function SaveViaAjax() {

    if ("undefined" != typeof CKEDITOR) {
        $.map(CKEDITOR.instances, function (instance, instanceName) {
            $("#" + instanceName).val(instance.getData());
        });
    }

    if (typeof (framestosave) != 'undefined' && framestosave.length > 0) {
        $.map(framestosave, function (frameToSave, index) {
            $('.itemsave:first', $('#' + frameToSave).contents()).trigger('click')
        });
    }
    SaveViaAjaxCustomCallback(SaveViaAjaxCallback);
}

function SaveViaAjaxCallback(data, statusText) {
    CloseModalIFrameDialog();

    // remove all message-classes from the fields
    $('*[id^="fieldname_"]').removeClass();

    var returnVal = false;
    if (data != false && data != null) {

        returnVal = true;

        if (data.aMessages) {
            var bOnLoadResetted = false;
            $.map(data.aMessages, function (oMessage, index) {
                toasterMessage(oMessage.sMessage, oMessage.sMessageType);

                // messages do have a reference to field (e.g. mail-field not valid)
                if (oMessage.sMessageRefersToField) {
                    // add the class to field
                    $('#fieldname_' + oMessage.sMessageRefersToField).addClass('fieldMsg ' + oMessage.sMessageType);
                }

                if (oMessage.sMessageType != 'ERROR' && !bOnLoadResetted) {
                    // remove "something changed" message, because now the data was saved
                    bOnLoadResetted = true;
                    window.onbeforeunload = function () {
                    };
                    // reattach the message binding
                    SetChangedDataMessage();
                }


            });
        } else {
            // remove "something changed" message, because now the data was saved
            window.onbeforeunload = function () {
            };
            // reattach the message binding
            SetChangedDataMessage();
            if (data.message && data.message != '') {
                toasterMessage(data.message, 'MESSAGE');
            }

            // add saved record to breadcrumb
            if (data.name && data.name != '' && document.getElementById('breadcrumbLastNode')) {
                document.getElementById('breadcrumbLastNode').innerHTML = data.name;
                sCurrentRecordName = data.name;
            }
        }
    } else {
        toasterMessage(CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.error_save'), 'ERROR');
    }

    return returnVal;
}

function Save() {
    CHAMELEON.CORE.showProcessingDialog();
    document.cmseditform.elements['module_fnc[contentmodule]'].value = 'Save';
    document.cmseditform.submit();
}

function ShowFieldEditWindow(type, data, evt) {
    var fieldObj = document.getElementById('fieldcontainer_' + data.field);
    fieldObj.innerHTML = data.content;
}

function ShowAjaxSaveResult(data, statusText) {
    CloseModalIFrameDialog();
    if (data.success == true) {
        toasterMessage(CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.msg_save_success'), 'MESSAGE');
        var contentContainer = parent.document ? parent.document.getElementById(data.fieldname + '_contentdiv') : null;
        if (contentContainer) {
            contentContainer.innerHTML = data.contentFormatted;
        }
        var reponse = jQuery(data.contentFormatted);
        var reponseScript = reponse.filter("script");
        parent.jQuery.each(reponseScript, function (idx, val) {
            parent.eval(val.text);
        });
        window.dispatchEvent(new Event("savesuccess"));
    } else {
        toasterMessage(CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.error_save'), 'ERROR');
        setTimeout(function () {
            window.dispatchEvent(new Event("saveerror"));
        }, 1000);
    }
}

function ShowAjaxSaveResultAndClose(data, statusText) {
    CloseModalIFrameDialog();
    ShowAjaxSaveResult(data, statusText);
    if (data.success == true) {
        window.setTimeout("parent.CloseModalIFrameDialog()", 1000);
    }
}

function ResetTreeNodeSelection(fieldName) {
    document.getElementById(fieldName).value = '';
    document.getElementById(fieldName + '_path').innerHTML = '<div class="treeField"><ul><li>' + CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.nothing_assigned') + '</li></ul></div>';
}

function markCheckboxes(fieldname) {
    var registryID = fieldname + '_checked_status';

    if (getCMSRegistryEntry(registryID) == false) {
        saveCMSRegistryEntry(registryID, 'true');
        var status = true;
    } else {
        var registryStatus = getCMSRegistryEntry(registryID);
        if (registryStatus == 'true') {
            var status = false;
            saveCMSRegistryEntry(registryID, 'false');
        } else {
            var status = true;
            saveCMSRegistryEntry(registryID, 'true');
        }
    }

    var elements = document.forms['cmseditform'].elements[fieldname + "[]"];
    for (i = 0; i < elements.length; i++) {
        var element = elements[i];
        if (false === element.disabled) {
            element.checked = status;
        }
    }
}

function invertCheckboxes(fieldname) {
    var elements = document.forms['cmseditform'].elements[fieldname + "[]"];
    for (i = 0; i < elements.length; i++) {
        var element = elements[i];
        if (false === element.disabled) {
            element.checked = false === element.checked;
        }
    }
}

function OpenPreviewURL(url, statusText) {
    var ghost = document["createElement"]("a");
    ghost["href"] = "about:blank";
    ghost["target"] = "PopHelper";
    document["getElementsByTagName"]("body")[0]["appendChild"](ghost);
    ghost["parentNode"]["removeChild"](ghost);
    var clk = document["createEvent"]("MouseEvents");
    clk["initMouseEvent"]("click", true, true, window, 0, 0, 0, 0, 0, true, false, false, true, 0, null);
    ghost["dispatchEvent"](clk);
    var previewWindow = window["open"](url, "PopHelper");
    previewWindow["focus"]();
}

function openLayoutManager(id) {
    GetAjaxCall(window.location.pathname + '?pagedef=templateengine&id=' + id + '&module_fnc%5Btemplateengine%5D=ExecuteAjaxCall&_fnc=IsMainNavigationSet', openLayoutManagerResponse);
}

function openLayoutManagerResponse(data, statusText) {
    CloseModalIFrameDialog();
    if (data.bMainNavigationIsSet == true) document.location.href = window.location.pathname + '?pagedef=templateengine&_mode=layout_selection&id=' + data.sPageId;
    else toasterMessage(data.sToasterErrorMessage, 'ERROR');
}

/** needed for backwards compatibility */
function DeleteRecord() {
    CHAMELEON.CORE.MTTableEditor.DeleteRecordWithCustomConfirmMessage(CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.confirm_delete'));
}

CHAMELEON.CORE.MTTableEditor.DeleteRecordWithCustomConfirmMessage = function (sConfirmText) {
    if (sCurrentRecordName != '') {
        sConfirmText += "\n\n " + CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.record') + ": \"" + sCurrentRecordName + '"';
    } else {
        sConfirmText += "\n\n " + CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.record') + ": \"" + CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.unnamed') + '"';
    }
    if (confirm(sConfirmText)) {
        window.onbeforeunload = function () {
        };
        CHAMELEON.CORE.showProcessingDialog();
        document.cmseditform.elements['module_fnc[contentmodule]'].value = 'Delete';
        document.cmseditform.submit();
    }
};

$(document).ready(function () {
    CHAMELEON.CORE.MTTableEditor.initTabs();
    CHAMELEON.CORE.MTTableEditor.inputFields();
});


CHAMELEON.CORE.MTTableEditor.initTabs = function () {
    var url = document.URL;
    var hash = window.location.hash.substr(0);

    $('.nav-tabs').find('li a').each(function (key, tabLinkItem) {
        var $tabLinkItem = $(tabLinkItem);
        if (hash === tabLinkItem.getAttribute('href')) {
            $tabLinkItem.click();
        }
        $tabLinkItem.click(function () {
            window.location.hash = this.getAttribute('href');
        });
    });
};

function updateIframeSize(sFieldName, iHeight) {
    if (sFieldName != '') {
        $('#' + sFieldName + '_iframe').height(iHeight + 'px');
    }
}

CHAMELEON.CORE.MTTableEditor.inputFields = function () {
    $('[data-datetimepicker-option]').each(function () {
        $(this).datetimepicker($(this).data("datetimepicker-option"));
    });

    $('.datetimepicker-input').each(function () {
        var id = $(this).attr('id');

        // This custom-event of the datetimepicker only works with the id of the element.
        $('#' + id).on("change.datetimepicker", function (e) {
            var moment = e.date;

            if (moment !== undefined) {
                if ($(this).hasClass('format-L')) {
                    var cmsDate = moment.format('YYYY-MM-DD');
                } else {
                    var cmsDate = moment.format('YYYY-MM-DD HH:mm:ss');
                }
                // We need a sql-date-format for BC reasons.
                $('input[name=' + id + ']').val(cmsDate);
            }
        });
    });

    $('[data-select2-option]').each(function () {
        $(this).select2($(this).data("select2-option"));
    });

    $('.lookup-container-field-types select').on('select2:select', function (e) {
        var data = e.params.data;
        var fieldName = $(this).attr('name');
        var fieldID = '#fieldTypeHelp' + data.id;
        var helpText = $(fieldID).html();

        if (helpText == '') {
            $("#" + fieldName + "-helpContainer").html("&nbsp;");
        } else {
            $("#" + fieldName + "-helpContainer").html(helpText);
        }
    });
};

CHAMELEON.CORE.MTTableEditor.addCheckBoxSwitchClickEvent = function (selector) {
    $(selector).bind('click', function () {
        var checkBoxFieldElement = $(this);
        var hiddenFieldName = checkBoxFieldElement.attr('name') + 'hidden';
        var hiddenFieldElement = $('#' + hiddenFieldName);

        if (checkBoxFieldElement.is(':checked')) {
            hiddenFieldElement.attr('disabled', true);
        } else {
            hiddenFieldElement.removeAttr('disabled');
        }
    });
};

$(document).ready(function () {
    $('.noChangesWarning').click(function () {
        window.onbeforeunload = function () {
        };
    });

    CHAMELEON.CORE.MTTableEditor.addCheckBoxSwitchClickEvent('label.switch input[type=checkbox]');
});
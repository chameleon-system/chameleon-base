if (typeof CHAMELEON === "undefined" || !CHAMELEON) {
    var CHAMELEON = {};
}
CHAMELEON.CORE = CHAMELEON.CORE || {};

var sLastDialogID = null;

CHAMELEON.CORE.showProcessingModal = function () {
    var processingDialogContainer = $("#processingDialog");

    if (!(processingDialogContainer.data('bs.modal') || {})._isShown){
        processingDialogContainer.modal('show');
    }
};

CHAMELEON.CORE.hideProcessingModal = function () {
    // $("#processingDialog").dialog('hide'); is not working, so this is the fix.
    var processingDialogContainer = $("#processingDialog");
    processingDialogContainer.removeClass("in");
    $(".modal-backdrop").remove();
    $('body').removeClass('modal-open');
    processingDialogContainer.hide();
};

/**
 * @deprecated since 6.3.0 - call CHAMELEON.CORE.showProcessingDialog(); instead
 */
function PleaseWait() {
    CHAMELEON.CORE.showProcessingDialog();
}

function PostAjaxForm(formid, functionName) {
    CHAMELEON.CORE.showProcessingDialog();
    PostAjaxFormTransparent(formid, functionName);
}

function PostAjaxFormTransparent(formid, callbackFunction) {
    var options = {
        success:callbackFunction,
        error:AjaxError,
        url:window.location.pathname,
        dataType:"json",
        type:'POST'
    };

    $("#" + formid).ajaxSubmit(options);
    return false;
}

function GetAjaxCall(url, functionName) {
    CHAMELEON.CORE.showProcessingDialog();
    GetAjaxCallTransparent(url, functionName);
}

function CMSAddGlobalParametersToURL(sURL) {
    // do not add token, if token exists
    if (sURL.indexOf(_cmsauthenticitytoken_parameter) < 0) {
        var sSep = '?';
        if (sURL.indexOf(sSep) > -1) sSep = '&';
        sURL = sURL + sSep + _cmsauthenticitytoken_parameter;
    }
    return sURL;
}

function GetAjaxCallTransparent(url, functionName) {
    url = CMSAddGlobalParametersToURL(url);

    $.ajax({
        url:url,
        processData:false,
        dataType:'json',
        success:functionName,
        error:AjaxError,
        type:'GET'
    });
}

function AjaxError(XMLHttpRequest, textStatus, errorThrown) {
    if (textStatus === 'parsererror') {
        window.parent.CHAMELEON.CORE.hideProcessingDialog();
        CHAMELEON.CORE.hideProcessingDialog();
        toasterMessage('Error! Wasn`t able to parse ajax response.', 'ERROR');
        if (XMLHttpRequest.responseText !== '') {
            var sError = XMLHttpRequest.responseText;

            if (sError.length > 1024) {
                sError = sError.substr(0, 1024);
            }

            if (sError.indexOf('<title>') !== -1) sError = '';

            var sMessage = CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.ajax_error', sError);

            // check if response is the login page, so we need to redirect the user
            if (XMLHttpRequest.responseText.indexOf('<input type="hidden" name="pagedef" value="login" />') !== -1) {
                var sLogoutMessage = CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.permission_error_with_logout');
                if (confirm(sLogoutMessage)) {
                    top.document.location.href = window.location.pathname;
                }
            } else {
                alert(sMessage);
            }
        }
    }
}

var stack_bottomright = {"dir1": "up", "dir2": "left", "push": "top", "firstpos1": 25, "firstpos2": 15};

/*
 * we provide a nice Message Box instead of bothering operating system window
 * allowed style types: MESSAGE (default), WARNING, ERROR, FATAL
 */
function toasterMessage(message,type) {
    CHAMELEON.CORE.hideProcessingDialog();
    var sStylingClass = '';
    if(type == 'ERROR' || type == 'FATAL') {
        type = 'error';
        var title = CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.error');
    } else if(type == 'WARNING') {
        type = 'notice';
        var title = CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.warning');
    } else if(type == 'MESSAGE' || type == 'SUCCESS') {
        type = 'success';
        var title = CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.notice');
        var sStylingClass = 'successNotify';
    }

    new PNotify({
        title: title,
        type: type,
        opacity: 1,
        insert_brs: true,
        text: message,
        animate_speed: "normal",
        styling: 'bootstrap3',
        hide: true,
        addclass: "stack-bottomright",
        delay: 6000,
        stack: stack_bottomright
    });
}

window.alert = function(message) {
    new PNotify({
        title: "Alert",
        text: message
    });
};

function CmsAjaxCallback_OpenLink(sResponse, statusText) {
    CloseModalIFrameDialog();
    window.open(sResponse);
}
function DisplayAjaxMessage(data, statusText) {
    CloseModalIFrameDialog();
    toasterMessage(data, 'MESSAGE');
}

function DisplayAjaxTextarea(data, statusText) {
    CloseModalIFrameDialog();
    var content = "<form accept-charset=\"UTF-8\"><textarea wrap=\"off\" style=\"width:100%;height:550px;\">" + data + "</textarea></form>";
    CreateModalIFrameDialogFromContent(content, 750, 650);
}

/*
 * save variable in CMS registry
 */
function saveCMSRegistryEntry(id, value) {
    registryID = '_CMSRegistry_' + id;
    if (document.getElementById(registryID)) {
        document.getElementById(registryID).innerHTML = value;
    } else {
        var newdiv = document.createElement('div');

        newdiv.setAttribute('id', registryID);
        newdiv.style.display = 'none';
        newdiv.innerHTML = value;

        document.body.appendChild(newdiv);
    }
}

/*
 * get variable from CMS registry
 */
function getCMSRegistryEntry(id) {
    registryID = '_CMSRegistry_' + id;
    if (document.getElementById(registryID)) {
        return document.getElementById(registryID).innerHTML;
    } else {
        return false;
    }
}

/*
 * delete variable from CMS registry
 */
function deleteCMSRegistryEntry(id) {
    registryID = '_CMSRegistry_' + id;
    if (document.getElementById(registryID)) {
        document.body.removeChild(registryID);
    }
}

/*
 * create dialog
 */
function LoadJQMDialog(width, height, dialogContent, hasCloseButton, title, isDraggable, isResizable, isModal) {
    if (typeof(isModal) == "undefined") {
        if (hasCloseButton) {
            isModal = false;
        } else {
            isModal = true;
        }
    }
    if (!title) title = 'Chameleon CMS';
    if (typeof(isDraggable) == 'undefined') isDraggable = true;
    if (typeof(isResizable) == 'undefined') isResizable = true;

    if ($('#modal_dialog').length == 0) {
        $('body').append('<div id="modal_dialog" style="display: none;"></div>');
    }

    $('#modal_dialog').dialog({
        width:parseInt(width) + 15,
        height:parseInt(height),
        title:title,
        modal:isModal,
        position:"center",
        resizable:isResizable,
        draggable:isDraggable,
        close:function (event, ui) {
            CloseModalIFrameDialog();
        },
        open:function (event, ui) {
            if (!hasCloseButton) {
                $(event.currentTarget).find('.ui-dialog-titlebar-close').css('display', 'none');
            }
        }
    }).html(dialogContent);

    if (isModal) {
        $('.ui-widget-overlay').click(function () {
            CloseModalIFrameDialog();
        });
    }

    return true;
}

/*
 * creates a ModalDialog without close button from iFrame
 */
function CreateModalIFrameDialog(url, width, height, title, isDraggable, isResizable) {
    url = CMSAddGlobalParametersToURL(url);
    var dialogContent = '<iframe id="dialog_list_iframe" src="' + url + '" width="100%" height="99.5%" border="0" frameborder="0"></iframe>';
    LoadJQMDialog(width, height, dialogContent, false, title, isDraggable, isResizable, true);
}

/*
 * creates a ModalDialog with close button from iFrame
 */
function CreateModalIFrameDialogCloseButton(url, width, height, title, isDraggable, isResizable) {
    url = CMSAddGlobalParametersToURL(url);
    var dialogContent = '<iframe id="dialog_list_iframe" src="' + url + '" width="100%" height="99.5%" border="0" frameborder="0"></iframe>';
    LoadJQMDialog(width, height, dialogContent, true, title, isDraggable, isResizable, true);
}

/*
 * creates a ModalDialog with close button from content string
 */
function CreateModalIFrameDialogFromContent(content, width, height, title, isDraggable, isResizable) {
    LoadJQMDialog(width, height, content, true, title, isDraggable, isResizable, true);
}

/*
 * creates a ModalDialog with close button from DIV-Container (ID)
 */
function CreateModalDialogFromContainer(contentID, width, height, title, isDraggable, isResizable) {
    var content = $('#' + contentID).html();
    $('#' + contentID).html('');
    top.sLastDialogID = contentID;
    var dialogContent = '<div style="width:100%;height:100%;" id="modal_dialog_content">' + content + '</div>';
    LoadJQMDialog(width, height, dialogContent, true, title, isDraggable, isResizable, true);
}

/*
 * creates a ModalDialog to show a full image from Image-URL
 */
function CreateMediaZoomDialogFromImageURL(imageURL, width, height) {
    dialogContent = '<a href="javascript:void();" onclick="CloseModalIFrameDialog();return false;"><img src="' + imageURL + '" width="' + width + '" height="' + height + '" border="0" style="cursor:hand;cursor:pointer;" /></a>';
    width = (parseInt(width) + 30);
    height = (parseInt(height) + 65);
    LoadJQMDialog(width, height, dialogContent, true, false, false, true, true);
    $(".ui-dialog-titlebar").hide();
}

/*
 * creates a ModalDialog without close button from content string
 */
function CreateModalIFrameDialogFromContentWithoutClose(content, width, height, title, isDraggable, isResizable) {
    LoadJQMDialog(width, height, content, false, title, isDraggable, isResizable, true);
}

/*
 * closes Modal Dialog
 */
function CloseModalIFrameDialog() {
    $('#modal_dialog').html('&nbsp;');
    var isDialog = $('#modal_dialog').is(':ui-dialog');
    if(isDialog) {
        if ($('#modal_dialog').dialog('isOpen')) {
            $('#modal_dialog').dialog('destroy');
        }
    }
    CHAMELEON.CORE.hideProcessingDialog();
}

function getRadioValue(rObj) {
    var returnVal = false;
    for (var i = 0; i < rObj.length; i++) {
        if (rObj[i].checked) returnVal = rObj[i].value;
    }
    return returnVal;
}

/**
 * @deprecated since 6.2.0 - Flash support will be removed in Chameleon 7.0.
 */
function InitVideoPlayer(playerID, FLVPlayerURL, maxThumbWidth, playerHeight) {
    $('#flashContainer' + playerID).flash(
        { src:FLVPlayerURL,
            id:playerID,
            name:playerID,
            align:'middle',
            wmode:'window',
            quality:'high',
            allowScriptAccess:'always',
            allowfullscreen:'true',
            width:maxThumbWidth,
            height:playerHeight },
        { version:9 }
    );
}

function addslashes(str) {
    // Escapes single quote, double quotes and backslash characters in a string with backslashes
    //
    // version: 1004.1212
    // discuss at: http://phpjs.org/functions/addslashes    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Ates Goral (http://magnetiq.com)
    // +   improved by: marrtins
    // +   improved by: Nate
    // +   improved by: Onno Marsman    // +   input by: Denny Wardhana
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Oskar Larsson Högfeldt (http://oskar-lh.name/)
    // *     example 1: addslashes("kevin's birthday");
    // *     returns 1: 'kevin\'s birthday'
    return (str + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
}


/**
 * switches the edit portal which will used as default portal while editing
 */
function SwitchEditPortal() {
    PostAjaxFormTransparent('portalChooserForm', SwitchEditPortalCallback);
}

function SwitchEditPortalCallback() {
    $('#portalChooser').slideToggle('fast');
    $('#portalContentBoxNameSpan').html(document.getElementById('activePortalID').options[document.getElementById('activePortalID').options.selectedIndex].text);
}

function GetSynchronouslyAjaxCall(url, functionName) {
    GetSynchronouslyAjaxCallTransparent(url, functionName);
}

function GetSynchronouslyAjaxCallTransparent(url, functionName) {
    url = CMSAddGlobalParametersToURL(url);

    $.ajax({
        url: url,
        processData: false,
        dataType:  'json',
        success: functionName,
        error: AjaxError,
        type: 'GET',
        async: false
    });
}

function OpenModule(url, width, height) {
    window.open(url,'stats','scrollbars=yes,width='+width+',height='+height);
}

/*
 * open standalone document manager popup
 */
function loadStandaloneDocumentManager() {
    _standAloneDocumentManagerWindow = window.open('/cms?pagedef=CMSDocumentManager','documentManager','width=1200,height=800,resizable=yes,scrollbars=no');
}

$(document).ready(function () {
    initLightBox();
});

function initLightBox(){
    $('.lightbox').click(function (event) {
        event.preventDefault();
        height = $(this).attr("data-height");
        width = $(this).attr("data-width");
        url = $(this).attr("href");
        CreateMediaZoomDialogFromImageURL(url,width,height);
        event.cancelBubble=true;
        return false;
    });
}


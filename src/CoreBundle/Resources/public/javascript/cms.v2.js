if (typeof CHAMELEON === "undefined" || !CHAMELEON) {
    var CHAMELEON = {};
}
CHAMELEON.CORE = CHAMELEON.CORE || {};

var sLastDialogID = null;
var coreuiProcessingModal = null;

CHAMELEON.CORE.showProcessingModal = function () {
    var processingDialogContainer = document.querySelector("#processingModal");
    if (processingDialogContainer) {
        if (coreuiProcessingModal === null) {
            coreuiProcessingModal = new coreui.Modal(processingDialogContainer, {focus: true});
        }
        coreuiProcessingModal.show();
    }
};

CHAMELEON.CORE.hideProcessingModal = function () {
    var processingDialogContainer = document.querySelector("#processingModal");
    if (processingDialogContainer) {
        coreuiProcessingModal?.hide();
    }
};

function PostAjaxForm(formid, functionName) {
    CHAMELEON.CORE.showProcessingModal();
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
    CHAMELEON.CORE.showProcessingModal();
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
    window.parent.CHAMELEON.CORE.hideProcessingModal();
    CHAMELEON.CORE.hideProcessingModal();

    var errorMessage = "Error";

    if (XMLHttpRequest.responseText !== "" || XMLHttpRequest.statusText !== "") {
        var responseError = XMLHttpRequest.responseText;

        if (responseError.length > 1024) {
            responseError = responseError.substr(0, 1024);
        }

        if (responseError.indexOf('<title>') !== -1) {
            responseError = XMLHttpRequest.statusText;
        }

        errorMessage = CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.ajax_error', responseError);
    }

    if (textStatus === 'parsererror') {
        toasterMessage(CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.ajax_parse_error'), 'ERROR');

        if (XMLHttpRequest.responseText !== '') {
            // check if response is the login page, so we need to redirect the user
            if (XMLHttpRequest.responseText.indexOf('<input type="hidden" name="pagedef" value="login" />') !== -1) {
                var sLogoutMessage = CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.permission_error_with_logout');
                if (confirm(sLogoutMessage)) {
                    top.document.location.href = window.location.pathname;
                }
            } else {
                toasterMessage(errorMessage, "ERROR");
            }
        }
    } else {
        toasterMessage(errorMessage, "ERROR");
    }
}

var stack_bottomright = {"dir1": "up", "dir2": "left", "push": "top", "firstpos1": 25, "firstpos2": 15};

/*
 * we provide a nice Message Box instead of bothering operating system window
 * allowed style types: MESSAGE (default), WARNING, ERROR, FATAL
 */
function toasterMessage(message,type) {
    CHAMELEON.CORE.hideProcessingModal();
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
        animate_speed: 'normal',
        styling: 'bootstrap3',
        icons: 'fontawesome5',
        hide: true,
        addclass: 'stack-bottomright',
        delay: 6000,
        stack: stack_bottomright
    });
}

window.alert = function(message) {
    new PNotify({
        title: "Alert",
        text: message,
        text_escape: true,
        type: 'error',
        opacity: 1,
        styling: 'bootstrap3',
        icons: 'fontawesome5'
    });
};

function CmsAjaxCallback_OpenLink(sResponse, statusText) {
    CloseModalIFrameDialog();
    window.open(sResponse);
}
function DisplayAjaxMessage(data, statusText) {
    CloseModalIFrameDialog();

    if (data.messageType) {
        toasterMessage(data.message, data.messageType);
    } else {
        toasterMessage(data, 'MESSAGE');
    }
}

function DisplayAjaxTextarea(data, statusText) {
    CloseModalIFrameDialog();
    var content = "<form accept-charset=\"UTF-8\"><textarea wrap=\"off\" style=\"width:100%;height:550px;\">" + data + "</textarea></form>";
    CreateModalIFrameDialogFromContent(content);
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
    return CHAMELEON.CORE.showModal(title, dialogContent, CHAMELEON.CORE.getModalSizeClassByPixel(width));
}

CHAMELEON.CORE.getModalSizeClassByPixel = function (width) {

    if (window !== window.parent) {  //Modal is opened in an iFrame
        return 'modal-xxl';
    }

    if (typeof width === 'undefined') {
        return 'modal-xl';
    }

    width = parseInt(width,10);

    if (0 === width) {
        return 'modal-xl';
    }

    if (width > 1140) {
        return 'modal-xl';
    } else if (width > 800) {
        return 'modal-lg';
    } else if (width > 500) {
        return ''; //default
    }

    return 'modal-sm';
};

CHAMELEON.CORE.showModal = function (title, content, sizeClass, height) {

    if (typeof sizeClass === 'undefined') {
        sizeClass = 'modal-xl';
    }

    var modalDialog = document.getElementById('modalDialog');
    var modalBody;

    if (null === modalDialog) {
        var newModal = document.createElement('div');
        newModal.id = 'modalDialog';
        newModal.className = 'modal fade';
        newModal.setAttribute('tabindex', '-1');
        newModal.setAttribute('role', 'dialog');
        newModal.setAttribute('aria-labelledby', 'modalDialogLabel');
        newModal.setAttribute('aria-hidden', 'true');
        newModal.innerHTML = '<div class="modal-dialog modal-dialog-centered ' + sizeClass + '">\n' +
            '                     <div class="modal-content">      ' +
            '                         <div class="modal-header" id="modalHeader">\n' +
            '                             <h5 class="modal-title" id="modalDialogLabel"></h5>\n' +
            '                             <button type="button" class="close" data-coreui-dismiss="modal" aria-label="Close">\n' +
            '                                 <span aria-hidden="true">&times;</span>\n' +
            '                             </button>\n' +
            '                         </div>' +
            '                         <div class="modal-body">\n' +
            '                         </div>\n' +
            '                     </div>\n' +
            '                 </div>';
        document.body.appendChild(newModal);
        modalDialog = document.getElementById('modalDialog');
        modalBody = modalDialog.querySelector('.modal-body');
    } else {
        // set dialog size
        var modalDialogInner = document.querySelectorAll('#modalDialog .modal-dialog')[0];
        modalDialogInner.classList.remove('modal-xxl', 'modal-xl', 'modal-lg', 'modal-md');
        if (sizeClass !== '') {
            modalDialogInner.classList.add(sizeClass);
        }

        // reset content
        modalBody = document.querySelectorAll('#modalDialog .modal-body')[0];
        modalBody.innerHTML = '';
    }

    // set title
    var modaldialogLabel = document.getElementById('modalDialogLabel');

    if (title) {
        modaldialogLabel.innerHTML = title;
    } else {
        modaldialogLabel.innerHTML = '';
    }

    if (typeof height === 'undefined' || height < 300) {
        height = window.innerHeight-150;
    }

    modalBody.style.height = height+'px';

    // init/reset modal
    modalCoreui = new coreui.Modal(modalDialog, {
        focus: true
    })

    // set content after dialog is initialized
    modalDialog.addEventListener('shown.coreui.modal', function () {
        var modalBody = this.querySelector('.modal-body');
        modalBody.innerHTML = content;
        var iframe = modalBody.querySelector('iframe');
        if (iframe) {
            var iframeSrc = iframe.getAttribute('src');
            if (iframeSrc !== null) {
                iframeSrc += (iframeSrc.includes('?') ? '&' : '?') + 'isInModal=1';
                iframe.setAttribute('src', iframeSrc);
            }
        }
    });

    modalCoreui.show();
    modalCoreui.handleUpdate();
};

/*
 * creates a ModalDialog without close button from iFrame
 */
function CreateModalIFrameDialog(url, width, height, title, isDraggable, isResizable) {
    if (typeof height === 'undefined' || height < 300) {
        height = window.innerHeight - 150;
    }
    url = CMSAddGlobalParametersToURL(url);
    var dialogContent = '<iframe id="dialog_list_iframe" src="' + url + '" width="99%" height="100%" frameborder="0" data-modal-body-height='+height+'></iframe>';
    CHAMELEON.CORE.showModal(title, dialogContent, CHAMELEON.CORE.getModalSizeClassByPixel(width), height);
}

/*
 * creates a ModalDialog with close button from iFrame
 */
function CreateModalIFrameDialogCloseButton(url, width, height, title, isDraggable, isResizable) {
    if (typeof height === 'undefined' || height < 300) {
        height = window.innerHeight-150;
    }
    url = CMSAddGlobalParametersToURL(url);
    var dialogContent = '<iframe id="dialog_list_iframe" src="' + url + '" width="100%" height="100%" frameborder="0" data-modal-body-height='+height+'></iframe>';
    CHAMELEON.CORE.showModal(title, dialogContent, CHAMELEON.CORE.getModalSizeClassByPixel(width), height);
}

/*
 * creates a ModalDialog with close button from content string
 */
function CreateModalIFrameDialogFromContent(content, width, height, title, isDraggable, isResizable) {
    CHAMELEON.CORE.showModal(title, content, CHAMELEON.CORE.getModalSizeClassByPixel(width), height);
}

/**
 * Creates a ModalDialog with close button from DIV container (by ID).
 */
function CreateModalDialogFromContainer(contentID, width, height, title, isDraggable, isResizable) {
    const contentElem = document.getElementById(contentID);

    if (!contentElem) {
        console.warn(`CreateModalDialogFromContainer: Element with ID '${contentID}' not found.`);
        return;
    }

    const content = contentElem.innerHTML;
    contentElem.innerHTML = '';

    if (typeof top !== 'undefined') {
        top.sLastDialogID = contentID;
    }

    const dialogContent = `
        <div style="width:100%;height:100%;" id="modal_dialog_content">
            ${content}
        </div>
    `;

    CHAMELEON.CORE.showModal(
        title,
        dialogContent,
        CHAMELEON.CORE.getModalSizeClassByPixel(width),
        height
    );
}


/*
 * creates a ModalDialog to show a full image from Image-URL
 */
function CreateMediaZoomDialogFromImageURL(imageURL, width, height) {
    dialogContent = '<a href="javascript:void();" onclick="CloseModalIFrameDialog();return false;" style="display: flex; justify-content: center"><img src="' + imageURL + '" width="' + width + '" height="' + height + '" border="0" style="cursor:hand;cursor:pointer;" /></a>';
    width = (parseInt(width) + 30);
    height = (parseInt(height) + 65);
    CHAMELEON.CORE.showModal('Image', dialogContent, CHAMELEON.CORE.getModalSizeClassByPixel(width), height);
}

/*
 * closes Modal Dialog
 */
function CloseModalIFrameDialog(parentIFrame = '') {
    $('#modalDialog .modal-body').html('&nbsp;');
    $('#modalDialog').modal('hide');
    if (parentIFrame) {
        var parentIFrameElement = parent.document.getElementById(parentIFrame);
        if (parentIFrameElement) {
            parentIFrameElement.contentWindow.CHAMELEON.CORE.hideProcessingModal();
        }
    } else {
        CHAMELEON.CORE.hideProcessingModal();
    }
}

function getRadioValue(rObj) {
    var returnVal = false;
    for (var i = 0; i < rObj.length; i++) {
        if (rObj[i].checked) returnVal = rObj[i].value;
    }
    return returnVal;
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
 *
 * @deprecated since 6.3.0 - use URL directly
 */
function loadStandaloneDocumentManager() {
    window.location.href = '/cms?pagedef=CMSDocumentManagerFull';
}

$(document).ready(function () {
    initLightBox();

    // init tooltips
    //@ToDo: update tooltip to new coreui
    $('[data-toggle="tooltip"], [rel="tooltip"]').tooltip({container: 'body', trigger: 'hover'});

    const popoverTriggerList = document.querySelectorAll('[data-coreui-toggle="popover"]')
    const popoverList = [...popoverTriggerList].map(popoverTriggerEl => {
        return new coreui.Popover(popoverTriggerEl, {
            html: true
        });
    });


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

CHAMELEON.CORE.handleFormAndLinkTargetsInModals = function () {
    if (self === top) {
        return;
    }

    if (false === $('#modalDialog', top.document).hasClass('show')) {
        return;
    }

    $("form[target='_top']").each(function() {
        $(this).attr('target', '');
        $(this).find("input[name='pagedef'][value='tableeditor']").each(function() {
            $(this).val('tableeditorPopup');
        });

        $(this).find("input[name='pagedef'][value='tablemanager']").each(function() {
            $(this).val('tablemanagerframe');
        });
    });

    $("a[target='_top']").each(function() {
        $(this).attr('target', '');

        var link = $(this).attr('href').replace('pagedef=tableeditor', 'pagedef=tableeditorPopup');
        $(this).attr('href', link);
    });
};

CHAMELEON.CORE.initializeEntryAutocomplete = function($element) {
    $element.typeahead({
        source: function (query, process) {
            return $.get($element.data('source-url'), {'term': query}, function (data) {
                return process(data);
            });
        },
        afterSelect: function(item) {
            if (typeof item.id !== "undefined") {
                var handled = false;
                if ("" !== item.id) {
                    if (undefined !== $element.data('onclick-function') && "" !== $element.data('onclick-function')) {
                        handled = true;
                        var specificOnClick = $element.data('onclick-function') + "('" + item.id + "')";
                        eval(specificOnClick);
                    }
                }

                if (false === handled && "" !== $element.data('record-url')) {
                    top.document.location.href = $element.data('record-url') + '&id=' + item.id;
                }
            }
        },
        autoSelect: false,
        minLength: 0,
        showHintOnFocus: true,
        items: $(window).height() > 600 ? 16 : 8,
        menu: '<ul class="typeahead dropdown-menu dropdown-menu-right" role="listbox"></ul>',
    });

    $element.on("keydown", function(event) {
        if (event.key !== "Enter") {
            return;
        }

        // Make sure typeahead does not prevent a form submit (if mouse is not over input field).

        $element.closest("form").submit();
    });

    $element.on("keyup", function(event) {
        if (event.key !== "Enter") {
            return;
        }

        if (0 === $element.closest("form").length) {
            // If there is no form: redo the hiding of Typeahead's "select(undefined)".

            $element.typeahead("show");
        }
    });
};

CHAMELEON.CORE.copyToClipboard = function(text) {
    if (window.getSelection && "" !== window.getSelection().toString()) {
        return;
    }

    const $temp = $("<input>");
    $("body").append($temp);
    $temp.val(text).select();
    document.execCommand("copy");
    $temp.remove();
};

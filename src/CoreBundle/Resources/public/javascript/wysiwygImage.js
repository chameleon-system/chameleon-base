var CKEditorFuncNum;

function setCKEditorFuncNum(newCKEditorFuncNum) {
    CKEditorFuncNum = newCKEditorFuncNum;
}

function selectImageResponse(data, responseMessage) {
    CloseModalIFrameDialog();
    if (data) {
        parent.opener.window.CKEDITOR.tools.callFunction(data['CKEditorFuncNum'], data['image_url'], function () {
            // Get the reference to a dialog window.
            var element,
                dialog = this.getDialog();

            // Get the reference to a text field that holds the "alt" attribute.
            element = dialog.getContentElement('info', 'txtAlt');
            if (element) {
                element.setValue(data['image'].aData['alt_tag']);
            }

            element = dialog.getContentElement('advanced', 'txtGenTitle');
            if (element) {
                if (data['image'].aData['cmscaption'] == undefined || data['image'].aData['cmscaption']) {
                    element.setValue(data['image'].aData['description']);
                } else {
                    element.setValue(data['image'].aData['cmscaption']);
                }
            }

            element = dialog.getContentElement('info', 'cmsMediaId');
            if (element) {
                element.setValue(data['image'].aData['id']);
            }

            window.close();
        });
    }
}

/*
 * selects the actual image
 */
function selectImage(id) {
    var url = window.location.pathname + "?pagedef=CMSwysiwygRPC&module_fnc[module]=ExecuteAjaxCall&_fnc=GetMediaProperties&mediaID=" + id + "&CKEditorFuncNum=" + CKEditorFuncNum;
    GetAjaxCall(url, selectImageResponse);
}


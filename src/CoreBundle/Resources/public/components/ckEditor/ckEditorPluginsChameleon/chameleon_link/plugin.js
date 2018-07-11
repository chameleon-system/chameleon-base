/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

/**
 * @fileOverview The "chameleon_document_link" plugin.
 *
 */

(function () {
    CKEDITOR.plugins.add('chameleon_link', {
        lang: 'en,de'
    });
    CKEDITOR.on('dialogDefinition', function (ev)
    {
        var dialogName = ev.data.name;
        if (dialogName == 'link') {
            CKEDITOR.plugins.chameleon_link.setupLinkDialog(ev);
        }
    });
})();

CKEDITOR.plugins.chameleon_link = {
    setupLinkDialog:function (ev) {
        var dialogDefinition = ev.data.definition,
            dialog = dialogDefinition.dialog;
        dialog.on('show', function () {
            var editor = ev.editor;
            editor.plugins.chameleon_link.allowSuggestions = true;
            if (editor.getSelection().getSelectedText() != '') {
                editor.plugins.chameleon_link.allowSuggestions = false;
            }
        });
    }
};
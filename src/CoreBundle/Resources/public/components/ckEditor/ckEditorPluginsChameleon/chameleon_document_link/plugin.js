/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

/**
 * @fileOverview The "chameleon_document_link" plugin.
 *
 */

(function () {
    CKEDITOR.plugins.add('chameleon_document_link', {
        requires:'placeholder',
        lang:'en,de',
        icons:'chameleon_document_link',
        init:function (editor) {
            var lang = editor.lang.chameleon_document_link;
            editor.addCommand('create_chameleon_document_link', {
                exec: function(editor) {
                    CKEDITOR.plugins.chameleon_document_link.createChameleonDocumentLink(editor);
                }
            });

            editor.ui.addButton && editor.ui.addButton('chameleon_document_link', {
                label:lang.toolbar,
                command:'create_chameleon_document_link',
                toolbar:'insert,5',
                icon:this.path + 'icons/chameleon_document_link.gif'
            });
        }
    });
})();

CKEDITOR.plugins.chameleon_document_link = {
    createChameleonDocumentLink:function (editor) {
        var url = '?pagedef=CMSDocumentManager&mode=wysiwyg',
            height = 800,
            width = 1200;
        CHAMELEON.CORE.CKEditor.setActiveInstanceName(editor.name);
        editor.popup(url, width, height);
    }
};

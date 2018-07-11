/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

/**
 * @fileOverview The "chameleon_flash" plugin.
 *
 */

(function () {
    CKEDITOR.plugins.add('chameleon_flash');
    CKEDITOR.on('dialogDefinition', function (ev) {
        // Take the dialog name and its definition from the event data.
        var dialogName = ev.data.name;

        if (dialogName == 'flash') {
            CKEDITOR.plugins.chameleon_flash.setupFlashDialog(ev);
        }
    });
})();

CKEDITOR.plugins.chameleon_flash = {
    setupFlashDialog:function (ev) {
        var dialogDefinition = ev.data.definition;
        CKEDITOR.plugins.chameleon_flash.setupScaleField(dialogDefinition);
    },
    setupScaleField:function (dialogDefinition) {
        var propertiesTab = dialogDefinition.getContents('properties'),
            scaleField = propertiesTab.get('scale');
        scaleField['default'] = 'showall';
    }
};
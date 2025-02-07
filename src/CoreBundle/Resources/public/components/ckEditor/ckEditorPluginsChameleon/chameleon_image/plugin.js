/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

/**
 * @fileOverview The "chameleon_image" plugin.
 *
 */

(function () {
    CKEDITOR.plugins.add('chameleon_image', {
        lang:'en,de',
        init: function(editor) {
            editor.addContentsCss(this.path + 'styles/editor.css');
        }
    });
    CKEDITOR.on('dialogDefinition', function (ev) {
        // Take the dialog name and its definition from the event data.
        var dialogName = ev.data.name;

        if (dialogName == 'image') {
            CKEDITOR.plugins.chameleon_image.setupImageDialog(ev);
        }
    });
})();

CKEDITOR.plugins.chameleon_image = {
    setupImageDialog:function (ev) {
        var dialogDefinition = ev.data.definition;

        this.setupShowFullField(dialogDefinition, ev.editor);
        this.setupShowCaptionField(dialogDefinition, ev.editor);
        this.setupCaptionField(dialogDefinition);
        this.setupTitleTextField(dialogDefinition);

        this.setupWidthField(dialogDefinition);
        this.setupHeightField(dialogDefinition);
        this.setupAlignField(dialogDefinition);
        this.setupClassField(dialogDefinition);

        this.setupCmsMediaIdField(dialogDefinition);

        /**
         * removes the "margin-top-bottom" and "margin-left-right" field from the core plugin
         */
        this.removeHSpaceAndVSpace(dialogDefinition);

        /**
         * add the new margin fields (top, bottom, left, right)
         */
        this.addMarginFields(dialogDefinition, ev.editor);
    },
    _marginValues: [
        ['0'],['1'],['2'],['3'],['4'],['5'],['6'],['7'],['8'],['9'],['10'],
        ['11'],['12'],['13'],['14'],['15'],['16'],['17'],['18'],['19'],['20']
    ],
    IMAGE: 1,
    LINK: 2,
    PREVIEW: 4,
    CLEANUP: 8,
    regexGetSize: /^\s*(\d+)((px)|\%)?\s*$/i,
    regexGetSizeOrEmpty: /(^\s*(\d+)((px)|\%)?\s*$)|^$/i,
    pxLengthRegex: /^\d+px$/,
    _incommit: false,
    _commitInternally: function( targetFields ) {

        if ( this._incommit ){
            return;
        }

        this._incommit = 1;

        var dialog = this.getDialog(),
            element = dialog.imageElement;
        if ( element ) {
            // Commit this field and broadcast to target fields.
            this.commit( this.IMAGE, element );

            targetFields = [].concat( targetFields );
            var length = targetFields.length,
                field;
            for ( var i = 0; i < length; i++ ) {
                field = dialog.getContentElement.apply( dialog, targetFields[ i ].split( ':' ) );
                // May cause recursion.
                field && field.setup( this.IMAGE, element );
            }
        }

        this._incommit = 0;
    },
    _updatePreview: function( dialog ) {
        //Don't load before onShow.
        if ( !dialog.originalElement || !dialog.preview ){
            return 1;
        }

        // Read attributes and update imagePreview;
        dialog.commitContent( this.PREVIEW, dialog.preview );
        return 0;
    },
    _marginFieldsSetup: function(type, element, marginType, self){
        if ( type == CKEDITOR.plugins.chameleon_image.IMAGE ) {

            var marginPx,
                marginStyle = element.getStyle( 'margin-'+marginType);

            marginStyle = marginStyle && marginStyle.match( CKEDITOR.plugins.chameleon_image.pxLengthRegex );
            marginPx = parseInt( marginStyle, 10 );

            if(isNaN(marginPx)){
                marginPx = 0;
            }

            self.setValue( marginPx );
        }
    },
    _marginFieldsCommit: function(type, element, internalCommit, marginType, self){

        var value = parseInt( self.getValue(), 10 );

        if ( type == CKEDITOR.plugins.chameleon_image.IMAGE || type == CKEDITOR.plugins.chameleon_image.PREVIEW ) {

            if ( !isNaN( value ) ) {
                element.setStyle( 'margin-'+marginType, CKEDITOR.tools.cssLength( value ) );
            } else if ( !value && self.isChanged() ) {
                element.removeStyle( 'margin-'+marginType );
            }

            if ( !internalCommit && type == CKEDITOR.plugins.chameleon_image.IMAGE ){
                //element.removeAttribute( 'hspace' );
            }
        } else if ( type == CKEDITOR.plugins.chameleon_image.CLEANUP ) {
            element.removeStyle( 'margin-'+marginType );
        }
    },
    addMarginFields: function(dialogDefinition, editor){

        var infoTab = dialogDefinition.getContents('info');

        infoTab.add({
            type: 'hbox',
            width: ['25%', '25%', '25%', '25%'],
            children: [
                {
                    type: 'select',
                    items: CKEDITOR.plugins.chameleon_image._marginValues,
                    'default': '0',
                    id: 'marginTop',
                    width: '60px',
                    label: 'Abstand oben',
                    onChange: function() {
                        CKEDITOR.plugins.chameleon_image._updatePreview( this.getDialog() );
                        CKEDITOR.plugins.chameleon_image._commitInternally.call( this, 'advanced:txtdlgGenStyle' );
                    },
                    validate: CKEDITOR.dialog.validate.integer( 'margin-top muss eine Ganzzahl sein' ),
                    setup: function( type, element ) {
                        CKEDITOR.plugins.chameleon_image._marginFieldsSetup(type, element, 'top', this);
                    },
                    commit: function( type, element, internalCommit ) {
                        CKEDITOR.plugins.chameleon_image._marginFieldsCommit(type, element, internalCommit, 'top', this);
                    }
                },{
                    type: 'select',
                    items: CKEDITOR.plugins.chameleon_image._marginValues,
                    'default': '0',
                    id: 'marginRight',
                    width: '60px',
                    label: 'Abstand rechts',
                    onChange: function() {
                        CKEDITOR.plugins.chameleon_image._updatePreview( this.getDialog() );
                        CKEDITOR.plugins.chameleon_image._commitInternally.call( this, 'advanced:txtdlgGenStyle' );
                    },
                    validate: CKEDITOR.dialog.validate.integer( 'margin-right muss eine Ganzzahl sein' ),
                    setup: function( type, element ) {
                        CKEDITOR.plugins.chameleon_image._marginFieldsSetup(type, element, 'right', this);
                    },
                    commit: function( type, element, internalCommit ) {
                        CKEDITOR.plugins.chameleon_image._marginFieldsCommit(type, element, internalCommit, 'right', this);
                    }
                },{
                    type: 'select',
                    items: CKEDITOR.plugins.chameleon_image._marginValues,
                    'default': '0',
                    id: 'marginBottom',
                    width: '60px',
                    label: 'Abstand unten',
                    onChange: function() {
                        CKEDITOR.plugins.chameleon_image._updatePreview( this.getDialog() );
                        CKEDITOR.plugins.chameleon_image._commitInternally.call( this, 'advanced:txtdlgGenStyle' );
                    },
                    validate: CKEDITOR.dialog.validate.integer( 'margin-bottom muss eine Ganzzahl sein' ),
                    setup: function( type, element ) {
                        CKEDITOR.plugins.chameleon_image._marginFieldsSetup(type, element, 'bottom', this);
                    },
                    commit: function( type, element, internalCommit ) {
                        CKEDITOR.plugins.chameleon_image._marginFieldsCommit(type, element, internalCommit, 'bottom', this);
                    }
                },{
                    type: 'select',
                    items: CKEDITOR.plugins.chameleon_image._marginValues,
                    'default': '0',
                    id: 'marginLeft',
                    width: '60px',
                    label: 'Abstand links',
                    onChange: function() {
                        CKEDITOR.plugins.chameleon_image._updatePreview( this.getDialog() );
                        CKEDITOR.plugins.chameleon_image._commitInternally.call( this, 'advanced:txtdlgGenStyle' );
                    },
                    validate: CKEDITOR.dialog.validate.integer( 'margin-left muss eine Ganzzahl sein' ),
                    setup: function( type, element ) {
                        CKEDITOR.plugins.chameleon_image._marginFieldsSetup(type, element, 'left', this);
                    },
                    commit: function( type, element, internalCommit ) {
                        CKEDITOR.plugins.chameleon_image._marginFieldsCommit(type, element, internalCommit, 'left', this);
                    }
                }
            ]
        });

        /**
         * ----------------- advanced tab --------------------
         */
        var advancedTab = dialogDefinition.getContents('advanced');
        advancedTab.add({
            type: 'text',
            id: 'txtdlgGenStyle',
            label: editor.lang.common.cssStyle,
            validate: CKEDITOR.dialog.validate.inlineStyle( editor.lang.common.invalidInlineStyle ),
            'default': '',
            setup: function( type, element ) {

                if ( type == CKEDITOR.plugins.chameleon_image.IMAGE ) {
                    var genStyle = element.getAttribute( 'style' );
                    if ( !genStyle && element.$.style.cssText )
                        genStyle = element.$.style.cssText;
                    this.setValue( genStyle );

                    var height = element.$.style.height,
                        width = element.$.style.width,
                        aMatchH = ( height ? height : '' ).match( CKEDITOR.plugins.chameleon_image.regexGetSize ),
                        aMatchW = ( width ? width : '' ).match( CKEDITOR.plugins.chameleon_image.regexGetSize );

                    this.attributesInStyle = {
                        height: !!aMatchH,
                        width: !!aMatchW
                    };
                }
            },
            onChange: function() {
                CKEDITOR.plugins.chameleon_image._commitInternally.call( this, [ 'info:cmbFloat', 'info:cmbAlign',
                    'info:marginTop', 'info:marginRight', 'info:marginBottom', 'info:marginLeft',
                    'info:txtBorder',
                    'info:txtWidth', 'info:txtHeight' ] );
                CKEDITOR.plugins.chameleon_image._updatePreview( this );
            },
            commit: function( type, element ) {
                if ( type == CKEDITOR.plugins.chameleon_image.IMAGE && ( this.getValue() || this.isChanged() ) ) {
                    element.setAttribute( 'style', this.getValue() );
                }
            }
        });

    },
    removeHSpaceAndVSpace: function(dialogDefinition){

        var infoTab = dialogDefinition.getContents('info'),
            advancedTab = dialogDefinition.getContents('advanced');

        infoTab.remove('txtHSpace');
        infoTab.remove('txtVSpace');
        advancedTab.remove('txtdlgGenStyle');

    },
    setupShowFullField:function (dialogDefinition, editor) {
        var infoTab = dialogDefinition.getContents('info'),
            lang = editor.lang.chameleon_image;
        infoTab.add({
            id:'showFull',
            type:'select',
            label:lang.labelShowFull,
            'default':'1',
            items:[
                ['Ja', '1'],
                ['Nein', '0']
            ],
            setup:function (type, element) {
                this.setValue(element.getAttribute('cmsshowfull'));
            },
            commit:function (type, element) {
                if (this.getValue() || this.isChanged()) {
                    element.setAttribute('cmsshowfull', this.getValue());
                }
            }
        });
    },
    setupShowCaptionField:function (dialogDefinition, editor) {
        var infoTab = dialogDefinition.getContents('info'),
            lang = editor.lang.chameleon_image;
        infoTab.add({
            id:'showCaption',
            type:'select',
            label:lang.labelShowCaption,
            'default':'0',
            items:[
                ['Ja', '1'],
                ['Nein', '0']
            ],
            setup:function (type, element) {
                this.setValue(element.getAttribute('cmsshowcaption'));
            },
            commit:function (type, element) {
                if (this.getValue() || this.isChanged()) {
                    element.setAttribute('cmsshowcaption', this.getValue());
                }
            }
        });
    },
    setupCaptionField:function (dialogDefinition) {
        var advancedTab = dialogDefinition.getContents('advanced'),
            captionField = advancedTab.get('txtGenTitle');
        captionField['setup'] = (function(type, element) {
            this.setValue(element.getAttribute('cmscaption'));
        });
        captionField['commit'] = (function(type, element) {
            if (this.getValue() || this.isChanged()) {
                element.setAttribute('cmscaption', this.getValue());
            }
        });
    },
    setupTitleTextField:function (dialogDefinition) {
        var advancedTab = dialogDefinition.getContents('advanced'),
            titleTextField = advancedTab.get('txtGenTitle');
        titleTextField['setup'] = (CKEDITOR.tools.override(titleTextField.setup, function(originalSetupFunction) {
            return function(type, element) {
                originalSetupFunction.call(this, type, element);
                this.setValue(element.getAttribute('title'));
            };
        }));
        titleTextField['commit'] = (CKEDITOR.tools.override(titleTextField.commit, function(originalCommitFunction) {
            return function(type, element) {
                originalCommitFunction.call(this, type, element);
                if (this.getValue() || this.isChanged()) {
                    element.setAttribute('title', this.getValue());
                }
            };
        }));
    },
    setupClassField:function (dialogDefinition) {
        var advancedTab = dialogDefinition.getContents('advanced'),
            classTextField = advancedTab.get('txtGenClass');
        classTextField['commit'] = (CKEDITOR.tools.override(classTextField.commit, function(originalCommitFunction) {
            return function(type, element) {
                originalCommitFunction.call(this, type, element);
                var value = this.getValue();
                if(false == value.includes('cms_image')){
                    element.setAttribute('class', value.concat(' ckeditor_cms_image'));
                }
            };
        }));
    },
    setupWidthField:function (dialogDefinition) {
        var infoTab = dialogDefinition.getContents('info'),
            widthField = infoTab.get('txtWidth');

        widthField['onChange'] = function(){
            // removes the internalCommit for 'advanced:txtdlgGenStyle'
        };

        widthField['setup'] = (CKEDITOR.tools.override(widthField.setup, function(originalSetupFunction){
            return function(type, element){
                originalSetupFunction.call(this, type, element);
                this.setValue(element.getAttribute('width'));
            }
        }));
        widthField['commit'] = (CKEDITOR.tools.override(widthField.commit, function(originalCommitFunction){
            return function(type, element){
                originalCommitFunction.call(this, type, element);

                if (this.getValue() || this.isChanged()) {
                    element.setAttribute('width', this.getValue());
                }
                element.setAttribute('max-width', '100%');

            }
        }));
    },
    setupHeightField:function (dialogDefinition) {
        var infoTab = dialogDefinition.getContents('info'),
            heightField = infoTab.get('txtHeight');

        heightField['onChange'] = function(){
            // removes the internalCommit for 'advanced:txtdlgGenStyle'
        };

        heightField['setup'] = (CKEDITOR.tools.override(heightField.setup, function(originalSetupFunction){
            return function(type, element){
                originalSetupFunction.call(this, type, element);
                this.setValue(element.getAttribute('height'));
            }
        }));
        heightField['commit'] = (CKEDITOR.tools.override(heightField.commit, function(originalCommitFunction){
            return function(type, element){
                originalCommitFunction.call(this, type, element);

                if (this.getValue() || this.isChanged()) {
                    element.setAttribute('height', this.getValue());
                }
            }
        }));
    },
    setupAlignField: function(dialogDefinition){

        // removes the internalCommit for 'advanced:txtdlgGenStyle'

        var infoTab = dialogDefinition.getContents('info'),
            alignField = infoTab.get('cmbAlign');

        alignField['onChange'] = function(type, element) {
            CKEDITOR.plugins.chameleon_image._updatePreview( dialogDefinition.dialog );
        };


    },
    setupCmsMediaIdField:function (dialogDefinition) {
        var infoTab = dialogDefinition.getContents('info');
        infoTab.add({
            id:'cmsMediaId',
            type:'text',
            label:'',
            'default':'',
            'inputStyle':'display:none',
            setup:function (type, element) {
                this.setValue(element.getAttribute('cmsmedia'));
            },
            commit:function (type, element) {
                if (this.getValue() || this.isChanged()) {
                    element.setAttribute('cmsmedia', this.getValue());
                }
            }
        });
    }
};

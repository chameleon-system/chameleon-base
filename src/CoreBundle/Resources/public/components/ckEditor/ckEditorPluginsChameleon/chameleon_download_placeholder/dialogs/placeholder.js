/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

(function() {
	function chameleon_download_placeholderDialog( editor, isEdit ) {

		var lang = editor.lang.chameleon_download_placeholder,
			generalLabel = editor.lang.common.generalTab;

        function getDownloadElementFromPlaceHolderValue(elementValue){
            elementValue = elementValue.slice( 2, -2 ); // strip off [{ and }]

            var _elementParts = elementValue.split(',');

            var _downloadElement = {
                id: _elementParts[0],
                type: _elementParts[1],
                title: _elementParts[2],
                icon: false, // optional
                kb: false // optional
            };
            if(_elementParts[3] !== undefined){
                if(_elementParts[3] == 'ico'){
                    _downloadElement.icon = true;
                }
                if(_elementParts[3] == 'kb'){
                    _downloadElement.kb = true;
                }
            }
            if(_elementParts[4] !== undefined){
                _downloadElement.kb = true;
            }

            return _downloadElement;

        }

        function getPlaceHolderValueFromDownloadElement(downloadElement){
            var _return = downloadElement.id+',';
            _return += downloadElement.type+',';
            _return += downloadElement.title;

            if(downloadElement.icon === true){
                _return += ',ico';
            }
            if(downloadElement.kb === true){
                _return += ',kb';
            }
            return _return;
        }

        if(isEdit){

            this._element = CKEDITOR.plugins.chameleon_download_placeholder.getSelectedPlaceHolder( editor);

            return {
                title: lang.title,
                minWidth: 300,
                minHeight: 80,
                contents: [
                    {
                        id: 'info',
                        label: generalLabel,
                        title: generalLabel,
                        elements: [
                            {
                                id: 'title',
                                type: 'text',
                                style: 'width: 100%;',
                                label: lang.field_title,
                                'default': '',
                                required: true,
                                validate: CKEDITOR.dialog.validate.notEmpty( lang.textMissing ),
                                setup: function( downloadElement ) {
                                    if ( isEdit ) {

                                        this.setValue( downloadElement.title);

                                    }
                                }
                            },
                            {
                                id: 'show_icon',
                                type: 'checkbox',
                                label: lang.field_show_icon,
                                setup: function(downloadElement){
                                    if(isEdit && downloadElement.icon){
                                        this.setValue('checked');
                                    }
                                }
                            },
                            {
                                id: 'show_size',
                                type: 'checkbox',
                                label: lang.field_show_size,
                                setup: function(downloadElement){
                                    if(isEdit && downloadElement.kb){
                                        this.setValue('checked');
                                    }
                                }
                            }
                        ]
                    }
                ],
                onShow: function() {
                    if ( isEdit ){
                        this._element = CKEDITOR.plugins.chameleon_download_placeholder.getSelectedPlaceHolder( editor );

                        this.setupContent( getDownloadElementFromPlaceHolderValue(this._element.getText()) );
                    }
                },
                onOk: function() {

                    var _elementTextWithPlaceHolderTags = this._element.getText();
                    var downloadElement = getDownloadElementFromPlaceHolderValue(_elementTextWithPlaceHolderTags);
                    downloadElement.title = this.getContentElement('info', 'title').getValue();
                    downloadElement.title = downloadElement.title.replace(',','');

                    downloadElement.icon = this.getContentElement('info', 'show_icon').getValue();
                    downloadElement.kb = this.getContentElement('info', 'show_size').getValue();

                    var _newPlaceHolderValue = getPlaceHolderValueFromDownloadElement(downloadElement);

                    var placeholderTag = editor.config.placeholderTag || '[{$CONTENT$}]',
                        _newPlaceHolder = placeholderTag.replace( '$CONTENT$', _newPlaceHolderValue );

                    // The placeholder must be recreated.
                    CKEDITOR.plugins.chameleon_download_placeholder.createPlaceholder( editor, this._element, _newPlaceHolder );

                    delete this._element;
                }
            };

        }
	}

	CKEDITOR.dialog.add( 'create_chameleon_download_placeholder', function( editor ) {
		return chameleon_download_placeholderDialog( editor );
	});
	CKEDITOR.dialog.add( 'edit_chameleon_download_placeholder', function( editor ) {
		return chameleon_download_placeholderDialog( editor, 1 );
	});
})();

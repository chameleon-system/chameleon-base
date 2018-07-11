/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

/**
 * @fileOverview The "placeholder" plugin.
 *
 */

(function() {
	CKEDITOR.plugins.add( 'chameleon_download_placeholder', {
		requires: 'dialog',
		lang: 'en,de', // %REMOVE_LINE_CORE%
		icons: 'chameleon_download_placeholder', // %REMOVE_LINE_CORE%
        hidpi: true, // %REMOVE_LINE_CORE%
		onLoad: function() {
			CKEDITOR.addCss( '.chameleon_download_placeholder' +
				'{' +
					'background-color: #f8a441;' +
					( CKEDITOR.env.gecko ? 'cursor: default;' : '' ) +
				'}'
				);
		},
		init: function( editor ) {
			var lang = editor.lang.chameleon_download_placeholder;

			editor.addCommand( 'create_chameleon_download_placeholder', new CKEDITOR.dialogCommand( 'create_chameleon_download_placeholder' ) );
			editor.addCommand( 'edit_chameleon_download_placeholder', new CKEDITOR.dialogCommand( 'edit_chameleon_download_placeholder' ) );

            if ( editor.addMenuItems ) {
                editor.addMenuGroup( 'chameleon_download_placeholder', 20 );
                editor.addMenuItems({
                    edit_chameleon_download_placeholder: {
                        label: lang.edit,
                        command: 'edit_chameleon_download_placeholder',
                        group: 'chameleon_download_placeholder',
                        order: 1,
                        icon: 'chameleon_download_placeholder'
                    }
                });

                if ( editor.contextMenu ) {
                    editor.contextMenu.addListener( function( element, selection ) {
                        if ( !element || !element.data( 'chameleon-download-placeholder' ) )
                            return null;

                        return { edit_chameleon_download_placeholder: CKEDITOR.TRISTATE_OFF };
                    });
                }
            }

			editor.on( 'doubleclick', function( evt ) {
                var placeHolderSelectionFound = CKEDITOR.plugins.chameleon_download_placeholder.getSelectedPlaceHolder( editor );
				if ( placeHolderSelectionFound ){
                    evt.data.dialog = 'edit_chameleon_download_placeholder';
                }
			});

			editor.on( 'contentDom', function() {
				editor.editable().on( 'resizestart', function( evt ) {
                    if ( editor.getSelection().getSelectedElement().data( 'chameleon-download-placeholder' ) )
						evt.data.preventDefault();
				});
			});

			CKEDITOR.dialog.add( 'create_chameleon_download_placeholder', this.path + 'dialogs/placeholder.js' );
			CKEDITOR.dialog.add( 'edit_chameleon_download_placeholder', this.path + 'dialogs/placeholder.js' );
		},
		afterInit: function( editor ) {
			var dataProcessor = editor.dataProcessor,
				dataFilter = dataProcessor && dataProcessor.dataFilter,
				htmlFilter = dataProcessor && dataProcessor.htmlFilter,
                placeholderReplaceRegex = /\[\{\s*?((\w|-){36}|\d+)\s*?,\s*?dl\s*?,[^,\[\{\}\]]*?\s*?(|,\s*?(ico|kb)|,\s*?ico\s*?,\s*?kb|,\s*?kb\s*?,\s*?ico)\s*?\}\]/g;

			if ( dataFilter ) {
				dataFilter.addRules({
					text: function( text ) {
						return text.replace( placeholderReplaceRegex, function( match ) {
							return CKEDITOR.plugins.chameleon_download_placeholder.createPlaceholder( editor, null, match, 1 );
						});
					}
				});
			}

			if ( htmlFilter ) {
				htmlFilter.addRules({
					elements: {
						'span': function( element ) {
							if ( element.attributes && element.attributes[ 'data-chameleon-download-placeholder' ] )
								delete element.name;
						}
					}
				});
			}
		}
	});
})();

CKEDITOR.plugins.chameleon_download_placeholder = {
	createPlaceholder: function( editor, oldElement, text, isGet ) {

        var element = new CKEDITOR.dom.element( 'span', editor.document);
		element.setAttributes({
			contentEditable: 'false',
			'data-chameleon-download-placeholder': 1,
			'class': 'chameleon_download_placeholder'
		});

		text && element.setText( text );

		if ( isGet )
			return element.getOuterHtml();

		if ( oldElement ) {
			if ( CKEDITOR.env.ie ) {
				element.insertAfter( oldElement );
				// Some time is required for IE before the element is removed.
				setTimeout( function() {
					oldElement.remove();
					element.focus();
				}, 10 );
			} else
				element.replace( oldElement );
		} else
			editor.insertElement( element );

		return null;
	},

	getSelectedPlaceHolder: function( editor ) {
		var range = editor.getSelection().getRanges()[ 0 ];
        range.shrink( CKEDITOR.SHRINK_TEXT );
        var node = range.startContainer;
		while ( node && !( node.type == CKEDITOR.NODE_ELEMENT && node.data( 'chameleon-download-placeholder' ) ) ){
			node = node.getParent();
        }
		return node;
	}
};

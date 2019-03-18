/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */


CKEDITOR.editorConfig = function (config) {

    // %REMOVE_START%

    config.skin = 'moono-lisa'; // use packaged skin
    // %REMOVE_END%

    config.uiColor = '#f0f3f5';
    // For the complete reference:
    // http://docs.ckeditor.com/#!/api/CKEDITOR.config

    /**
     * disable encoding of html entities since we handle encoding while rendering in frontend
     */
    config.entities = false;

    /**
     * the default value for this config parameter is '#39'
     * since we use twig conditions in our e-mail templates ckEditor is not allowed to
     * convert single quotes into html entities (&#39)
     * this is only relevant if config.entities is true
     */
    config.entities_additional = '';

    // the placeholder regex only matches for "not-download" placeholders
    // (download placeholders include ",dl," )
    config.placeholderReplaceRegex = /\[\{([^\}\]](?!,dl,))*?\}\]/g;
    config.placeholderTagStart = '[{';
    config.placeholderTagEnd = '}]';
    config.placeholderTag = config.placeholderTagStart + '$CONTENT$' + config.placeholderTagEnd;

    // enables the outlines plugin onload
    config.startupOutlineBlocks = true;

    // ads this class to <pre> tags
    config.insertpre_class = 'prettyprint';

    // disable the advanced content filter for now because we need to rework the image plugin first to allow the chameleon properties
    config.allowedContent = true;

    config.autoGrow_maxHeight = 600;

    config.minimumChangeMilliseconds = 300;

    config.codemirror = {

        // Set this to the theme you wish to use (codemirror themes)
        theme: 'default',

        // Whether or not you want to show line numbers
        lineNumbers: true,

        // Whether or not you want to use line wrapping
        lineWrapping: true,

        // Whether or not you want to highlight matching braces
        matchBrackets: true,

        // Whether or not you want to highlight matching tags
        matchTags: true,

        // Whether or not you want tags to automatically close themselves
        autoCloseTags: true,

        // Whether or not you want Brackets to automatically close themselves
        autoCloseBrackets: true,

        // Whether or not to enable search tools, CTRL+F (Find), CTRL+SHIFT+F (Replace), CTRL+SHIFT+R (Replace All), CTRL+G (Find Next), CTRL+SHIFT+G (Find Previous)
        enableSearchTools: true,

        // Whether or not you wish to enable code folding (requires 'lineNumbers' to be set to 'true')
        enableCodeFolding: true,

        // Whether or not to enable code formatting
        enableCodeFormatting: true,

        // Whether or not to automatically format code should be done when the editor is loaded
        autoFormatOnStart: true,

        // Whether or not to automatically format code which has just been uncommented
        autoFormatOnUncomment: true,

        // Whether or not to highlight the currently active line
        highlightActiveLine: true,

        // Whether or not to highlight all matches of current word/selection
        highlightMatches: true,

        // Define the language specific mode 'htmlmixed' for html  including (css, xml, javascript), 'application/x-httpd-php' for php mode including html, or 'text/javascript' for using java script only
        mode: 'htmlmixed',

        // Whether or not to show the search Code button on the toolbar
        showSearchButton: true,

        // Whether or not to show Trailing Spaces
        showTrailingSpace: true,

        // Whether or not to show the format button on the toolbar
        showFormatButton: true,

        // Whether or not to show the comment button on the toolbar
        showCommentButton: true,

        // Whether or not to show the uncomment button on the toolbar
        showUncommentButton: true,

        // Whether or not to show the showAutoCompleteButton button on the toolbar
        showAutoCompleteButton: true
    };
};

// disables the remove of empty i and span tags (needed for bootstrap)
CKEDITOR.dtd.$removeEmpty['i'] = false;
CKEDITOR.dtd.$removeEmpty['span'] = false;

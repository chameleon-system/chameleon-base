/**
 * @fileOverview The "chameleon_content_filter_extra" plugin.
 * @description if loaded the defined html tags and properties are allowed
 *
 */

CKEDITOR.plugins.add('chameleon_content_filter_extra', {
    init: function (editor) {

        if (editor.addFeature) {

            /**
             * allowedContent example:

             * allowedContent: {
             *      'img': true, // will allow everything
             *      'div': {
             *          classes: 'myClass1, myClass2' // will allow "myClass1" and "myClass2" on <div>
             *      },
             *      '*': {
             *          styles: 'display' // will allow "display" in "styles" in all tags
             *      },
             *      'a': {
                        attributes: '!href' // sets "href" as required for <a>
             *      },
             *      'td tr': {
             *          styles: '...' // will allow "styles" for "td" and "tr"
             *      }
             * }
             *
             * IMPORTANT:
             * tags can only be defined once in allowedContent.
             * "a": { ... } and "a img": { ... } won't work
             *
             * more information:
             * @see http://docs.ckeditor.com/#!/guide/dev_allowed_content_rules
             */

            editor.addFeature({
                name: 'font',
                allowedContent: {
                    'font': true,
                    '*': {
                        styles: 'background-color, color, font-size'
                    },
                    'script': true,
                    'embed': true,
                    'object': true
                }
            });
        }
    }
});
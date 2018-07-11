CHAMELEON = CHAMELEON || {};
CHAMELEON.CORE = CHAMELEON.CORE || {};
CHAMELEON.CORE.CKEditor = CHAMELEON.CORE.CKEditor || {};

CHAMELEON.CORE.CKEditor.activeInstanceName = '';

CHAMELEON.CORE.CKEditor.instantiate = function (editorName, settings) {
    CKEDITOR.replace(editorName, settings);
};

CHAMELEON.CORE.CKEditor.fetchData = function (editorName) {
    var data = CKEDITOR.instances[editorName].getData();
    $('#' + editorName).val(data);
};

// if our patch for the placeholder plugin would not be accepted we need to extend the createPlaceholder method and
// patch the placeholderReplaceRegex regex variable in core/httpdocs/chameleon/blackbox/components/ckEditor/plugins/placeholder/plugin.js
/*CKEDITOR.on('instanceReady', function (ev) {
    CKEDITOR.plugins.link.onOk = CKEDITOR.tools.override(CKEDITOR.plugins.link.onOk, function (originalLinkOnOkFunction) {
        return function () {
            console.log('reached this');
            originalLinkOnOkFunction.call(this);

            if (this._.selectedElement) {
                var element = this._.selectedElement;
                element.setAttribute('title', 'foo');

            }
        };
    });
});*/

CHAMELEON.CORE.CKEditor.setActiveInstanceName = function (newActiveInstanceName) {
    CHAMELEON.CORE.CKEditor.activeInstanceName = newActiveInstanceName;
};
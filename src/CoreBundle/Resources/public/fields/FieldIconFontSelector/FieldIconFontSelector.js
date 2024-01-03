if (typeof CHAMELEON === "undefined" || !CHAMELEON) {
    var CHAMELEON = {};
}
CHAMELEON.CORE = CHAMELEON.CORE || {};
CHAMELEON.CORE.FieldIconFontSelector = CHAMELEON.CORE.FieldIconFontSelector || {};

CHAMELEON.CORE.FieldIconFontSelector =
{
    openDialog: function (fieldName, title) {
        const content = document.getElementById(fieldName+'-icon-list').innerHTML;
        CHAMELEON.CORE.showModal(title, content, 'modal-xxl');
    },

    selectIconClass: function (iconElement, fieldName) {
        const iconClass = iconElement.dataset.cssClass;
        document.getElementById(fieldName).value = iconClass;
        document.getElementById(fieldName+'-active-icon').className = iconClass;
        CloseModalIFrameDialog();
    }
}

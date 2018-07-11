/**
 * please note:
 * if your project already has a chameleon.js in "assets/js" please check if getCheckboxState exists and if not copy it from this file
 */

if (typeof CHAMELEON === "undefined" || !CHAMELEON) {
    var CHAMELEON = {};
}
CHAMELEON.Custom = CHAMELEON.Custom || {};

/**
 *
 * @param checkbox has to be an jquery object
 * @return {*}
 */
CHAMELEON.Custom.getCheckboxState = function (checkbox) {
    var checkboxState = checkbox.prop('checked');

    if ('checked' == checkboxState) {
        checkboxState = true;
    } else if ('undefined' == checkboxState) {
        checkboxState = false;
    }

    return checkboxState;
};
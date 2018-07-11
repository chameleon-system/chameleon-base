/*
 * provides a method to localize date strings
 */

if (typeof CHAMELEON === "undefined" || !CHAMELEON) {
    var CHAMELEON = {};
}
CHAMELEON.CORE = CHAMELEON.CORE || {};


CHAMELEON.CORE.i18n = CHAMELEON.CORE.i18n || {};

CHAMELEON.CORE.i18n.GetLocalDateString = function (dateobject) {
    iMonth = dateobject.getMonth() + 1;
    var sDateString = dateobject.getFullYear() + '-' + iMonth + '-' + dateobject.getDate() + ' ' + dateobject.getHours() + ':' + dateobject.getMinutes();
    return sDateString;
};

if (typeof CHAMELEON === "undefined" || !CHAMELEON) {
    var CHAMELEON = {};
}
CHAMELEON.Custom = CHAMELEON.Custom || {};

CHAMELEON.Custom.pkgNewsletter  = CHAMELEON.Custom.pkgNewsletter || {};

CHAMELEON.Custom.pkgNewsletter.toggleSignUpGroups = function (callingCheckbox)
{
    var checkState = CHAMELEON.Custom.getCheckboxState($(callingCheckbox));

    $('.newsletter_group input[type=checkbox]').prop('checked', checkState);
};

CHAMELEON.Custom.pkgNewsletter.toggleAllSignUpCheckbox = function(callingCheckbox)
{
    var checkState = CHAMELEON.Custom.getCheckboxState($(callingCheckbox));
    var checkStateAllSignUpCheckbox = CHAMELEON.Custom.getCheckboxState($('input[type=checkbox]#all'));

    if (false == checkState && true == checkStateAllSignUpCheckbox) {
        $('input[type=checkbox]#all').prop('checked', false);
    }
};
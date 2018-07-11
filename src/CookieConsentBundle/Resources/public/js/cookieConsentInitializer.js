window.addEventListener("load", function()
{
    var loadedConfig = document.getElementById('chameleon_system_cookie_consent.config').dataset;

    var config = {
        position: loadedConfig.position,
        theme: loadedConfig.theme,
        palette: {
            popup: {
                background: loadedConfig.bgcolor
            },
            button: {
                background: loadedConfig.buttonbgcolor,
                text: loadedConfig.buttontextcolor
            }
        },
        content: {
            message: loadedConfig.consentmessage,
            dismiss: loadedConfig.okbuttonmessage
        }
    };

    if ('' === loadedConfig.morelinkurl) {
        config['showLink'] = false;
    } else {
        config['content']['link'] = loadedConfig.morelinktext;
        config['content']['href'] = loadedConfig.morelinkurl;
    }

    window.cookieconsent.initialise(config);
});

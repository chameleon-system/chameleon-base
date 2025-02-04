const { TABS, TOOLS } = FilerobotImageEditor;
const imageUrlField = document.querySelector('input[name="imageUrl"]');
const imageUrl = imageUrlField.value;

const imageIdField = document.querySelector('input[name="imageId"]');
const imageId = imageIdField.value;

const config = {
    source: imageUrl,
    onSave: (editedImageObject, designState) =>{
        const hiddenInput = document.querySelector('#editedImageData');
        hiddenInput.value = JSON.stringify({ editedImageObject, designState });

        const submitButton = document.querySelector('#submitEditedImage');
        submitButton.click();
    },
    annotationsCommon: {
        fill: '#ff0000',
    },
    Text: { text: 'Filerobot...' },
    Rotate: { angle: 90, componentType: 'slider' },
    useBackendTranslations: true,
    language: 'de',
    translations: {
        profile: 'Profile',
        coverPhoto: 'Cover photo',
        facebook: 'Facebook',
        socialMedia: 'Social Media',
        fbProfileSize: '180x180px',
        fbCoverPhotoSize: '820x312px',
    },
    Crop: {
        presetsItems: [
            {
                titleKey: 'classicTv',
                descriptionKey: '4:3',
                ratio: 4 / 3,
            },
            {
                titleKey: 'cinemascope',
                descriptionKey: '21:9',
                ratio: 21 / 9,
            },
        ],
        presetsFolders: [
            {
                titleKey: 'socialMedia',

                groups: [
                    {
                        titleKey: 'facebook',
                        items: [
                            {
                                titleKey: 'profile',
                                width: 180,
                                height: 180,
                                descriptionKey: 'fbProfileSize',
                            },
                            {
                                titleKey: 'coverPhoto',
                                width: 820,
                                height: 312,
                                descriptionKey: 'fbCoverPhotoSize',
                            },
                        ],
                    },
                ],
            },
        ],
    },
    tabsIds: [TABS.ADJUST, TABS.ANNOTATE, TABS.FINETUNE, TABS.RESIZE, TABS.FILTERS],
    defaultTabId: TABS.ADJUST,
    defaultToolId: TOOLS.TEXT,
};

const filerobotImageEditor = new FilerobotImageEditor(
    document.querySelector('#editor_container'),
    config,
);

filerobotImageEditor.render({
    onClose: (closingReason) => {
        filerobotImageEditor.terminate();
    },
});
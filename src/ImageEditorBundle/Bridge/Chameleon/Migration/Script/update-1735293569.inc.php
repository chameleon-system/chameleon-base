<h1>Build #1735293569</h1>
<h2>Date: 2024-12-27</h2>
<div class="changelog">
    - #65211: add backend messages for image editor
</div>
<?php

TCMSLogChange::AddBackEndMessage(
    'IMAGE_EDITOR_ERROR_COULD_NOT_CREATE_FILE',
    'Das neue Bild konnte nicht angelegt werden',
    '4',
    'Tritt auf wenn file_put_contents nicht in der Lage war die neue Bilddatei anzulegen',
    'de'
);

TCMSLogChange::AddBackEndMessage(
    'IMAGE_EDITOR_ERROR_COULD_NOT_CREATE_FILE',
    'The new image could not be created',
    '4',
    'This occures when file_put_contents can not create the image file',
    'en'
);

TCMSLogChange::AddBackEndMessage(
    'IMAGE_EDITOR_WARNING_COULD_NOT_SAVE_IMAGE_DATA',
    'Die Bilddaten konnten nicht gespeichert werden.',
    '4',
    'Tritt dieser Fehler auf, ist in der SaveFields Methode die verwendet wird um das Updaten von Thumbnails, Caches usw zu forcieren ein Fehler aufgetreten',
    'de'
);

TCMSLogChange::AddBackEndMessage(
    'IMAGE_EDITOR_WARNING_COULD_NOT_SAVE_IMAGE_DATA',
    'The image could not be saved',
    '4',
    'If this happen, there has been an error in the SaveFields method which forces the system to update thumbnails and caches',
    'en'
);

TCMSLogChange::AddBackEndMessage(
    'IMAGE_EDITOR_SUCCESS_NEW_IMAGE_HAS_BEEN_SAVED',
    'Das Speichern des Bildes war erfolgreich',
    '2',
    'Das Speichern des Bildes war erfolgreich',
    'de'
);

TCMSLogChange::AddBackEndMessage(
    'IMAGE_EDITOR_SUCCESS_NEW_IMAGE_HAS_BEEN_SAVED',
    'Saving the image has been successfull',
    '2',
    'Saving the image has been successfull',
    'en'
);

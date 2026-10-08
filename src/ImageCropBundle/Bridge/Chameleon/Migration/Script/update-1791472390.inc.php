<h1>Build #1791472390</h1>
<h2>Date: 2026-10-08</h2>
<div class="changelog">
    - #70314: describe multiple saved image crops in the field type help text
</div>
<?php

$helpTexts = [
    'de' => <<<'HTML'
<div class="field-name"><strong>Feldname:</strong> cms_media_id oder beliebig</div>
<div class="php-class"><strong>PHP Klasse:</strong> TCMSFieldMediaWithImageCrop extends TCMSFieldExtendedLookupMedia</div>
<div>Erzeugt eine Bildbox mit Auswahl aus der Medienverwaltung und direktem Bildupload. Für ein Bild können mehrere Ausschnitte, auch für unterschiedliche responsive Darstellungen, gespeichert und später bearbeitet werden. Gespeicherte Ausschnitte sind im Editor markiert und werden am Bildfeld aufgelistet. „Ausschnitt speichern“ speichert den aktuellen Ausschnitt, ohne ihn im Feld auszuwählen; „Ausschnitt speichern und wählen“ wählt ihn zusätzlich aus. Der Editor bleibt nach dem Speichern geöffnet.</div>
<div class="important"><strong>Wichtig:</strong> Als Standard-Wert sollte '1' angegeben werden (= Platzhalter-Bild).</div>
<div><strong>Optionale Parameter:</strong></div>
<ul>
    <li><strong>bShowCategorySelector=1/0</strong> - Feld zum Wählen der Kategorie anzeigen. Standard: 1.</li>
    <li><strong>sDefaultCategoryId=ID einer Medienkategorie</strong> - Neue Bilder werden in diese Kategorie hochgeladen.</li>
    <li><strong>imageCropPresetSystemName=Systemname</strong> - Standardvorlage beim Öffnen des Ausschnitt-Editors.</li>
    <li><strong>imageCropPresetRestrictionSystemNames=Systemname1;Systemname2</strong> - Auswählbare Vorlagen einschränken auf hier benötigte Vorlagen, z.B. für responsive Design. Für jede angegebene Vorlage kann ein eigener Ausschnitt gespeichert werden. Bei gesetzter Einschränkung können keine neuen freien Ausschnitte angelegt werden.<br>Listentrennung mit Semikolon</li>
</ul>
HTML,
    'en' => <<<'HTML'
<div class="field-name"><strong>Field name:</strong> cms_media_id or any other name</div>
<div class="php-class"><strong>PHP class:</strong> TCMSFieldMediaWithImageCrop extends TCMSFieldExtendedLookupMedia</div>
<div>Creates an image box for selecting images from the media manager or uploading them directly. Multiple crops can be saved and edited for one image, including crops for different responsive layouts. Saved crops are marked in the editor and listed at the image field. “Save” saves the current crop without selecting it in the field; “Save and set cutout” also selects it. The editor stays open after saving.</div>
<div class="important"><strong>Important:</strong> Set the default value to '1' (= placeholder image).</div>
<div><strong>Optional parameters:</strong></div>
<ul>
    <li><strong>bShowCategorySelector=1/0</strong> - Show the media category selector. Default: 1.</li>
    <li><strong>sDefaultCategoryId=media category ID</strong> - Upload new images to this category.</li>
    <li><strong>imageCropPresetSystemName=systemname</strong> - Default preset when opening the crop editor.</li>
    <li><strong>imageCropPresetRestrictionSystemNames=systemname1;systemname2</strong> - Restrict selectable presets to those needed here, for example for responsive design. A separate crop can be saved for each listed preset. When this restriction is set, new freeform crops cannot be created.<br>Separate list entries with semicolons.</li>
</ul>
HTML,
];

foreach ($helpTexts as $language => $helpText) {
    $data = TCMSLogChange::createMigrationQueryData('cms_field_type', $language)
        ->setFields(['help_text' => $helpText])
        ->setWhereEquals([
            'constname' => 'CMSFIELD_EXTENDEDTABLELIST_MEDIA_CROP',
        ]);
    TCMSLogChange::update(__LINE__, $data);
}

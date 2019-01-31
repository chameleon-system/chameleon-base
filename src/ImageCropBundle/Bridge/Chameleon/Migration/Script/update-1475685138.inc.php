<h1>update - Build #1475685138</h1>
<h2>Date: 2016-10-05</h2>
<div class="changelog">
    add field type<br>
</div>
<?php
$fieldTypeId = TCMSLogChange::createUnusedRecordId('cms_field_type');
$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'de')
    ->setFields(
        array(
            '049_trans' => 'Bild mit Ausschnitt',
            'mysql_standard_value' => '',
            'force_auto_increment' => '0',
            'fieldclass' => '\ChameleonSystem\ImageCropBundle\Bridge\Chameleon\Field\TCMSFieldMediaWithImageCrop',
            'indextype' => 'index',
            'constname' => 'CMSFIELD_EXTENDEDTABLELIST_MEDIA_CROP',
            'mysql_type' => 'CHAR',
            'length_set' => '36',
            'base_type' => 'standard',
            'help_text' => '<div class="field-name"><strong>Feldname:</strong> cms_media_id&nbsp; oder beliebig</div><div class="php-class"><strong>PHP Klasse:</strong> TCMSFieldMediaWithImageCrop extends TCMSFieldExtendedLookupMedia</div><div>Erzeugt eine Bildbox mit Auswahl aus der Medienverwaltung und direktem Bildupload. Es ist möglich, Ausschnitte anzulegen.</div><div>&nbsp;</div><div class="important"><strong>Wichtig:</strong> Als Standard-Wert sollte \'1\' angegeben werden (= Platzhalter-Bild).</div><div>&nbsp;</div><div><ul>	<li class="parameter required head">Pflicht-Parameter:</li>		<li>&nbsp;</li>	<li class="parameter optional head">Optionale Parameter:</li>	<li>	<ul>		<li class="parameter optional"><strong>bShowCategorySelector=1/0</strong> - Feld zum Wählen der Kategorie anzeigen. Default = 1</li>		<li class="parameter optional"><strong>sDefaultCategoryId=[{id einer Medienkategorie}]</strong> In diese Kategorie werden die Bilder hochgeladen.</li>	</ul>	</li>	<li>imageCropPresetSystemName - Default Vorlage für Ausschnitt</li>	<li>imageCropPresetRestrictionSystemNames - Auswählbare Presets einschränken, mit ; trennen	<ul>	</ul>	</li></ul></div>',
            'contains_images' => '1',
            'id' => $fieldTypeId,
        )
    );
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'en')
    ->setFields(
        array(
            '049_trans' => 'Image with crop',
            'help_text' => '<div class="field-name"><strong>Field name:</strong> cms_media_id&nbsp; or arbitrary name</div><div class="php-class"><strong>PHP class:</strong> TCMSFieldMediaWithImageCrop extends TCMSFieldExtendedLookupMedia</div><div>Creates an image preview box from which you can choose images from the media manager or upload images directly. It is possible to crop the images.</div><div>&nbsp;</div><div class="important"><strong>Important:</strong> You should set the default value to \'1\' (= Placeholder image).</div><div>&nbsp;</div><div><ul>	<li class="parameter required head">Pflicht-Parameter:</li>	<li>		</li>	<li>&nbsp;</li>	<li class="parameter optional head">Optional parameters:</li>	<li>	<ul>		<li class="parameter optional"><strong>bShowCategorySelector=1/0</strong> - Show selector for image category. Default = 1</li>		<li class="parameter optional"><strong>sDefaultCategoryId=[{id of a category}]</strong> Images are uploaded into this category.</li>	</ul>	</li>	<li>imageCropPresetSystemName - Default preset for crop</li>	<li>imageCropPresetRestrictionSystemNames - Restrict selectable presets, separate with ;.	<ul>	</ul>	</li></ul></div>',
        )
    )
    ->setWhereEquals(
        array(
            'id' => $fieldTypeId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

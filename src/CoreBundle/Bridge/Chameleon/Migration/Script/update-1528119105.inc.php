<h1>Build #1528119105</h1>
<h2>Date: 2018-06-04</h2>
<div class="changelog">
    - Add cms_media.custom_filename field to allow custom non-translated file names.
    - Make cms_media.path field monolingual and dependent of cms_media.custom_filename
</div>
<?php

$cmsMediaTableId = TCMSLogChange::GetTableId('cms_media');
$newFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');

$query = 'ALTER TABLE `cms_media` ADD `custom_filename` VARCHAR(255) NOT NULL';
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'cms_tbl_conf_id' => $cmsMediaTableId,
      'name' => 'custom_filename',
      'translation' => 'Custom file name',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING'),
      'cms_tbl_field_tab' => '',
      'isrequired' => '0',
      'fieldclass' => '',
      'fieldclass_subtype' => '',
      'class_type' => 'Core',
      'modifier' => 'none',
      'field_default_value' => '',
      'length_set' => '200',
      'fieldtype_config' => '',
      'restrict_to_groups' => '0',
      'field_width' => '',
      'position' => '0',
      '049_helptext' => 'In this field you may provide a custom file name which will be part of the image URL. The resulting image URL can be viewed in the preview field below.',
      'row_hexcolor' => '',
      'is_translatable' => '0',
      'validation_regex' => '',
      'id' => $newFieldId,
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'translation' => 'Dateiname',
      '049_helptext' => 'In diesem Feld können Sie einen eigenen Dateinamen vergeben, der Teil der Bild-URL wird. Die resultierende Bild-URL sehen Sie im Vorschaufeld darunter.',
  ])
  ->setWhereEquals([
      'id' => $newFieldId,
  ])
;
TCMSLogChange::update(__LINE__, $data);

TCMSLogChange::SetFieldPosition($cmsMediaTableId, 'custom_filename', 'metatags');
TCMSLogChange::SetFieldPosition($cmsMediaTableId, 'path', 'custom_filename');

$pathFieldId = TCMSLogChange::GetTableFieldId($cmsMediaTableId, 'path');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'translation' => 'Path',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_SEOURLTITLE'),
      'fieldclass' => 'TCMSFieldMediaPath',
      'fieldtype_config' => 'sourcefieldname=custom_filename',
      'modifier' => 'none',
  ])
  ->setWhereEquals([
      'id' => $pathFieldId,
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        'translation' => 'Pfad',
    ])
    ->setWhereEquals([
       'id' => $pathFieldId,
    ])
;
TCMSLogChange::update(__LINE__, $data);

TCMSLogChange::RunQuery(__LINE__, 'UPDATE `cms_media` SET `custom_filename` = `description`');

TCMSLogChange::makeFieldMonolingual('cms_media', 'path');
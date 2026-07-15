<h1>Build #1784112000</h1>
<h2>Date: 2026-07-15</h2>
<div class="changelog">
    - #70773: Add AI-generated image label fields to cms_media.
</div>
<?php

$cmsMediaTableId = TCMSLogChange::GetTableId('cms_media');
$aiGeneratedFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');
$aiLabelVariantFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');

$query = "ALTER TABLE `cms_media`
              ADD `ai_generated_image` ENUM('0','1') DEFAULT '0' NOT NULL COMMENT 'AI-generated image: If enabled, generated thumbnails receive an AI label',
              ADD `ai_label_variant` ENUM('dark','light') DEFAULT 'dark' NOT NULL COMMENT 'AI label variant'";
TCMSLogChange::RunQuery(__LINE__, $query);

$query = 'ALTER TABLE `cms_media` ADD INDEX `ai_generated_image` (`ai_generated_image`)';
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'cms_tbl_conf_id' => $cmsMediaTableId,
        'name' => 'ai_generated_image',
        'translation' => 'Mark as AI-generated image',
        'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_BOOLEAN'),
        'cms_tbl_field_tab' => '',
        'isrequired' => '0',
        'fieldclass' => '',
        'fieldclass_subtype' => '',
        'class_type' => 'Core',
        'modifier' => 'none',
        'field_default_value' => '0',
        'length_set' => '',
        'fieldtype_config' => '',
        'restrict_to_groups' => '0',
        'field_width' => '0',
        'position' => '3170',
        '049_helptext' => 'If enabled, generated thumbnails and cropped variants receive an AI label at the bottom right.',
        'row_hexcolor' => '',
        'is_translatable' => '0',
        'validation_regex' => '',
        'id' => $aiGeneratedFieldId,
    ]);
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        'translation' => 'KI-generiertes Bild kennzeichnen',
        '049_helptext' => 'Ist dieses Feld aktiviert, erhalten generierte Thumbnails und Ausschnitte unten rechts ein AI-Label.',
    ])
    ->setWhereEquals([
        'id' => $aiGeneratedFieldId,
    ]);
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'cms_tbl_conf_id' => $cmsMediaTableId,
        'name' => 'ai_label_variant',
        'translation' => 'AI label variant',
        'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_LIST'),
        'cms_tbl_field_tab' => '',
        'isrequired' => '0',
        'fieldclass' => '',
        'fieldclass_subtype' => '',
        'class_type' => 'Core',
        'modifier' => 'none',
        'field_default_value' => 'dark',
        'length_set' => '\'dark\',\'light\'',
        'fieldtype_config' => '',
        'restrict_to_groups' => '0',
        'field_width' => '0',
        'position' => '3171',
        '049_helptext' => 'Select the light or dark AI label variant used on generated thumbnails.',
        'row_hexcolor' => '',
        'is_translatable' => '0',
        'validation_regex' => '',
        'id' => $aiLabelVariantFieldId,
    ]);
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        'translation' => 'AI-Label-Variante',
        '049_helptext' => 'Wählt die helle oder dunkle AI-Label-Variante für generierte Thumbnails.',
    ])
    ->setWhereEquals([
        'id' => $aiLabelVariantFieldId,
    ]);
TCMSLogChange::update(__LINE__, $data);

$lockedFieldId = TCMSLogChange::getDatabaseConnection()->fetchOne(
    'SELECT `id` FROM `cms_field_conf` WHERE `cms_tbl_conf_id` = :tableId AND `name` = :fieldName',
    [
        'tableId' => $cmsMediaTableId,
        'fieldName' => 'locked',
    ]
);
if (false !== $lockedFieldId && !empty($lockedFieldId)) {
    TCMSLogChange::SetFieldPosition($cmsMediaTableId, 'ai_generated_image', 'locked');
    TCMSLogChange::SetFieldPosition($cmsMediaTableId, 'ai_label_variant', 'ai_generated_image');
}

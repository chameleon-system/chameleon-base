<h1>Build #1548146915</h1>
<h2>Date: 2019-01-22</h2>
<div class="changelog">
    - Introduce version history boolean field to field type table configuration (initialized with value 'false' by default).<br />
    - Enable version history for base field type "WYSIWYG".<br />
</div>
<?php

// Establish version history boolean field to field type table configuration.

$fieldConfigurationId = TCMSLogChange::createUnusedRecordId('cms_field_conf');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'name' => 'version_history',
        'translation' => 'Version History',
        'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_BOOLEAN'),
        'cms_tbl_field_tab' => '',
        'isrequired' => '0',
        'fieldclass' => '',
        'modifier' => 'none',
        'field_default_value' => '0',
        'length_set' => '',
        'fieldtype_config' => '',
        'restrict_to_groups' => '0',
        'field_width' => '0',
        'position' => '0',
        '049_helptext' => "Allows listing and restoring previous revisions of the field's value if table versioning is enabled",
        'row_hexcolor' => '',
        'is_translatable' => '0',
        'validation_regex' => '',
        'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_field_type'),
        'fieldclass_subtype' => '',
        'class_type' => 'Core',
        'id' => $fieldConfigurationId,
    ]);
TCMSLogChange::insert(__LINE__, $data);

TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('cms_field_type'), 'version_history', 'base_type');

$query = "ALTER TABLE `cms_field_type`
                        ADD `version_history` ENUM('0','1') DEFAULT '0' NOT NULL COMMENT 'Version History: Allows listing and restoring previous revisions of the field\'s value if table versioning is enabled.'";
TCMSLogChange::RunQuery(__LINE__, $query);

// Enable version history for base WYSIWYG field type.

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'en')
    ->setFields([
        'version_history' => '1',
    ])
    ->setWhereEquals([
        'constname' => 'CMSFIELD_WYSIWYG',
    ]);
TCMSLogChange::update(__LINE__, $data);

// Supplement translation

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        'name' => 'version_history',
        'translation' => 'Versionshistorie',
        '049_helptext' => 'Erlaubt bei aktivierter Versionierung der Tabelle die Auflistung und Wiederherstellung bisheriger Versionen des Feldwerts.',
    ])->setWhereEquals([
        'id' => $fieldConfigurationId
    ]);
TCMSLogChange::update(__LINE__, $data);
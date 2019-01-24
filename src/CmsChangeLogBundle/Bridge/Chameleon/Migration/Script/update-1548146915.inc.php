<h1>Build #1548146915</h1>
<h2>Date: 2019-01-22</h2>
<div class="changelog">
    - Introduce version history boolean field to field type table configuration (initialized with value 'false' by default).<br />
    - Enable version history for base field type "WYSIWYG".<br />
</div>
<?php

// Establish version history boolean field to field type table configuration.

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'name' => 'version_history',
        'translation' => 'Versionshistorie',
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
        'position' => '2175',
        '049_helptext' => 'Erlaubt bei aktivierter Versionierung der Tabelle die Auflistung und Wiederherstellung bisheriger Versionen des Feldwerts.',
        'row_hexcolor' => '',
        'is_translatable' => '0',
        'validation_regex' => '',
        'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_field_type'),
        'fieldclass_subtype' => '',
        'class_type' => 'Core',
        'id' => '84003022-e235-caf6-81ba-4fca4f2a95ed',
    ]);
TCMSLogChange::insert(__LINE__, $data);

$query = "ALTER TABLE `cms_field_type`
                        ADD `version_history` ENUM('0','1') DEFAULT '0' NOT NULL COMMENT 'Versionshistorie: Erlaubt bei aktivierter Versionierung der Tabelle die Auflistung und Wiederherstellung bisheriger Versionen des Feldwerts.'";
TCMSLogChange::RunQuery(__LINE__, $query);

/*$query ="ALTER TABLE `cms_field_type`
                     CHANGE `version_history`
                            `version_history` ENUM('0','1') DEFAULT '0' NOT NULL COMMENT 'Version History: If table versioning is enabled, allows listing and restoration of previous versions of a field\\'s value.'";
TCMSLogChange::RunQuery(__LINE__, $query);*/
// TODO: Clarify content language of table's own comment, use either German or English help text.

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'translation' => 'Version History',
        '049_helptext' => 'If table versioning is enabled, allows listing and restoration of previous versions of a field\'s value.',
    ])
    ->setWhereEquals([
        'id' => '84003022-e235-caf6-81ba-4fca4f2a95ed',
    ]);
TCMSLogChange::update(__LINE__, $data);

// Enable version history for base WYSIWYG field type.

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'en')
    ->setFields([
        'version_history' => '1',
    ])
    ->setWhereEquals([
        'id' => '42',
    ]);
TCMSLogChange::update(__LINE__, $data);


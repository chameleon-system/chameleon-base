<h1>Build #1547452866</h1>
<h2>Date: 2019-01-14</h2>
<div class="changelog">
    - https://github.com/chameleon-system/chameleon-system/issues/265
    - backend - main navigation: add new field icon_font_awesome
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_tbl_conf'),
        'name' => 'new_field',
        'translation' => '',
        'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING'),
        'cms_tbl_field_tab' => '',
        'isrequired' => '0',
        'fieldclass' => '',
        'fieldclass_subtype' => '',
        'class_type' => 'Core',
        'modifier' => 'none',
        'field_default_value' => '',
        'length_set' => '',
        'fieldtype_config' => '',
        'restrict_to_groups' => '0',
        'field_width' => '',
        'position' => '0',
        '049_helptext' => '',
        'row_hexcolor' => '',
        'is_translatable' => '0',
        'validation_regex' => '',
        'id' => 'd1956950-5815-f44e-9881-023e28e354c3',
    ])
;
TCMSLogChange::insert(__LINE__, $data);

$query = "ALTER TABLE `cms_tbl_conf`
                        ADD `icon_font_awesome` VARCHAR(255) NOT NULL COMMENT 'Icon Font Awesome css class: '";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        'name' => 'icon_font_awesome',
        'translation' => 'Icon Font Awesome css class',
        'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING'),
        'field_width' => '0',
        'position' => '2124',
    ])
    ->setWhereEquals([
        'id' => 'd1956950-5815-f44e-9881-023e28e354c3',
    ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
//      'name' => 'icon_font_awesome',
        'translation' => 'Icon Font Awesome css class',
    ])
    ->setWhereEquals([
        'id' => 'd1956950-5815-f44e-9881-023e28e354c3',
    ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_module'),
        'name' => 'new_field',
        'translation' => '',
        'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING'),
        'cms_tbl_field_tab' => '',
        'isrequired' => '0',
        'fieldclass' => '',
        'fieldclass_subtype' => '',
        'class_type' => 'Core',
        'modifier' => 'none',
        'field_default_value' => '',
        'length_set' => '',
        'fieldtype_config' => '',
        'restrict_to_groups' => '0',
        'field_width' => '',
        'position' => '0',
        '049_helptext' => '',
        'row_hexcolor' => '',
        'is_translatable' => '0',
        'validation_regex' => '',
        'id' => '63cde82b-57a4-61d0-1444-e05638bf3aef',
    ])
;
TCMSLogChange::insert(__LINE__, $data);

$query = "ALTER TABLE `cms_module`
                        ADD `icon_font_awesome` VARCHAR(255) NOT NULL COMMENT 'Icon Font Awesome css class: '";
TCMSLogChange::RunQuery(__LINE__, $query);
$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        'name' => 'icon_font_awesome',
        'translation' => 'Icon Font Awesome css class',
        'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING'),
        'field_width' => '0',
        'position' => '2125',
    ])
    ->setWhereEquals([
        'id' => '63cde82b-57a4-61d0-1444-e05638bf3aef',
    ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
//      'name' => 'icon_font_awesome',
        'translation' => 'Icon Font Awesome css class',
    ])
    ->setWhereEquals([
        'id' => '63cde82b-57a4-61d0-1444-e05638bf3aef',
    ])
;
TCMSLogChange::update(__LINE__, $data);

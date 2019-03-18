<h1>Build #1547452866</h1>
<h2>Date: 2019-01-14</h2>
<div class="changelog">
    - https://github.com/chameleon-system/chameleon-system/issues/265
    - backend - main navigation: add new field icon_font_css_class
</div>
<?php

$fieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_tbl_conf'),
        'name' => 'icon_font_css_class',
        'translation' => 'Icon Font CSS class',
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
        'field_width' => '0',
        'position' => '2124',
        '049_helptext' => 'The field is used to display a font icon next to the menu item of the table. Fill in the class name here, for example for Font Awesome: fas fa-check',
        'row_hexcolor' => '',
        'is_translatable' => '0',
        'validation_regex' => '',
        'id' => $fieldId,
    ])
;
TCMSLogChange::insert(__LINE__, $data);

$query = "ALTER TABLE `cms_tbl_conf`
                        ADD `icon_font_css_class` VARCHAR(255) NOT NULL COMMENT 'Icon Font CSS class: '";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        'translation' => 'Icon Font CSS-Klasse',
        '049_helptext' => 'In diesem Feld kann ein Font-Icon angegeben werden, das neben dem Menüeintrag für die Tabelle dargestellt wird. Tragen Sie hier den Klassennamen ein, z.B. für Font Awesome: fas fa-check',
    ])
    ->setWhereEquals([
        'id' => $fieldId,
    ])
;
TCMSLogChange::update(__LINE__, $data);

$fieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_module'),
        'name' => 'icon_font_css_class',
        'translation' => 'Icon Font CSS class',
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
        'field_width' => '0',
        'position' => '2125',
        '049_helptext' => 'The field is used to display a font icon next to the menu item of the module. Fill in the class name here, for example for Font Awesome: fas fa-check',
        'row_hexcolor' => '',
        'is_translatable' => '0',
        'validation_regex' => '',
        'id' => $fieldId,
    ])
;
TCMSLogChange::insert(__LINE__, $data);

$query = "ALTER TABLE `cms_module`
                        ADD `icon_font_css_class` VARCHAR(255) NOT NULL COMMENT 'Icon Font CSS class: '";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        'translation' => 'Icon Font CSS-Klasse',
        '049_helptext' => 'In diesem Feld kann ein Font-Icon angegeben werden, das neben dem Menüeintrag für das Modul dargestellt wird. Tragen Sie hier den Klassennamen ein, z.B. für Font Awesome: fas fa-check',
    ])
    ->setWhereEquals([
        'id' => $fieldId,
    ])
;
TCMSLogChange::update(__LINE__, $data);

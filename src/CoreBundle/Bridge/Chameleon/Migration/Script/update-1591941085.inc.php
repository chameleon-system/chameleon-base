<h1>Build #1591941085</h1>
<h2>Date: 2020-06-12</h2>
<div class="changelog">
    - #690: Show menu entry for table; add new field type for this
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'en')
    ->setFields([
        '049_trans' => 'Sidebar menu entry lookup',
        'force_auto_increment' => '0',
        'constname' => 'CMSFIELD_SIDEBAR_LOOKUP',
        'mysql_type' => '',
        'length_set' => '',
        'base_type' => 'standard',
        'help_text' => '',
        'mysql_standard_value' => '',
        'fieldclass' => 'ChameleonSystem\CoreBundle\Field\FieldSidebarConnected',
        'contains_images' => '0',
        'indextype' => 'none',
        'class_subtype' => '',
        'class_type' => 'Core',
        'id' => 'c15bdac0-e953-68f2-0294-e8a60345a698',
    ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'de')
    ->setFields([
        '049_trans' => 'Menü-Eintrag Lookup',
    ])
    ->setWhereEquals([
        'id' => 'c15bdac0-e953-68f2-0294-e8a60345a698',
    ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
->setFields([
    'name' => 'shown_in_menu_as',
    'translation' => 'Found in the menu as',
    'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_SIDEBAR_LOOKUP'),
    'cms_tbl_field_tab' => '',
    'isrequired' => '0',
    'fieldclass' => 'ChameleonSystem\CoreBundle\Field\FieldSidebarConnected',
    'modifier' => 'readonly',
    'field_default_value' => '',
    'length_set' => '',
    'fieldtype_config' => '',
    'restrict_to_groups' => '0',
    'field_width' => '0',
    'position' => '2182',
    '049_helptext' => 'Shows the first location if any where this table is accessible in the sidebar menu.',
    'row_hexcolor' => '',
    'is_translatable' => '0',
    'validation_regex' => '',
    'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_tbl_conf'),
    'fieldclass_subtype' => '',
    'class_type' => 'Core',
    'id' => 'de07ce14-0168-f459-1ffe-f345eef640fa',
])
;
TCMSLogChange::insert(__LINE__, $data);

TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('cms_tbl_conf'), 'shown_in_menu_as', 'list_query');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        'translation' => 'Im Menü unter',
        '049_helptext' => 'Zeigt den ersten Eintrag, falls vorhanden, unter dem diese Tabelle im Seitenmenü angezeigt wird.',
    ])
    ->setWhereEquals([
        'id' => 'de07ce14-0168-f459-1ffe-f345eef640fa',
    ])
;
TCMSLogChange::update(__LINE__, $data);

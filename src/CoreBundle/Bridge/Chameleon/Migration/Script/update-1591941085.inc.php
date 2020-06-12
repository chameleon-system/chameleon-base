<h1>Build #1591941085</h1>
<h2>Date: 2020-06-12</h2>
<div class="changelog">
    - #478: (related) Show menu entry for table; add new field type for this
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
        'id' => '39bffe51-143d-875d-bdaa-1f6c270c041e',
    ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'de')
    ->setFields([
        'translation' => 'Menü-Eintrag Lookup',
    ])
    ->setWhereEquals([
        'id' => '39bffe51-143d-875d-bdaa-1f6c270c041e',
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
      '049_helptext' => '', // TODO
      'row_hexcolor' => '',
      'is_translatable' => '0',
      'validation_regex' => '',
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_tbl_conf'),
      'fieldclass_subtype' => '',
      'class_type' => 'Core',
      'id' => '68aed6eb-5511-75a8-050b-da33b2fc11ab', // TODO ? also below
  ])
;
TCMSLogChange::insert(__LINE__, $data);

TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('cms_tbl_conf'), 'shown_in_menu_as', 'list_query');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        'translation' => 'Im Menü unter',
    ])
    ->setWhereEquals([
        'id' => '68aed6eb-5511-75a8-050b-da33b2fc11ab',
    ])
;
TCMSLogChange::update(__LINE__, $data);

// TODO use virtual field? add "isVirtual"
//   wegen TCMSTableConf.class.php:185 muss ein Tabellenfeld existieren (wenn man keinen eigenen Typ verwendet..)
//$query ="ALTER TABLE `cms_tbl_conf`
//                 ADD `shown_in_menu_as` VARCHAR(255) NOT NULL COMMENT 'Im Seitenmenü unter: '";
//TCMSLogChange::RunQuery(__LINE__, $query);

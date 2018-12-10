<h1>Build #1544434917</h1>
<h2>Date: 2018-12-10</h2>
<div class="changelog">
    - #95: Add and show active field for routing
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'name' => 'active',
      'translation' => 'Activ',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_BOOLEAN'),
      'cms_tbl_field_tab' => '',
      'isrequired' => '0',
      'fieldclass' => '',
      'modifier' => 'none',
      'field_default_value' => '1',
      'length_set' => '',
      'fieldtype_config' => '',
      'restrict_to_groups' => '0',
      'field_width' => '0',
      'position' => '2172',
      '049_helptext' => '',
      'row_hexcolor' => '',
      'is_translatable' => '0',
      'validation_regex' => '',
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('pkg_cms_routing'),
      'fieldclass_subtype' => '',
      'class_type' => 'Core',
      'id' => '35caadaf-764e-2492-a4e8-cb4d7bc339ac',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('pkg_cms_routing'), 'active', 'cms_portal_mlt');

$query ="ALTER TABLE `pkg_cms_routing`
                        ADD `active` ENUM('0','1') DEFAULT '1' NOT NULL COMMENT 'Aktiv: '";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'translation' => 'Aktiv',
  ])
  ->setWhereEquals([
      'id' => '35caadaf-764e-2492-a4e8-cb4d7bc339ac',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$query ="ALTER TABLE `pkg_cms_routing` ADD INDEX ( `active` )";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'en')
  ->setFields([
      'title' => 'Activ',
      'name' => '`pkg_cms_routing`.`active`',
      'db_alias' => 'active',
      'position' => '271',
      'width' => '-1',
      'align' => 'left',
      'callback_fnc' => 'gcf_GetAciveIcon',
      'use_callback' => '1',
      'show_in_list' => '1',
      'show_in_sort' => '0',
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('pkg_cms_routing'),
      'cms_translation_field_name' => '',
      'id' => '7179ca5e-91f5-0912-b378-fbd4755e9820',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

// TODO how to write this?
//TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('cms_tbl_display_list_fields'))

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'de')
    ->setFields([
        'translation' => 'Aktiv',
    ])
    ->setWhereEquals([
        'id' => '7179ca5e-91f5-0912-b378-fbd4755e9820',
    ])
;
TCMSLogChange::update(__LINE__, $data);

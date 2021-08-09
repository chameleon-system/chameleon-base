<h1>Build #1628503013</h1>
<h2>Date: 2021-08-09</h2>
<div class="changelog">
    - ref #126: add systemname field to message types
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_message_manager_message_type'),
      'name' => 'systemname',
      'translation' => 'Systemname',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING'),
      'isrequired' => '0',
      'class_type' => 'Core',
      'modifier' => 'none',
      'restrict_to_groups' => '0',
      'is_translatable' => '0',
      'id' => 'f74894ff-4161-9f95-c390-0468ea94c338',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$query ="ALTER TABLE `cms_message_manager_message_type`
                        ADD `systemname` VARCHAR(255) NOT NULL COMMENT 'Systemname: '";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'translation' => 'Systemname',
  ])
  ->setWhereEquals([
      'id' => 'f74894ff-4161-9f95-c390-0468ea94c338',
  ])
;
TCMSLogChange::update(__LINE__, $data);

TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('cms_message_manager_message_type'), 'systemname', 'name');

// add list fields: ID, systemname, name
$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'en')
  ->setFields([
      'title' => 'Systemname',
      'name' => '`cms_message_manager_type`.`systemname`',
      'db_alias' => 'systemname',
      'position' => '2',
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_message_manager_message_type'),
      'cms_translation_field_name' => '',
      'width' => '-1',
      'align' => 'left',
      'callback_fnc' => '',
      'use_callback' => '0',
      'show_in_list' => '1',
      'show_in_sort' => '0',
      'id' => '6b79ce5b-d7d9-7b4a-c774-104961d857c6',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'de')
  ->setFields([
      'title' => 'Systemname',
  ])
  ->setWhereEquals([
      'id' => '6b79ce5b-d7d9-7b4a-c774-104961d857c6',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'en')
  ->setFields([
      'title' => 'Titel',
      'name' => '`cms_message_manager_type`.`name`',
      'db_alias' => 'name',
      'position' => '3',
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_message_manager_message_type'),
      'cms_translation_field_name' => '',
      'width' => '-1',
      'align' => 'left',
      'use_callback' => '0',
      'show_in_list' => '1',
      'show_in_sort' => '0',
      'id' => '45a424da-644d-4226-f6e3-c439549d48c6',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'de')
  ->setFields([
      'title' => 'Titel',
  ])
  ->setWhereEquals([
      'id' => '45a424da-644d-4226-f6e3-c439549d48c6',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'en')
  ->setFields([
      'title' => 'ID',
      'name' => '`cms_message_manager_type`.`id`',
      'db_alias' => 'id',
      'position' => '1',
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_message_manager_message_type'),
      'cms_translation_field_name' => '',
      'width' => '-1',
      'align' => 'left',
      'callback_fnc' => '',
      'use_callback' => '0',
      'show_in_list' => '1',
      'show_in_sort' => '0',
      'id' => '15c0df93-681e-5035-6519-6c3be86cb184',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'de')
  ->setFields([
      'title' => 'ID',
  ])
  ->setWhereEquals([
      'id' => '15c0df93-681e-5035-6519-6c3be86cb184',
  ])
;
TCMSLogChange::update(__LINE__, $data);

// set systemnames for common core message types
$data = TCMSLogChange::createMigrationQueryData('cms_message_manager_message_type', 'en')
    ->setFields([
        'systemname' => 'unknown',
    ])
    ->setWhereEquals([
        'id' => '1',
    ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_message_manager_message_type', 'en')
    ->setFields([
        'systemname' => 'notice',
    ])
    ->setWhereEquals([
        'id' => '2',
    ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_message_manager_message_type', 'en')
    ->setFields([
        'systemname' => 'warning',
    ])
    ->setWhereEquals([
        'id' => '3',
    ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_message_manager_message_type', 'en')
    ->setFields([
        'systemname' => 'error',
    ])
    ->setWhereEquals([
        'id' => '4',
    ])
;
TCMSLogChange::update(__LINE__, $data);

$query = "UPDATE `cms_message_manager_message_type` SET `systemname` = 'popup' WHERE `name` LIKE 'Popup%'";
TCMSLogChange::RunQuery(__LINE__, $query);

// generate additional systemnames from name field in lowercase and spaces replacement by underscores
$query = "UPDATE `cms_message_manager_message_type` SET `systemname` = REPLACE(LOWER(`name`),' ','_') WHERE `systemname` = ''";
TCMSLogChange::RunQuery(__LINE__, $query);

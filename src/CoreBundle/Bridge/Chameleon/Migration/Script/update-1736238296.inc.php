<h1>Build #1736238296</h1>
<h2>Date: 2025-01-07</h2>
<div class="changelog">
    - ref #65446: fix list field table names for message type table
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'en')
  ->setFields([
      'name' => '`cms_message_manager_message_type`.`id`', // prev.: '`cms_message_manager_type`.`id`'
  ])
  ->setWhereEquals([
      'id' => '15c0df93-681e-5035-6519-6c3be86cb184',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'en')
  ->setFields([
      'name' => '`cms_message_manager_message_type`.`systemname`', // prev.: '`cms_message_manager_type`.`systemname`'
      'title' => 'System name', // prev.: 'Systemname'
  ])
  ->setWhereEquals([
      'id' => '6b79ce5b-d7d9-7b4a-c774-104961d857c6',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'en')
  ->setFields([
      'name' => '`cms_message_manager_message_type`.`name`', // prev.: '`cms_message_manager_type`.`name`'
      'title' => 'Title', // prev.: 'Titel'
  ])
  ->setWhereEquals([
      'id' => '45a424da-644d-4226-f6e3-c439549d48c6',
  ])
;
TCMSLogChange::update(__LINE__, $data);

<h1>Build #1528354506</h1>
<h2>Date: 2018-06-07</h2>
<div class="changelog">
    - Adjust sorting of cms_tpl_module records.
</div>
<?php

$orderFieldId = TCMSLogChange::createUnusedRecordId('cms_tbl_display_orderfields');

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_orderfields', 'en')
  ->setFields([
      'name' => '`cms_tpl_module`.`name`',
      'sort_order_direction' => 'ASC',
      'position' => '',
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_tpl_module'),
      'id' => $orderFieldId,
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_orderfields', 'en')
  ->setFields([
      'position' => '1',
  ])
  ->setWhereEquals([
      'id' => $orderFieldId,
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_orderfields', 'en')
  ->setFields([
      'position' => '2',
  ])
  ->setWhereEquals([
      'name' => '`cms_tpl_module`.`classname`',
  ])
;
TCMSLogChange::update(__LINE__, $data);

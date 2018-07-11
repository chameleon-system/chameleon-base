<h1>Build #1528357659</h1>
<h2>Date: 2018-06-07</h2>
<div class="changelog">
    - Sort migration counters
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_orderfields', 'en')
  ->setFields([
      'name' => '`cms_migration_counter`.`name`',
      'sort_order_direction' => 'ASC',
      'position' => '91',
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_migration_counter'),
      'id' => TCMSLogChange::createUnusedRecordId('cms_tbl_display_orderfields'),
  ])
;
TCMSLogChange::insert(__LINE__, $data);

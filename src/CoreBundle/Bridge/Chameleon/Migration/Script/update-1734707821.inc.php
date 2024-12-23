<h1>Build #1734707821</h1>
<h2>Date: 2024-12-20</h2>
<div class="changelog">
    - ref #65248: change display order field build number to DESC
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_orderfields', 'de')
  ->setFields([
      'name' => '`build_number`',
      'sort_order_direction' => 'DESC',
      'position' => '90',
      'only_backend' => '0',
  ])
  ->setWhereEquals([
      'id' => 'b5d06ace-d8b1-b61c-5881-bcfb4d4f6c48',
  ])
;
TCMSLogChange::update(__LINE__, $data);


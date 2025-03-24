<h1>Build #1739974175</h1>
<h2>Date: 2025-02-19</h2>
<div class="changelog">
    - Update the field `name` in the table `cms_tbl_display_list_fields` to use the field `cms_division`.`name` instead of `bereiche`.`name`
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'de')
  ->setFields([
      'name' => '`cms_division`.`name`', // prev.: '`bereiche`.`name`'
  ])
  ->setWhereEquals([
      'name' => '`bereiche`.`name`',
  ])
;
TCMSLogChange::update(__LINE__, $data);


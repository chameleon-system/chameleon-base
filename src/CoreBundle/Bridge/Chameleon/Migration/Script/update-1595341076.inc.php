<h1>Build #1595341076</h1>
<h2>Date: 2020-07-21</h2>
<div class="changelog">
    - #690: Hide "View in category window" as it does not help with the sidebar (and is now replaced)
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'modifier' => 'hidden',
  ])
  ->setWhereEquals([
      'name' => 'cms_content_box_id',
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_tbl_conf'),
  ])
;
TCMSLogChange::update(__LINE__, $data);


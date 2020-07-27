<h1>Build #1595841100</h1>
<h2>Date: 2020-07-27</h2>
<div class="changelog">
    - #574: Hide "rights" for custom menu items: now handled for the page; not used anymore
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'modifier' => 'hidden',
  ])
  ->setWhereEquals([
      'name' => 'cms_right_mlt',
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_menu_custom_item'),
  ])
;
TCMSLogChange::update(__LINE__, $data);


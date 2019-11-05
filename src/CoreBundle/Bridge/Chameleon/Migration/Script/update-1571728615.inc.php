<h1>Build #1571728615</h1>
<h2>Date: 2019-10-22</h2>
<div class="changelog">
    - #503: Add proper sorting of menu items in "main menu items" table display
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_orderfields', 'en')
  ->setFields([
      'name' => '`cms_menu_item`.`position`',
      'sort_order_direction' => 'ASC',
      'position' => '92',
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_menu_item'),
      'id' => '9f22963e-2768-8e9e-2e33-4f7d0018f4c1',
  ])
;
TCMSLogChange::insert(__LINE__, $data);


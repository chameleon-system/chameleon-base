<h1>Build #1574955118</h1>
<h2>Date: 2019-11-28</h2>
<div class="changelog">
    - #512: Remove deprecated menu entry (cms text image)
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_menu_item', 'de')
  ->setWhereEquals([
      'target_table_name' => 'cms_tbl_conf',
      'target' => TCMSLogChange::GetTableId('cms_font_image'),
  ])
;
TCMSLogChange::delete(__LINE__, $data);

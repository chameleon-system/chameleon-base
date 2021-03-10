<h1>Build #1615360383</h1>
<h2>Date: 2021-03-10</h2>
<div class="changelog">
    - #692: Fix active icon list display
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'de')
  ->setFields([
      'callback_fnc' => 'gcf_GetAciveIcon',
  ])
  ->setWhereEquals([
      'callback_fnc' => 'gcf_GetPublishedIcon',
  ])
;
TCMSLogChange::update(__LINE__, $data);


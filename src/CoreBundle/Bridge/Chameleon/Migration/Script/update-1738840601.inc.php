<h1>Build #1738840601</h1>
<h2>Date: 2025-02-06</h2>
<div class="changelog">
    - #65529: remove sanity check menu item and module
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_menu_item', 'en')
  ->setWhereEquals([
      'id' => 'f3919eeb-f650-60dd-6051-47e83bfbd10d',
  ])
;
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_module', 'en')
  ->setWhereEquals([
      'id' => '4f395b4b-f7c0-9a0a-6b20-e43b3224b75d',
  ])
;
TCMSLogChange::delete(__LINE__, $data);


<h1>Build #1527678214</h1>
<h2>Date: 2018-05-30</h2>
<div class="changelog">
    - Fix display of tree lists.
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'en')
  ->setFields([
      'title' => 'Path',
  ])
  ->setWhereEquals([
      'name' => '`cms_tree`.`pathcache`',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'de')
  ->setFields([
      'title' => 'Pfad',
  ])
  ->setWhereEquals([
      'name' => '`cms_tree`.`pathcache`',
  ])
;
TCMSLogChange::update(__LINE__, $data);

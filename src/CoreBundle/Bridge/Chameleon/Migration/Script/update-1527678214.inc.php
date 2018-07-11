<h1>Build #1527678214</h1>
<h2>Date: 2018-05-30</h2>
<div class="changelog">
    - Fix display of tree lists.
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
  ->setFields([
      'list_query' => 'SELECT `cms_tree`.*, `cms_portal`.`name` AS portal_name
FROM `cms_tree`
LEFT JOIN `cms_tree` AS parent on `cms_tree`.`lft` BETWEEN parent.`lft` AND parent.`rgt`
INNER JOIN `cms_portal` on parent.id = `cms_portal`.`main_node_tree`
',
  ])
  ->setWhereEquals([
      'name' => 'cms_tree',
  ])
;
TCMSLogChange::update(__LINE__, $data);

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

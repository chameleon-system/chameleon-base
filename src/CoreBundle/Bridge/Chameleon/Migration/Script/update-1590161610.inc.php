<h1>Build #1590161610</h1>
<h2>Date: 2020-05-22</h2>
<div class="changelog">
    - #572: portals: add fielClass 'TCMSFieldPortalHomeTreeNode' to field 'page_not_found_node'
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'fieldclass' => 'TCMSFieldPortalHomeTreeNode',
  ])
  ->setWhereEquals([
      'name' => 'page_not_found_node',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$query ="ALTER TABLE `cms_portal` DROP INDEX `page_not_found_node`";
TCMSLogChange::RunQuery(__LINE__, $query);

$query ="ALTER TABLE `cms_portal`
                     CHANGE `page_not_found_node`
                            `page_not_found_node` CHAR(36) NOT NULL COMMENT '404-Page-Not-Found-Seite: '";
TCMSLogChange::RunQuery(__LINE__, $query);

$query ="ALTER TABLE `cms_portal` ADD INDEX `page_not_found_node` (`page_not_found_node`)";
TCMSLogChange::RunQuery(__LINE__, $query);


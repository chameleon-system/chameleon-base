<h1>Build #1544006453</h1>
<h2>Date: 2018-12-05</h2>
<div class="changelog">
    - #92: Do not show (now empty) log entry anymore
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
  ->setFields([
      'cms_content_box_id' => '',
  ])
  ->setWhereEquals([
      'name' => 'pkg_cms_core_log_channel',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
  ->setFields([
      'cms_content_box_id' => '',
  ])
  ->setWhereEquals([
      'name' => 'pkg_cms_core_log',
  ])
;
TCMSLogChange::update(__LINE__, $data);


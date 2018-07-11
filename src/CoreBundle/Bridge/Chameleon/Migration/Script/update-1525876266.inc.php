<h1>Build #1525876266</h1>
<h2>Date: 2018-05-09</h2>
<div class="changelog">
    - Remove TCMSSmartURLHandler_ThumbAutoCreate
</div>
<?php

$databaseConnection = TCMSLogChange::getDatabaseConnection();

$id = $databaseConnection->fetchColumn("SELECT `id` FROM `cms_smart_url_handler` WHERE `name` = 'TCMSSmartURLHandler_ThumbAutoCreate'");

$data = TCMSLogChange::createMigrationQueryData('cms_smart_url_handler_cms_portal_mlt', 'en')
  ->setWhereEquals([
      'source_id' => $id,
  ])
;
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_smart_url_handler', 'en')
  ->setWhereEquals([
      'id' => $id,
  ])
;
TCMSLogChange::delete(__LINE__, $data);

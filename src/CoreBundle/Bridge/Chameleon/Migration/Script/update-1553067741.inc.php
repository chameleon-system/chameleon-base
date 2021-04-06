<h1>Build #1553067741</h1>
<h2>Date: 2019-03-20</h2>
<div class="changelog">
    - Remove Flash support
</div>
<?php

$databaseConnection = TCMSLogChange::getDatabaseConnection();

$flashUrlHandlerId = $databaseConnection->fetchColumn("SELECT `id` FROM `cms_smart_url_handler` WHERE `name` = 'TCMSSmartURLHandler_FlashCrossDomain'");
if (false === $flashUrlHandlerId) {
    return;
}

$data = TCMSLogChange::createMigrationQueryData('cms_smart_url_handler_cms_portal_mlt', 'en')
  ->setWhereEquals([
      'source_id' => $flashUrlHandlerId,
  ])
;
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_smart_url_handler', 'en')
  ->setWhereEquals([
      'id' => $flashUrlHandlerId,
  ])
;
TCMSLogChange::delete(__LINE__, $data);

<h1>Build #1718009570</h1>
<h2>Date: 2024-06-10</h2>
<div class="changelog">
    - ref #63725: fixes missing newsletter queue name field
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
  ->setFields([
//      'name' => 'pkg_newsletter_queue',
//      'translation' => 'Newsletter Versandwarteschlange',
      'name_column' => 'pkg_newsletter_user',
      'name_column_callback' => 'gcf_LookupName',
  ])
  ->setWhereEquals([
      'name' => 'pkg_newsletter_queue',
  ])
;
TCMSLogChange::update(__LINE__, $data);


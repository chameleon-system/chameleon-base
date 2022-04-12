<h1>Build #1648127183</h1>
<h2>Date: 2022-03-24</h2>
<div class="changelog">
    - #744: Only delete changelog entries (rename cronjob)
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_cronjobs', 'de')
  ->setFields([
      'name' => 'Changelog-Einträge löschen',
  ])
  ->setWhereEquals([
      'id' => 'e4f366c8-cde1-7a65-590c-74833be921fb',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_cronjobs', 'en')
    ->setFields([
        'name' => 'Delete changelog entries',
    ])
    ->setWhereEquals([
        'id' => 'e4f366c8-cde1-7a65-590c-74833be921fb',
    ])
;
TCMSLogChange::update(__LINE__, $data);


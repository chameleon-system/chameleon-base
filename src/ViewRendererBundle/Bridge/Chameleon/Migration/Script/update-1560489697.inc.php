<h1>Build #1560489697</h1>
<h2>Date: 2019-06-14</h2>
<div class="changelog">
    -
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'translation' => 'Eigene LESS/SCSS-Datei',
  ])
  ->setWhereEquals([
      'id' => '9e6edd85-9a33-2631-f400-9f504f9f3061',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'translation' => 'LESS/SCSS-File',
    ])
    ->setWhereEquals([
        'id' => '9e6edd85-9a33-2631-f400-9f504f9f3061',
    ])
;
TCMSLogChange::update(__LINE__, $data);

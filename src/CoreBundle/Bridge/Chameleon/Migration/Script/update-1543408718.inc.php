<h1>Build #1543408718</h1>
<h2>Date: 2018-11-28</h2>
<div class="changelog">
    - #30: Correct field definition for cms_media "width"
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'translation' => 'Breite',
      'fieldclass' => '',
  ])
  ->setWhereEquals([
      'id' => TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_media'), 'width'),
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'translation' => 'Width',
    ])
    ->setWhereEquals([
        'id' => TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_media'), 'width'),
    ])
;
TCMSLogChange::update(__LINE__, $data);


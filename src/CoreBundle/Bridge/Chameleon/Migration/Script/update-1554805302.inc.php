<h1>Build #1554805302</h1>
<h2>Date: 2019-04-09</h2>
<div class="changelog">
    - Hide icon field in table and module settings
</div>
<?php

$fieldId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_tbl_conf'), 'icon_list');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'modifier' => 'hidden',
      '049_helptext' => '@deprecated since 7.0.0 - use an icon font instead',
  ])
  ->setWhereEquals([
      'id' => $fieldId,
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        '049_helptext' => '@deprecated since 7.0.0 - use an icon font instead',
    ])
    ->setWhereEquals([
        'id' => $fieldId,
    ])
;
TCMSLogChange::update(__LINE__, $data);


$fieldId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_tpl_module'), 'icon_list');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'modifier' => 'hidden',
        '049_helptext' => '@deprecated since 7.0.0 - use an icon font instead',
    ])
    ->setWhereEquals([
        'id' => $fieldId,
    ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        '049_helptext' => '@deprecated since 7.0.0 - use an icon font instead',
    ])
    ->setWhereEquals([
        'id' => $fieldId,
    ])
;
TCMSLogChange::update(__LINE__, $data);

<h1>Build #1554805302</h1>
<h2>Date: 2019-04-09</h2>
<div class="changelog">
    - Hide old icon fields
</div>
<?php

$tableNames = [
    'cms_content_box',
    'cms_module',
    'cms_tbl_conf',
    'cms_tpl_module',
];

foreach ($tableNames as $tableName) {
    $fieldId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId($tableName), 'icon_list');

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
}

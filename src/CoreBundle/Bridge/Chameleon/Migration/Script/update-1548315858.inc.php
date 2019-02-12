<h1>Build #1548315858</h1>
<h2>Date: 2019-01-24</h2>
<div class="changelog">
    - Mark cms_module.show_as_popup as deprecated.
</div>
<?php

$fieldId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_module'), 'show_as_popup');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        '049_helptext' => '@deprecated since 6.3.0 - no longer used',
    ])
    ->setWhereEquals([
        'id' => $fieldId,
    ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      '049_helptext' => '@deprecated since 6.3.0 - no longer used',
  ])
  ->setWhereEquals([
      'id' => $fieldId,
  ])
;
TCMSLogChange::update(__LINE__, $data);

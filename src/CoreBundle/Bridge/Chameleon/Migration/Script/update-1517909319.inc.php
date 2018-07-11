<h1>Build #1517909319</h1>
<h2>Date: 2018-02-06</h2>
<div class="changelog">
    - Mark cms_config.databaseversion as deprecated.
</div>
<?php

$fieldId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_config'), 'databaseversion');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields(array(
      '049_helptext' => '@deprecated since 6.2.0 - no longer used.',
  ))
  ->setWhereEquals(array(
      'id' => $fieldId,
  ))
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(array(
        '049_helptext' => '@deprecated since 6.2.0 - no longer used.',
    ))
    ->setWhereEquals(array(
        'id' => $fieldId,
    ))
;
TCMSLogChange::update(__LINE__, $data);

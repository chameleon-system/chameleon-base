<h1>Build #1527663588</h1>
<h2>Date: 2018-05-30</h2>
<div class="changelog">
    - Mark pkg_track_object_history.session_id as deprecated
</div>
<?php

$fieldId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('pkg_track_object_history'), 'session_id');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      '049_helptext' => '@deprecated since 6.2.0 - no longer used',
  ])
  ->setWhereEquals([
      'id' => $fieldId,
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      '049_helptext' => '@deprecated since 6.2.0 - no longer used',
  ])
  ->setWhereEquals([
      'id' => $fieldId,
  ])
;
TCMSLogChange::update(__LINE__, $data);

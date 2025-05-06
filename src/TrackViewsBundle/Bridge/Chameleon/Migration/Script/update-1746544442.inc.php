<h1>Build #1746544442</h1>
<h2>Date: 2025-05-06</h2>
<div class="changelog">
    - ref #66494: drop session_id field from pkg_track_object_history
</div>
<?php

$query = 'ALTER TABLE `pkg_track_object_history`
                       DROP `session_id` ';
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf_cms_usergroup_mlt', 'de')
  ->setWhereEquals([
      'source_id' => 'f4a89021-2803-fdb9-0116-18be321ce78a',
  ])
;
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setWhereEquals([
      'id' => 'f4a89021-2803-fdb9-0116-18be321ce78a',
  ])
;
TCMSLogChange::delete(__LINE__, $data);

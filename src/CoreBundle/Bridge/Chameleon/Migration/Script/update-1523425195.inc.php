<h1>Build #1523425195</h1>
<h2>Date: 2018-04-11</h2>
<div class="changelog">
    - #41570: Switch "layout" field to (the correct type) string
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING'),
      'fieldtype_config' => '',
  ])
  ->setWhereEquals([
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_master_pagedef'),
      'name' => 'layout',
  ])
;
TCMSLogChange::update(__LINE__, $data);

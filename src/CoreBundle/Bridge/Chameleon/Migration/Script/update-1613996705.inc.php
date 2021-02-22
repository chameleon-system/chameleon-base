<h1>Build #1613996705</h1>
<h2>Date: 2021-02-22</h2>
<div class="changelog">
    - ref #515: Wrong labels when editing spots of a static module
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'translation' => 'Spots',
  ])
  ->setWhereEquals([
      'field_default_value' => 'cms_master_pagedef_spot',
  ])
;
TCMSLogChange::update(__LINE__, $data);


$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'translation' => 'Belongs to spot',
  ])
  ->setWhereEquals([
      'name' => 'cms_master_pagedef_spot_id',
  ])
;
TCMSLogChange::update(__LINE__, $data);

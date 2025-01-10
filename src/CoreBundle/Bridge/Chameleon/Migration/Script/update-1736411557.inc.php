<h1>Build #1736411557</h1>
<h2>Date: 2025-01-09</h2>
<div class="changelog">
    - ref #none: register list view class for table "cms_field_conf" (table class extension)
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_list_class', 'en')
  ->setFields([
      'name' => 'RecordFieldsTableListView',
      'classname' => '\ChameleonSystem\CoreBundle\Bridge\Chameleon\ListManager\RecordFieldsTableListView',
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_field_conf'),
      'id' => '08c92779-b07e-b96e-64e0-add7bff6097d',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'en')
  ->setFields([
      // 'name' => 'cms_field_conf',
      'cms_tbl_list_class_id' => '08c92779-b07e-b96e-64e0-add7bff6097d', // prev.: ''
  ])
  ->setWhereEquals([
      'id' => '44',
  ])
;
TCMSLogChange::update(__LINE__, $data);

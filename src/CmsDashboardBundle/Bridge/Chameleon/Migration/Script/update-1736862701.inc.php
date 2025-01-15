<h1>Build #1736862701</h1>
<h2>Date: 2025-01-14</h2>
<div class="changelog">
    - ref #65182: add dashboard_widget_config field to cms_user
</div>
<?php


$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_user'),
      'translation' => 'Dashboard Widget Configuration',
      'name' => 'dashboard_widget_config',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_TEXT'), // prev.: '34'
      'modifier' => 'hidden', // prev.: 'none'
      'position' => '2179', // prev.: '0'
      'id' => '75f0144f-edee-6c26-50e9-66942c2a509c',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$query ="ALTER TABLE `cms_user`
                        ADD `dashboard_widget_config` LONGTEXT NOT NULL COMMENT 'Dashboard Widget Konfiguration: '";
TCMSLogChange::RunQuery(__LINE__, $query);


$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'translation' => 'Dashboard Widget Konfiguration', // prev.: ''
  ])
  ->setWhereEquals([
      'id' => '75f0144f-edee-6c26-50e9-66942c2a509c',
  ])
;
TCMSLogChange::update(__LINE__, $data);

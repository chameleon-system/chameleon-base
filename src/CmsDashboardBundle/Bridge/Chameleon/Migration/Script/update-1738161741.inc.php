<h1>Build #1738161741</h1>
<h2>Date: 2025-01-29</h2>
<div class="changelog">
    - ref #65684: add new field 'dashboard_bg' to cms_config
</div>
<?php
$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_config'),
      'name' => 'dashboard_bg', // prev.: 'new_field'
      'translation' => 'Dashboard Background Image',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_EXTENDEDTABLELIST_MEDIA_CROP'), // prev.: '34'
      'cms_tbl_field_tab' => 'fbb3e97d-25e1-7756-ec44-4a9afbd09a13', // prev.: ''
      'id' => 'd43f4a6e-c620-726f-5e62-e095caece5a2',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$query = "ALTER TABLE `cms_config`
                        ADD `dashboard_bg` CHAR(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Dashboard Hintergrundbild: '";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'translation' => 'Dashboard Hintergrundbild', // prev.: ''
      'position' => '2180', // prev.: '0'
  ])
  ->setWhereEquals([
      'id' => 'd43f4a6e-c620-726f-5e62-e095caece5a2',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$query = 'ALTER TABLE `cms_config` ADD `dashboard_bg_image_crop_id` CHAR(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL';
TCMSLogChange::RunQuery(__LINE__, $query);

$query = 'ALTER TABLE `cms_config` ADD INDEX `dashboard_bg` (`dashboard_bg`)';
TCMSLogChange::RunQuery(__LINE__, $query);

$query = 'ALTER TABLE `cms_config` ADD INDEX ( `dashboard_bg_image_crop_id` )';
TCMSLogChange::RunQuery(__LINE__, $query);

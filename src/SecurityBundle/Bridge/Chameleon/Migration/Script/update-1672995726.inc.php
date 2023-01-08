<h1>Build #1672995726</h1>
<h2>Date: 2023-01-06</h2>
<div class="changelog">
    - 59178 - add google login
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'name' => 'google_id',
      'translation' => 'Google User ID',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING'),
      'cms_tbl_field_tab' => '',
      'isrequired' => '0',
      'fieldclass' => '',
      'modifier' => 'readonly',
      'field_default_value' => '',
      'length_set' => '',
      'fieldtype_config' => '',
      'restrict_to_groups' => '0',
      'field_width' => '0',
      'position' => '2175',
      '049_helptext' => '',
      'row_hexcolor' => '',
      'is_translatable' => '0',
      'validation_regex' => '',
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_user'),
      'fieldclass_subtype' => '',
      'class_type' => 'Core',
      'id' => '31435f1d-78a5-2cd4-8ee7-59de772d107f',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$query ="ALTER TABLE `cms_user`
                        ADD `google_id` VARCHAR(255) NOT NULL COMMENT 'Google User ID: '";
TCMSLogChange::RunQuery(__LINE__, $query);
$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf_index', 'en')
  ->setFields([
      'name' => 'google_id',
      'definition' => 'google_id',
      'type' => 'INDEX',
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_user'),
      'id' => 'ef7ed2d7-eadd-ee08-44f9-56e9c5c91a9c',
  ])
;
TCMSLogChange::insert(__LINE__, $data);
$query ="ALTER TABLE `cms_user`
                        ADD INDEX  `google_id` ( google_id )";
TCMSLogChange::RunQuery(__LINE__, $query);


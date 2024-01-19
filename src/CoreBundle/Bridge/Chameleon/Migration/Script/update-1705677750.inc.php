<h1>Build #1705677750</h1>
<h2>Date: 2024-01-19</h2>
<div class="changelog">
    - ref #62140: add field to cms_tbl_display_orderfields to only use for sorting in the backend
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_tbl_display_orderfields'),
      'name' => 'only_backend',
      'translation' => 'Benutze das Feld nur zur Sortierung im CMS-Backend',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_BOOLEAN'),
      'cms_tbl_field_tab' => '',
      'isrequired' => '0',
      'fieldclass' => '',
      'fieldclass_subtype' => '',
      'class_type' => 'Core',
      'modifier' => 'none',
      'field_default_value' => '',
      'length_set' => '',
      'fieldtype_config' => '',
      'restrict_to_groups' => '0',
      'field_width' => '',
      'position' => '0', //position will be set below
      '049_helptext' => '',
      'row_hexcolor' => '',
      'is_translatable' => '0',
      'validation_regex' => '',
      'id' => TTools::GetUUID(),
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$query ="ALTER TABLE `cms_tbl_display_orderfields`
            ADD `only_backend` ENUM('0','1') DEFAULT '0' NOT NULL COMMENT 'Benutze das Feld nur zur Sortierung im CMS-Backend: ',
            ADD INDEX `only_backend` (`only_backend`)";
TCMSLogChange::RunQuery(__LINE__, $query);

TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('cms_tbl_display_orderfields'), 'only_backend', 'cms_tbl_conf_id');

<h1>Build #1549281421</h1>
<h2>Date: 2019-02-04</h2>
<div class="changelog">
    - Add CMS main menu table.
</div>
<?php

// Add cms_menu_category table.

$cmsMenuCategoryTableId = TCMSLogChange::createUnusedRecordId('cms_tbl_conf');

$query = "CREATE TABLE `cms_menu_category` (
                  `id` CHAR( 36 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
                  `cmsident` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Key is used so that records can be easily identified in Chameleon',
                  PRIMARY KEY ( `id` ),
                  UNIQUE (`cmsident`)
                ) ENGINE = InnoDB";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'en')
  ->setFields([
      'name' => 'cms_menu_category',
      'dbobject_type' => 'Customer',
      'translation' => 'CMS main menu',
      'engine' => 'InnoDB',
      'list_query' => '',
      'cms_content_box_id' => '4',
      'only_one_record_tbl' => '0',
      'is_multilanguage' => '0',
      'is_workflow' => '0',
      'locking_active' => '0',
      'changelog_active' => '0',
      'revision_management_active' => '0',
      'name_column' => '',
      'name_column_callback' => '',
      'display_column' => '',
      'display_column_callback' => '',
      'list_group_field' => '',
      'list_group_field_header' => '',
      'list_group_field_column' => '',
      'cms_tbl_list_class_id' => '',
      'table_editor_class' => '',
      'table_editor_class_subtype' => '',
      'table_editor_class_type' => 'Core',
      'icon_list' => '',
      'show_previewbutton' => '0',
      'cms_tpl_page_id' => '',
      'rename_on_copy' => '0',
      'cms_usergroup_id' => TCMSLogChange::GetUserGroupIdByKey('cms_admin'),
      'notes' => '',
      'frontend_auto_cache_clear_enabled' => '1',
      'dbobject_extend_class' => 'TCMSRecord',
      'dbobject_extend_subtype' => 'dbobjects',
      'dbobject_extend_type' => 'Core',
      'auto_limit_results' => '-1',
      'icon_font_css_class' => 'fas fa-project-diagram',
      'id' => $cmsMenuCategoryTableId,
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
  ->setFields([
      'translation' => 'CMS Hauptmenü',
  ])
  ->setWhereEquals([
      'id' => $cmsMenuCategoryTableId,
  ])
;
TCMSLogChange::update(__LINE__, $data);

// Set permissions.

TCMSLogChange::SetTableRolePermissions('cms_admin', 'cms_menu_category', false, [
    0,
    1,
    2,
    3,
]);

// Add name field.

$nameFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'cms_tbl_conf_id' => $cmsMenuCategoryTableId,
      'name' => 'name',
      'translation' => 'Name',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING'),
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
      'position' => '0',
      '049_helptext' => '',
      'row_hexcolor' => '',
      'is_translatable' => '0',
      'validation_regex' => '',
      'id' => $nameFieldId,
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$query = 'ALTER TABLE `cms_menu_category` ADD `name` VARCHAR(255) NOT NULL';
TCMSLogChange::RunQuery(__LINE__, $query);

TCMSLogChange::makeFieldMultilingual('cms_menu_category', 'name');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'name' => 'name',
      'translation' => 'Name',
  ])
  ->setWhereEquals([
      'id' => $nameFieldId,
  ])
;
TCMSLogChange::update(__LINE__, $data);

// Add position field.

$positionFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'cms_tbl_conf_id' => $cmsMenuCategoryTableId,
      'name' => 'position',
      'translation' => 'Position',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_SORTORDER'),
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
      'position' => '0',
      '049_helptext' => '',
      'row_hexcolor' => '',
      'is_translatable' => '0',
      'validation_regex' => '',
      'id' => $positionFieldId,
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$query = 'ALTER TABLE `cms_menu_category` ADD `position` INT NOT NULL';
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'name' => 'position',
      'translation' => 'Position',
  ])
  ->setWhereEquals([
      'id' => $positionFieldId,
  ])
;
TCMSLogChange::update(__LINE__, $data);

// Add system_name field.

$systemNameFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'cms_tbl_conf_id' => $cmsMenuCategoryTableId,
        'name' => 'system_name',
        'translation' => 'System name',
        'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING'),
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
        'position' => '0',
        '049_helptext' => 'TODO',
        'row_hexcolor' => '',
        'is_translatable' => '0',
        'validation_regex' => '',
        'id' => $systemNameFieldId,
    ])
;
TCMSLogChange::insert(__LINE__, $data);

$query = 'ALTER TABLE `cms_menu_category`
                        ADD `system_name` VARCHAR(255) NOT NULL';
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        'translation' => 'Systemname',
        '049_helptext' => 'TODO',
    ])
    ->setWhereEquals([
        'id' => $systemNameFieldId,
    ])
;
TCMSLogChange::update(__LINE__, $data);

$query = 'ALTER TABLE `cms_menu_category` ADD INDEX ( `position` )';
TCMSLogChange::RunQuery(__LINE__, $query);

// Add field cms_menu_item

$cmsMenuItemFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'cms_tbl_conf_id' => $cmsMenuCategoryTableId,
        'name' => 'cms_menu_item',
        'translation' => 'Menu items',
        'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_PROPERTY'),
        'cms_tbl_field_tab' => '',
        'isrequired' => '0',
        'fieldclass' => '',
        'fieldclass_subtype' => '',
        'class_type' => 'Core',
        'modifier' => 'none',
        'field_default_value' => 'cms_menu_item',
        'length_set' => '',
        'fieldtype_config' => 'bOpenOnLoad=true',
        'restrict_to_groups' => '0',
        'field_width' => '',
        'position' => '0',
        '049_helptext' => '',
        'row_hexcolor' => '',
        'is_translatable' => '0',
        'validation_regex' => '',
        'id' => $cmsMenuItemFieldId,
    ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        'translation' => 'Menüeinträge',
    ])
    ->setWhereEquals([
        'id' => $cmsMenuItemFieldId,
    ])
;
TCMSLogChange::update(__LINE__, $data);

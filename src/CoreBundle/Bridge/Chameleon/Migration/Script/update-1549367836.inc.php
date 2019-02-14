<h1>Build #1549367836</h1>
<h2>Date: 2019-02-05</h2>
<div class="changelog">
    - Add CMS main menu item table.
</div>
<?php

// Add cms_menu_item table.

$cmsMenuItemTableId = TCMSLogChange::createUnusedRecordId('cms_tbl_conf');

$query = "CREATE TABLE `cms_menu_item` (
                  `id` CHAR( 36 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
                  `cmsident` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Key is used so that records can be easily identified in Chameleon',
                  PRIMARY KEY ( `id` ),
                  UNIQUE (`cmsident`)
                ) ENGINE = InnoDB";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'en')
  ->setFields([
      'name' => 'cms_menu_item',
      'dbobject_type' => 'Customer',
      'translation' => 'CMS main menu items',
      'engine' => 'InnoDB',
      'list_query' => '',
      'cms_content_box_id' => '',
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
      'cms_usergroup_id' => '6',
      'notes' => '',
      'frontend_auto_cache_clear_enabled' => '1',
      'dbobject_extend_class' => 'TCMSRecord',
      'dbobject_extend_subtype' => 'dbobjects',
      'dbobject_extend_type' => 'Core',
      'auto_limit_results' => '-1',
      'icon_font_css_class' => '',
      'id' => $cmsMenuItemTableId,
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
  ->setFields([
      'translation' => 'CMS Hauptmenüpunkte',
  ])
  ->setWhereEquals([
      'id' => $cmsMenuItemTableId,
  ])
;
TCMSLogChange::update(__LINE__, $data);

// Set permissions.

TCMSLogChange::SetTableRolePermissions('cms_admin', 'cms_menu_item', false, [
    0,
    1,
    2,
    3,
]);

// Add name field.

$nameFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'cms_tbl_conf_id' => $cmsMenuItemTableId,
      'name' => 'name',
      'translation' => '',
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

$query = 'ALTER TABLE `cms_menu_item`
                        ADD `name` VARCHAR(255) NOT NULL';
TCMSLogChange::RunQuery(__LINE__, $query);

TCMSLogChange::makeFieldMultilingual('cms_menu_item', 'name');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'translation' => 'Name',
  ])
  ->setWhereEquals([
      'id' => $nameFieldId,
  ])
;
TCMSLogChange::update(__LINE__, $data);

// Add target field.

$targetFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'cms_tbl_conf_id' => $cmsMenuItemTableId,
      'name' => 'target',
      'translation' => '',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_EXTENDEDMULTITABLELIST'),
      'cms_tbl_field_tab' => '',
      'isrequired' => '0',
      'fieldclass' => '',
      'fieldclass_subtype' => '',
      'class_type' => 'Core',
      'modifier' => 'none',
      'field_default_value' => '',
      'length_set' => '',
      'fieldtype_config' => 'sTables=cms_tbl_conf,cms_module,cms_menu_custom_item',
      'restrict_to_groups' => '0',
      'field_width' => '',
      'position' => '0',
      '049_helptext' => '',
      'row_hexcolor' => '',
      'is_translatable' => '0',
      'validation_regex' => '',
      'id' => $targetFieldId,
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$query = 'ALTER TABLE `cms_menu_item`
                        ADD `target` CHAR(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL';
TCMSLogChange::RunQuery(__LINE__, $query);

$query = 'ALTER TABLE `cms_menu_item`
                         ADD `target_table_name` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL';
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'translation' => 'Ziel',
  ])
  ->setWhereEquals([
      'id' => $targetFieldId,
  ])
;
TCMSLogChange::update(__LINE__, $data);

$query = 'ALTER TABLE `cms_menu_item` ADD INDEX ( `target` )';
TCMSLogChange::RunQuery(__LINE__, $query);

$query = 'ALTER TABLE  `cms_menu_item` ADD INDEX `target_combined` (  `target_table_name` ,  `target` )';
TCMSLogChange::RunQuery(__LINE__, $query);

// Add icon_font_css_class field.

$iconFontCssClassFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'cms_tbl_conf_id' => $cmsMenuItemTableId,
      'name' => 'icon_font_css_class',
      'translation' => '',
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
      '049_helptext' => 'The field is used to display a font icon next to the menu item. Fill in the class name here, for example for Font Awesome: fas fa-check',
      'row_hexcolor' => '',
      'is_translatable' => '0',
      'validation_regex' => '',
      'id' => $iconFontCssClassFieldId,
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$query = 'ALTER TABLE `cms_menu_item`
                        ADD `icon_font_css_class` VARCHAR(255) NOT NULL';
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'translation' => 'Icon Font CSS-Klasse',
      '049_helptext' => 'In diesem Feld kann ein Font-Icon angegeben werden, das neben dem Menüeintrag dargestellt wird. Tragen Sie hier den Klassennamen ein, z.B. für Font Awesome: fas fa-check',
  ])
  ->setWhereEquals([
      'id' => $iconFontCssClassFieldId,
  ])
;
TCMSLogChange::update(__LINE__, $data);

// Add position field.

$positionFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'cms_tbl_conf_id' => $cmsMenuItemTableId,
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

$query = 'ALTER TABLE `cms_menu_item` ADD `position` INT NOT NULL';
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

// Add cms_menu_category_id field

$cmsMenuCategoryIdFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'cms_tbl_conf_id' => $cmsMenuItemTableId,
        'name' => 'cms_menu_category_id',
        'translation' => 'CMS main menu category',
        'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_PROPERTY_PARENT_ID'),
        'cms_tbl_field_tab' => '',
        'isrequired' => '0',
        'fieldclass' => '',
        'fieldclass_subtype' => '',
        'class_type' => 'Core',
        'modifier' => 'none',
        'field_default_value' => '',
        'length_set' => '',
        'fieldtype_config' => 'bAllowEdit=true',
        'restrict_to_groups' => '0',
        'field_width' => '',
        'position' => '0',
        '049_helptext' => '',
        'row_hexcolor' => '',
        'is_translatable' => '0',
        'validation_regex' => '',
        'id' => $cmsMenuCategoryIdFieldId,
    ])
;
TCMSLogChange::insert(__LINE__, $data);

$query = 'ALTER TABLE `cms_menu_item`
                        ADD `cms_menu_category_id` CHAR(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL';
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        'translation' => 'CMS Hauptmenü-Kategorie',
    ])
    ->setWhereEquals([
        'id' => $cmsMenuCategoryIdFieldId,
    ])
;
TCMSLogChange::update(__LINE__, $data);

$query = 'ALTER TABLE `cms_menu_item` ADD INDEX ( `cms_menu_category_id` )';
TCMSLogChange::RunQuery(__LINE__, $query);

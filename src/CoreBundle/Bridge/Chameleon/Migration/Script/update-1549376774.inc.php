<h1>Build #1549376774</h1>
<h2>Date: 2019-02-05</h2>
<div class="changelog">
    - Add cms_menu_custom_item table.
</div>
<?php

// Add cms_menu_custom_item table.

$cmsMenuCustomItemsTableId = TCMSLogChange::createUnusedRecordId('cms_tbl_conf');

$query = "CREATE TABLE `cms_menu_custom_item` (
                  `id` CHAR( 36 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
                  `cmsident` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Key is used so that records can be easily identified in Chameleon',
                  PRIMARY KEY ( `id` ),
                  UNIQUE (`cmsident`)
                ) ENGINE = InnoDB";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'en')
  ->setFields([
      'name' => 'cms_menu_custom_item',
      'dbobject_type' => 'Customer',
      'translation' => 'CMS main menu - custom items',
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
      'cms_usergroup_id' => '6',
      'notes' => '',
      'frontend_auto_cache_clear_enabled' => '1',
      'dbobject_extend_class' => 'TCMSRecord',
      'dbobject_extend_subtype' => 'dbobjects',
      'dbobject_extend_type' => 'Core',
      'auto_limit_results' => '-1',
      'icon_font_css_class' => 'fas fa-project-diagram',
      'id' => $cmsMenuCustomItemsTableId,
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
  ->setFields([
      'translation' => 'CMS Hauptmenü - eigene Menüpunkte',
  ])
  ->setWhereEquals([
      'id' => $cmsMenuCustomItemsTableId,
  ])
;
TCMSLogChange::update(__LINE__, $data);

// Set permissions.

TCMSLogChange::SetTableRolePermissions('cms_admin', 'cms_menu_custom_item', false, [
    0,
    1,
    2,
    3,
]);

// Add name field.

$nameFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'cms_tbl_conf_id' => $cmsMenuCustomItemsTableId,
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

$query = 'ALTER TABLE `cms_menu_custom_item`
                        ADD `name` VARCHAR(255) NOT NULL';
TCMSLogChange::RunQuery(__LINE__, $query);

TCMSLogChange::makeFieldMultilingual('cms_menu_custom_item', 'name');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'translation' => 'Name',
  ])
  ->setWhereEquals([
      'id' => $nameFieldId,
  ])
;
TCMSLogChange::update(__LINE__, $data);

// Add url field

$urlFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'cms_tbl_conf_id' => $cmsMenuCustomItemsTableId,
      'name' => 'url',
      'translation' => 'Target URL',
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
      'id' => $urlFieldId,
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$query = 'ALTER TABLE `cms_menu_custom_item`
                        ADD `url` VARCHAR(255) NOT NULL';
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'name' => 'url',
      'translation' => 'Ziel-URL',
  ])
  ->setWhereEquals([
      'id' => $urlFieldId,
  ])
;
TCMSLogChange::update(__LINE__, $data);

// Add cms_right_mlt field.

$cmsRightMltFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'cms_tbl_conf_id' => $cmsMenuCustomItemsTableId,
      'name' => 'cms_right_mlt',
      'translation' => 'Required rights',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_MULTITABLELIST_CHECKBOXES'),
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
      'id' => $cmsRightMltFieldId,
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$query = 'ALTER TABLE `cms_menu_custom_item`
                        ADD `cms_right_mlt` VARCHAR(255) NOT NULL';
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'translation' => 'Benötigte Berechtigungen',
  ])
  ->setWhereEquals([
      'id' => $cmsRightMltFieldId,
  ])
;
TCMSLogChange::update(__LINE__, $data);

$query = "CREATE TABLE `cms_menu_custom_item_cms_right_mlt` (
                  `source_id` CHAR( 36 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
                  `target_id` CHAR( 36 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
                  `entry_sort` int(11) NOT NULL default '0',
                  PRIMARY KEY ( `source_id` , `target_id` ),
                  INDEX (target_id),
                  INDEX (entry_sort)
                ) ENGINE = InnoDB";
TCMSLogChange::RunQuery(__LINE__, $query);

// Add new right for navigation editing

$rightId = TCMSLogChange::createUnusedRecordId('cms_right');

$data = TCMSLogChange::createMigrationQueryData('cms_right', 'en')
    ->setFields([
        'name' => 'navigation_edit',
        '049_trans' => 'edit site navigation',
        'id' => $rightId,
    ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_right', 'de')
    ->setFields([
        '049_trans' => 'Seitennavigation editieren',
    ])
    ->setWhereEquals([
        'id' => $rightId,
    ])
;
TCMSLogChange::update(__LINE__, $data);

// Assign the new right to all users that currently have the right to edit pages.

$query = 'SELECT `target_id` FROM `cms_tbl_conf_cms_role1_mlt` WHERE `source_id` = ?';
$databaseConnection = TCMSLogChange::getDatabaseConnection();
$rows = $databaseConnection->fetchAll($query, [
        TCMSLogChange::GetTableId('cms_tpl_page'),
]);
foreach ($rows as $row) {
    $data = TCMSLogChange::createMigrationQueryData('cms_role_cms_right_mlt', 'en')
        ->setFields([
            'source_id' => $row['target_id'],
            'target_id' => $rightId,
            'entry_sort' => '0',
        ])
    ;
    TCMSLogChange::insert(__LINE__, $data);
}

// Add navigation menu item.

$navigationMenuItemId = TCMSLogChange::createUnusedRecordId('cms_menu_custom_item');

$data = TCMSLogChange::createMigrationQueryData('cms_menu_custom_item', 'en')
  ->setFields([
      'name' => 'Navigation',
      'url' => '/cms?pagedef=CMSModulePageTree&table=cms_tpl_page&noassign=1',
      'id' => $navigationMenuItemId,
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_menu_custom_item', 'de')
  ->setFields([
      'name' => 'Navigation',
  ])
  ->setWhereEquals([
      'id' => $navigationMenuItemId,
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_menu_custom_item_cms_right_mlt', 'en')
    ->setFields([
        'source_id' => $navigationMenuItemId,
        'target_id' => $rightId,
        'entry_sort' => '0',
    ])
;
TCMSLogChange::insert(__LINE__, $data);

// Add media manager menu item.

$mediaManagerMenuItemId = TCMSLogChange::createUnusedRecordId('cms_menu_custom_item');

$data = TCMSLogChange::createMigrationQueryData('cms_menu_custom_item', 'en')
    ->setFields([
        'name' => 'Media',
        'url' => '/cms?pagedef=mediaManager&_pagedefType=@ChameleonSystemMediaManagerBundle',
        'id' => $mediaManagerMenuItemId,
    ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_menu_custom_item', 'de')
    ->setFields([
        'name' => 'Medien',
    ])
    ->setWhereEquals([
        'id' => $mediaManagerMenuItemId,
    ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_menu_custom_item_cms_right_mlt', 'en')
    ->setFields([
        'source_id' => $mediaManagerMenuItemId,
        'target_id' => TCMSLogChange::GetUserRightIdByKey('cms_image_pool_upload'),
        'entry_sort' => '0',
    ])
;
TCMSLogChange::insert(__LINE__, $data);

// Add document manager menu item.

$documentManagerMenuItemId = TCMSLogChange::createUnusedRecordId('cms_menu_custom_item');

$data = TCMSLogChange::createMigrationQueryData('cms_menu_custom_item', 'en')
    ->setFields([
        'name' => 'Documents',
        'url' => '/cms?pagedef=CMSDocumentManagerFull',
        'id' => $documentManagerMenuItemId,
    ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_menu_custom_item', 'de')
    ->setFields([
        'name' => 'Dokumente',
    ])
    ->setWhereEquals([
        'id' => $documentManagerMenuItemId,
    ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_menu_custom_item_cms_right_mlt', 'en')
  ->setFields([
      'source_id' => $documentManagerMenuItemId,
      'target_id' => TCMSLogChange::GetUserRightIdByKey('cms_data_pool_upload'),
      'entry_sort' => '0',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

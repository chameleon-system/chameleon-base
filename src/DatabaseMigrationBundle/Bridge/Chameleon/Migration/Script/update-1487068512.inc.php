<h1>update - Build #1487068512</h1>
<h2>Date: 2017-02-14</h2>
<div class="changelog">
    - integrate new update counter tables
</div>
<?php

  /*
   * The tables themselves needed to be created manually as described in the migration guide, as they are needed in the
   * counter migration step before running updates. In that step the tables are only used in "raw" form so that we can
   * hand in these table definitions and permission settings afterwards. This minimizes the amount of work to do manually
   * without having to resort to evil tricks.
   */

  $migrationCounterTableId = TCMSLogChange::createUnusedRecordId('cms_tbl_conf');

  $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'en')
      ->setFields(array(
          'name' => 'cms_migration_counter',
          'dbobject_type' => 'Customer',
          'translation' => 'Update Index',
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
          'icon_list' => 'page_text.gif',
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
          'id' => $migrationCounterTableId,
      ))
  ;
  TCMSLogChange::insert(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
      ->setFields(array(
          'translation' => 'Update-Verzeichnis',
      ))
      ->setWhereEquals(array(
          'id' => $migrationCounterTableId,
      ))
  ;
  TCMSLogChange::update(__LINE__, $data);

  $fieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');
  $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
      ->setFields(array(
          'cms_tbl_conf_id' => $migrationCounterTableId,
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
          'restrict_to_groups' => '',
          'field_width' => '',
          'position' => '0',
          '049_helptext' => '',
          'row_hexcolor' => '',
          'is_translatable' => '0',
          'validation_regex' => '',
          'id' => $fieldId,
      ))
  ;
  TCMSLogChange::insert(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
      ->setFields(array(
          'translation' => 'Name',
      ))
      ->setWhereEquals(array(
          'id' => $fieldId,
      ))
  ;
  TCMSLogChange::update(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf_cms_role_mlt', 'en')
      ->setFields(array(
          'source_id' => $migrationCounterTableId,
          'target_id' => '1',
          'entry_sort' => '0',
      ))
  ;
  TCMSLogChange::insert(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf_cms_role1_mlt', 'en')
      ->setFields(array(
          'source_id' => $migrationCounterTableId,
          'target_id' => '1',
          'entry_sort' => '0',
      ))
  ;
  TCMSLogChange::insert(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf_cms_role2_mlt', 'en')
      ->setFields(array(
          'source_id' => $migrationCounterTableId,
          'target_id' => '1',
          'entry_sort' => '0',
      ))
  ;
  TCMSLogChange::insert(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf_cms_role3_mlt', 'en')
      ->setFields(array(
          'source_id' => $migrationCounterTableId,
          'target_id' => '1',
          'entry_sort' => '0',
      ))
  ;
  TCMSLogChange::insert(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf_cms_role6_mlt', 'en')
      ->setFields(array(
          'source_id' => $migrationCounterTableId,
          'target_id' => '1',
          'entry_sort' => '0',
      ))
  ;
  TCMSLogChange::insert(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf_cms_role4_mlt', 'en')
      ->setFields(array(
          'source_id' => $migrationCounterTableId,
          'target_id' => '1',
          'entry_sort' => '0',
      ))
  ;
  TCMSLogChange::insert(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf_cms_role7_mlt', 'en')
      ->setFields(array(
          'source_id' => $migrationCounterTableId,
          'target_id' => '1',
          'entry_sort' => '0',
      ))
  ;
  TCMSLogChange::insert(__LINE__, $data);

  $migrationFileTableId = TCMSLogChange::createUnusedRecordId('cms_tbl_conf');

  $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'en')
      ->setFields(array(
          'name' => 'cms_migration_file',
          'dbobject_type' => 'Customer',
          'translation' => 'Update Data',
          'engine' => 'InnoDB',
          'list_query' => 'SELECT `id`, `cmsident`, `build_number` FROM `cms_migration_file`',
          'cms_content_box_id' => '',
          'only_one_record_tbl' => '0',
          'is_multilanguage' => '0',
          'is_workflow' => '0',
          'locking_active' => '0',
          'changelog_active' => '0',
          'revision_management_active' => '0',
          'name_column' => 'build_number',
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
          'id' => $migrationFileTableId,
      ))
  ;
  TCMSLogChange::insert(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
      ->setFields(array(
          'translation' => 'Update-Daten',
      ))
      ->setWhereEquals(array(
          'id' => $migrationFileTableId,
      ))
  ;
  TCMSLogChange::update(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_orderfields', 'en')
      ->setFields(array(
          'name' => '`build_number`',
          'sort_order_direction' => 'ASC',
          'position' => '90',
          'cms_tbl_conf_id' => $migrationFileTableId,
          'id' => TCMSLogChange::createUnusedRecordId('cms_tbl_display_orderfields'),
      ))
  ;
  TCMSLogChange::insert(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
      ->setFields(array(
          'translation' => 'Name',
      ))
      ->setWhereEquals(array(
          'id' => $fieldId,
      ))
  ;
  TCMSLogChange::update(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf_cms_role_mlt', 'en')
      ->setFields(array(
          'source_id' => $migrationFileTableId,
          'target_id' => '1',
          'entry_sort' => '0',
      ))
  ;
  TCMSLogChange::insert(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf_cms_role1_mlt', 'en')
      ->setFields(array(
          'source_id' => $migrationFileTableId,
          'target_id' => '1',
          'entry_sort' => '0',
      ))
  ;
  TCMSLogChange::insert(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf_cms_role2_mlt', 'en')
      ->setFields(array(
          'source_id' => $migrationFileTableId,
          'target_id' => '1',
          'entry_sort' => '0',
      ))
  ;
  TCMSLogChange::insert(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf_cms_role3_mlt', 'en')
      ->setFields(array(
          'source_id' => $migrationFileTableId,
          'target_id' => '1',
          'entry_sort' => '0',
      ))
  ;
  TCMSLogChange::insert(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf_cms_role6_mlt', 'en')
      ->setFields(array(
          'source_id' => $migrationFileTableId,
          'target_id' => '1',
          'entry_sort' => '0',
      ))
  ;
  TCMSLogChange::insert(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf_cms_role4_mlt', 'en')
      ->setFields(array(
          'source_id' => $migrationFileTableId,
          'target_id' => '1',
          'entry_sort' => '0',
      ))
  ;
  TCMSLogChange::insert(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf_cms_role7_mlt', 'en')
      ->setFields(array(
          'source_id' => $migrationFileTableId,
          'target_id' => '1',
          'entry_sort' => '0',
      ))
  ;
  TCMSLogChange::insert(__LINE__, $data);

  $fieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');

  $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
      ->setFields(array(
          'cms_tbl_conf_id' => $migrationFileTableId,
          'name' => 'build_number',
          'translation' => 'Build number',
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
          'restrict_to_groups' => '',
          'field_width' => '',
          'position' => '0',
          '049_helptext' => '',
          'row_hexcolor' => '',
          'is_translatable' => '0',
          'validation_regex' => '',
          'id' => $fieldId,
      ))
  ;
  TCMSLogChange::insert(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
      ->setFields(array(
          'translation' => 'Build-Nummer',
      ))
      ->setWhereEquals(array(
          'id' => $fieldId,
      ))
  ;
  TCMSLogChange::update(__LINE__, $data);

  $fieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');

  $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
      ->setFields(array(
          'cms_tbl_conf_id' => $migrationCounterTableId,
          'name' => 'cms_migration_file',
          'translation' => 'Update data',
          'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_PROPERTY'),
          'cms_tbl_field_tab' => '',
          'isrequired' => '0',
          'fieldclass' => '',
          'fieldclass_subtype' => '',
          'class_type' => 'Core',
          'modifier' => 'none',
          'field_default_value' => '',
          'length_set' => '',
          'fieldtype_config' => 'bOpenOnLoad=true',
          'restrict_to_groups' => '',
          'field_width' => '',
          'position' => '0',
          '049_helptext' => '',
          'row_hexcolor' => '',
          'is_translatable' => '0',
          'validation_regex' => '',
          'id' => $fieldId,
      ))
  ;
  TCMSLogChange::insert(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
      ->setFields(array(
          'translation' => 'Update-Daten',
      ))
      ->setWhereEquals(array(
          'id' => $fieldId,
      ))
  ;
  TCMSLogChange::update(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_module', 'en')
      ->setFields(array(
          'name' => 'Update execution',
      ))
      ->setWhereEquals(array(
          'uniquecmsname' => 'cmsupdatemanager',
      ))
  ;
  TCMSLogChange::update(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_module', 'de')
      ->setFields(array(
          'name' => 'Updates ausführen',
      ))
      ->setWhereEquals(array(
          'uniquecmsname' => 'cmsupdatemanager',
      ))
  ;
  TCMSLogChange::update(__LINE__, $data);

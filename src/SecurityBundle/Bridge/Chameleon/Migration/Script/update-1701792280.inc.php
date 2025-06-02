<h1>Build #1701792280</h1>
<h2>Date: 2023-12-05</h2>
<div class="changelog">
    -5 9178 - add google login
</div>
<?php

$tableExisis = TCMSLogChange::TableExists('cms_user_sso');
if ($tableExisis) {
    TCMSLogChange::getLogger()->info('Table cms_user_sso already exists. Skipping migration script.');

    return;
}

$query = "CREATE TABLE `cms_user_sso` (
                  `id` CHAR( 36 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
                  `cmsident` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Key is used so that records can be easily identified in Chameleon',
                  PRIMARY KEY ( `id` ),
                  UNIQUE (`cmsident`)
                ) ENGINE = InnoDB";
TCMSLogChange::RunQuery(__LINE__, $query);

$query = "ALTER TABLE `cms_user_sso` COMMENT 'CMS User SSO Ids: '";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'en')
  ->setFields([
      'name' => 'cms_user_sso',
      'translation' => 'CMS User SSO Ids',
      'engine' => 'InnoDB',
      'list_query' => '',
      'cms_content_box_id' => '',
      'only_one_record_tbl' => '0',
      'locking_active' => '0',
      'changelog_active' => '0',
      'name_column' => 'type',
      'name_column_callback' => '',
      'display_column' => '',
      'display_column_callback' => '',
      'list_group_field' => '',
      'list_group_field_header' => '',
      'list_group_field_column' => '',
      'cms_tbl_list_class_id' => '',
      'table_editor_class' => '',
      'show_previewbutton' => '0',
      'cms_tpl_page_id' => '',
      'rename_on_copy' => '0',
      'cms_usergroup_id' => '8',
      'notes' => 'Holds SSO ids for the user from different SSO services (such as google)',
      'frontend_auto_cache_clear_enabled' => '1',
      'dbobject_extend_class' => 'TCMSRecord',
      'auto_limit_results' => '-1',
      'icon_font_css_class' => '',
      'dbobject_type' => 'Customer',
      'is_multilanguage' => '0',
      'is_workflow' => '0',
      'revision_management_active' => '0',
      'table_editor_class_subtype' => '',
      'table_editor_class_type' => 'Core',
      'icon_list' => '',
      'dbobject_extend_subtype' => 'dbobjects',
      'dbobject_extend_type' => 'Core',
      'id' => 'c9c8c34b-76ad-3bab-c907-129365cdf9bd',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
    ->setFields([
        'translation' => 'CMS User SSO Ids',
        'notes' => 'Enthält SSO-IDs für den Benutzer von verschiedenen SSO-Diensten (z. B. Google).',
    ])->setWhereEquals(['id' => 'c9c8c34b-76ad-3bab-c907-129365cdf9bd'])
;
TCMSLogChange::update(__LINE__, $data);
TCMSLogChange::SetTableRolePermissions('cms_admin', 'cms_user_sso', false, [0, 1]);
TCMSLogChange::SetTableRolePermissions('cms_manager', 'cms_user_sso', false, [2, 3]);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'name' => 'cms_user_id',
      'translation' => 'Belongs to',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_PROPERTY_PARENT_ID'),
      'cms_tbl_field_tab' => '',
      'isrequired' => '0',
      'fieldclass' => '',
      'modifier' => 'none',
      'field_default_value' => '',
      'length_set' => '',
      'fieldtype_config' => '',
      'restrict_to_groups' => '0',
      'field_width' => '0',
      'position' => '1',
      '049_helptext' => '',
      'row_hexcolor' => '',
      'is_translatable' => '0',
      'validation_regex' => '',
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_user_sso'),
      'fieldclass_subtype' => '',
      'class_type' => 'Core',
      'id' => 'de1dc57b-2070-8ee9-a88c-4d7d18d4242e',
  ])
;
TCMSLogChange::insert(__LINE__, $data);
$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        'translation' => 'Gehört zu',
    ])->setWhereEquals(['id' => 'de1dc57b-2070-8ee9-a88c-4d7d18d4242e'])
;
TCMSLogChange::update(__LINE__, $data);
$query = "ALTER TABLE `cms_user_sso`
                        ADD `cms_user_id` CHAR(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Gehört zu: '";
TCMSLogChange::RunQuery(__LINE__, $query);
$query = 'ALTER TABLE `cms_user_sso` ADD INDEX `cms_user_id` (`cms_user_id`)';
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'name' => 'type',
      'translation' => 'SSO Service',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING'),
      'cms_tbl_field_tab' => '',
      'isrequired' => '0',
      'fieldclass' => '',
      'modifier' => 'none',
      'field_default_value' => '',
      'length_set' => '',
      'fieldtype_config' => '',
      'restrict_to_groups' => '0',
      'field_width' => '0',
      'position' => '2',
      '049_helptext' => '',
      'row_hexcolor' => '',
      'is_translatable' => '0',
      'validation_regex' => '',
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_user_sso'),
      'fieldclass_subtype' => '',
      'class_type' => 'Core',
      'id' => '217517dc-bbd9-8351-2827-97023b96663e',
  ])
;
TCMSLogChange::insert(__LINE__, $data);
$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        'translation' => 'SSO Service',
    ])->setWhereEquals(['id' => '217517dc-bbd9-8351-2827-97023b96663e'])
;
TCMSLogChange::update(__LINE__, $data);
$query = "ALTER TABLE `cms_user_sso`
                        ADD `type` VARCHAR(255) NOT NULL COMMENT 'SSO Service: '";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'name' => 'sso_id',
      'translation' => 'SSO ID',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING'),
      'cms_tbl_field_tab' => '',
      'isrequired' => '0',
      'fieldclass' => '',
      'modifier' => 'none',
      'field_default_value' => '',
      'length_set' => '',
      'fieldtype_config' => '',
      'restrict_to_groups' => '0',
      'field_width' => '0',
      'position' => '3',
      '049_helptext' => '',
      'row_hexcolor' => '',
      'is_translatable' => '0',
      'validation_regex' => '',
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_user_sso'),
      'fieldclass_subtype' => '',
      'class_type' => 'Core',
      'id' => 'eaf67226-5d7c-9a1b-407b-7af52ddaeb19',
  ])
;
TCMSLogChange::insert(__LINE__, $data);
$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        'translation' => 'SSO ID',
    ])->setWhereEquals(['id' => 'eaf67226-5d7c-9a1b-407b-7af52ddaeb19'])
;
TCMSLogChange::update(__LINE__, $data);
$query = "ALTER TABLE `cms_user_sso`
                        ADD `sso_id` VARCHAR(255) NOT NULL COMMENT 'SSO ID: '";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf_index', 'en')
    ->setFields([
        'name' => 'sso',
        'definition' => 'type,sso_id',
        'type' => 'INDEX',
        'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_user_sso'),
        'id' => 'bedbcb3f-ce48-f6d0-1695-30299fdc3919',
    ])
;
TCMSLogChange::insert(__LINE__, $data);
$query = 'ALTER TABLE `cms_user_sso`
                        ADD INDEX  `sso` ( `type`,`sso_id` )';
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'en')
    ->setFields([
        'title' => 'Type',
        'name' => '`cms_user_sso`.`type`',
        'db_alias' => 'type',
        'position' => '1',
        'width' => '-1',
        'align' => 'left',
        'callback_fnc' => '',
        'use_callback' => '0',
        'show_in_list' => '1',
        'show_in_sort' => '0',
        'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_user_sso'),
        'cms_translation_field_name' => '',
        'id' => '1cc06c9b-13d3-a488-103c-13b3b3156996',
    ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'de')
    ->setFields([
        'title' => 'Typ',
    ])
    ->setWhereEquals([
        'id' => '1cc06c9b-13d3-a488-103c-13b3b3156996',
    ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'en')
    ->setFields([
        'title' => 'SSO ID',
        'name' => '`cms_user_sso`.`sso_id`',
        'db_alias' => 'sso_id',
        'position' => '2',
        'width' => '-1',
        'align' => 'left',
        'callback_fnc' => '',
        'use_callback' => '0',
        'show_in_list' => '1',
        'show_in_sort' => '0',
        'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_user_sso'),
        'cms_translation_field_name' => '',
        'id' => 'b37194c1-38d6-5756-abde-c1eeee2b8106',
    ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'de')
    ->setFields([
        'title' => 'SSO ID',
    ])
    ->setWhereEquals([
        'id' => 'b37194c1-38d6-5756-abde-c1eeee2b8106',
    ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'name' => 'cms_user_sso',
        'translation' => 'SSO IDs',
        'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_PROPERTY'),
        'cms_tbl_field_tab' => '',
        'isrequired' => '0',
        'fieldclass' => '',
        'modifier' => 'none',
        'field_default_value' => '',
        'length_set' => '',
        'fieldtype_config' => '',
        'restrict_to_groups' => '0',
        'field_width' => '0',
        'position' => '2180',
        '049_helptext' => '',
        'row_hexcolor' => '',
        'is_translatable' => '0',
        'validation_regex' => '',
        'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_user'),
        'fieldclass_subtype' => '',
        'class_type' => 'Core',
        'id' => '8f38a3bb-2039-7934-931d-c58f459bbe61',
    ])
;
TCMSLogChange::insert(__LINE__, $data);
$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        'translation' => 'SSO IDs',
    ])->setWhereEquals(['id' => '8f38a3bb-2039-7934-931d-c58f459bbe61'])
;
TCMSLogChange::update(__LINE__, $data);
TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('cms_user'), 'cms_user_sso', 'allow_cms_login');

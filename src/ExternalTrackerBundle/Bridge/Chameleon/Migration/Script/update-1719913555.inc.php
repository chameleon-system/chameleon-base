<h1>Build #1719913555</h1>
<h2>Date: 2024-07-02</h2>
<div class="changelog">
    - ref #63934: add pkg_external_tracker table if missing
</div>
<?php

if (false === TCMSLogChange::TableExists('pkg_external_tracker')) {

    $query ="CREATE TABLE `pkg_external_tracker` (
                      `id` CHAR( 36 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
                      `cmsident` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Key is used so that records can be easily identified in Chameleon',
                      PRIMARY KEY ( `id` ),
                      UNIQUE (`cmsident`)
                    ) ENGINE = InnoDB";
    TCMSLogChange::RunQuery(__LINE__, $query);

    $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'en')
      ->setFields([
          'name' => 'pkg_external_tracker',
          'translation' => 'External Tracking-Services',
          'engine' => 'InnoDB',
          'list_query' => '',
          'only_one_record_tbl' => '0',
          'locking_active' => '0',
          'changelog_active' => '0',
          'name_column' => '',
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
          'cms_usergroup_id' => '7',
          'notes' => '',
          'frontend_auto_cache_clear_enabled' => '1',
          'dbobject_extend_class' => 'TCMSRecord',
          'auto_limit_results' => '-1',
          'icon_font_css_class' => 'fas fa-chart-line',
          'id' => '8bff1420-ef3f-c273-8e5b-3e9a87848fa0',
      ])
    ;
    TCMSLogChange::insert(__LINE__, $data);

    TCMSLogChange::SetTableRolePermissions('cms_manager', 'pkg_external_tracker', false, [0, 1, 2, 3, 6]);

    $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
      ->setFields([
//          'name' => 'pkg_external_tracker',
          'translation' => 'Externe Tracking-Dienste',
      ])
      ->setWhereEquals([
          'id' => '8bff1420-ef3f-c273-8e5b-3e9a87848fa0',
      ])
    ;
    TCMSLogChange::update(__LINE__, $data);

    $query ="ALTER TABLE `pkg_external_tracker` COMMENT 'Externe Tracking-Dienste: '";
    TCMSLogChange::RunQuery(__LINE__, $query);

    $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
      ->setFields([
          'cms_tbl_conf_id' => TCMSLogChange::GetTableId('pkg_external_tracker'),
          'name' => 'name',
          'translation' => 'Name',
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
          'position' => '3183',
          '049_helptext' => '',
          'row_hexcolor' => '',
          'is_translatable' => '0',
          'validation_regex' => '',
          'id' => '64b09648-0999-f12d-2d94-41fabb81e310',
      ])
    ;
    TCMSLogChange::insert(__LINE__, $data);

    $query ="ALTER TABLE `pkg_external_tracker`
                            ADD `name` VARCHAR(255) NOT NULL COMMENT 'Name: '";
    TCMSLogChange::RunQuery(__LINE__, $query);

    $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
      ->setFields([
          'translation' => 'Name',
      ])
      ->setWhereEquals([
          'id' => '64b09648-0999-f12d-2d94-41fabb81e310',
      ])
    ;
    TCMSLogChange::update(__LINE__, $data);

    $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
      ->setFields([
          'cms_tbl_conf_id' => TCMSLogChange::GetTableId('pkg_external_tracker'),
          'name' => 'active',
          'translation' => 'Active',
          'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_BOOLEAN'),
          'cms_tbl_field_tab' => '',
          'isrequired' => '0',
          'fieldclass' => '',
          'modifier' => 'none',
          'field_default_value' => '0',
          'length_set' => '',
          'fieldtype_config' => '',
          'restrict_to_groups' => '0',
          'field_width' => '0',
          'position' => '3184',
          '049_helptext' => '',
          'row_hexcolor' => '',
          'is_translatable' => '0',
          'validation_regex' => '',
          'id' => '30257021-67cc-439b-d70b-bed07963877c',
      ])
    ;
    TCMSLogChange::insert(__LINE__, $data);

    $query ="ALTER TABLE `pkg_external_tracker`
                            ADD `active` ENUM('0','1') DEFAULT '0' NOT NULL COMMENT 'Aktiv: '";
    TCMSLogChange::RunQuery(__LINE__, $query);

    $query ="ALTER TABLE `pkg_external_tracker` ADD INDEX `active` (`active`)";
    TCMSLogChange::RunQuery(__LINE__, $query);
    
    $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
      ->setFields([
//          'name' => 'active',
          'translation' => 'Aktiv',
      ])
      ->setWhereEquals([
          'id' => '30257021-67cc-439b-d70b-bed07963877c',
      ])
    ;
    TCMSLogChange::update(__LINE__, $data);

    $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
      ->setFields([
          'cms_tbl_conf_id' => TCMSLogChange::GetTableId('pkg_external_tracker'),
          'name' => 'identifier',
          'translation' => 'User/Website Tracking Code/ID',
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
          'position' => '3186',
          '049_helptext' => 'der vom Anbieter für die Seite vergebene Code',
          'row_hexcolor' => '',
          'is_translatable' => '0',
          'validation_regex' => '',
          'id' => 'b3795255-65d3-706f-cd4a-3796b0bcec2e',
      ])
    ;
    TCMSLogChange::insert(__LINE__, $data);

    $query ="ALTER TABLE `pkg_external_tracker`
                            ADD `identifier` VARCHAR(255) NOT NULL COMMENT 'Benutzer/Site-Code: der vom Anbieter für die Seite vergebene Code'";
    TCMSLogChange::RunQuery(__LINE__, $query);

    $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
      ->setFields([
//          'name' => 'identifier',
          'translation' => 'Benutzer/Site-Code',
      ])
      ->setWhereEquals([
          'id' => 'b3795255-65d3-706f-cd4a-3796b0bcec2e',
      ])
    ;
    TCMSLogChange::update(__LINE__, $data);


    $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
      ->setFields([
          'cms_tbl_conf_id' => TCMSLogChange::GetTableId('pkg_external_tracker'),
          'name' => 'test_identifier',
          'translation' => 'User/Website Tracking Code/ID in DEMOMODE',
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
          'position' => '3187',
          '049_helptext' => 'The code assigned by the provider for the page when the tracker is in demo mode, e.g. on stage and development systems (set chameleon_system_external_tracker.demo_mode to true).',
          'row_hexcolor' => '',
          'is_translatable' => '0',
          'validation_regex' => '',
          'id' => 'e9e42ae2-2a09-401b-4ff2-89753a54ea7d',
      ])
    ;
    TCMSLogChange::insert(__LINE__, $data);

    $query ="ALTER TABLE `pkg_external_tracker`
                            ADD `test_identifier` VARCHAR(255) NOT NULL COMMENT 'Benutzer/Site-Code im DEMOMODUS: Der vom Anbieter für die Seite vergebene Code, wenn der Tracker im Demo-Modus ist, also z.B. auf Stage und Entwicklungssystemen (chameleon_system_external_tracker.demo_mode auf true setzen).'";
    TCMSLogChange::RunQuery(__LINE__, $query);

    $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
      ->setFields([
//          'name' => 'test_identifier',
          'translation' => 'Benutzer/Site-Code im DEMOMODUS',
          '049_helptext' => 'Der vom Anbieter für die Seite vergebene Code, wenn der Tracker im Demo-Modus ist, also z.B. auf Stage und Entwicklungssystemen (chameleon_system_external_tracker.demo_mode auf true setzen).',
      ])
      ->setWhereEquals([
          'id' => 'e9e42ae2-2a09-401b-4ff2-89753a54ea7d',
      ])
    ;
    TCMSLogChange::update(__LINE__, $data);

    $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
      ->setFields([
          'cms_tbl_conf_id' => TCMSLogChange::GetTableId('pkg_external_tracker'),
          'name' => 'class',
          'translation' => 'Class',
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
          'position' => '3188',
          '049_helptext' => '',
          'row_hexcolor' => '',
          'is_translatable' => '0',
          'validation_regex' => '',
          'id' => '8aca0d5e-73e4-c189-7857-b90637f0387c',
      ])
    ;
    TCMSLogChange::insert(__LINE__, $data);

    $query ="ALTER TABLE `pkg_external_tracker`
                            ADD `class` VARCHAR(255) NOT NULL COMMENT 'Klasse: '";
    TCMSLogChange::RunQuery(__LINE__, $query);

    $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
      ->setFields([
//          'name' => 'class',
          'translation' => 'Klasse',
      ])
      ->setWhereEquals([
          'id' => '8aca0d5e-73e4-c189-7857-b90637f0387c',
      ])
    ;
    TCMSLogChange::update(__LINE__, $data);

    $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
      ->setFields([
          'cms_tbl_conf_id' => TCMSLogChange::GetTableId('pkg_external_tracker'),
          'name' => 'class_subtype',
          'translation' => 'Klassen Unterart (Pfad)',
          'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING'),
          'cms_tbl_field_tab' => '',
          'isrequired' => '0',
          'fieldclass' => '',
          'modifier' => 'hidden',
          'field_default_value' => '',
          'length_set' => '',
          'fieldtype_config' => '',
          'restrict_to_groups' => '0',
          'field_width' => '0',
          'position' => '3189',
          '049_helptext' => '@deprecated since 6.2.0 - field is no longer used, but preserved to ensure backwards compatibility',
          'row_hexcolor' => '',
          'is_translatable' => '0',
          'validation_regex' => '',
          'id' => '28d50b3b-749a-6f2f-82e6-214d6a2ff589',
      ])
    ;
    TCMSLogChange::insert(__LINE__, $data);

    $query ="ALTER TABLE `pkg_external_tracker`
                            ADD `class_subtype` VARCHAR(255) NOT NULL COMMENT 'Klassen Unterart (Pfad): @deprecated since 6.2.0 - field is no longer used, but preserved to ensure backwards compatibility'";
    TCMSLogChange::RunQuery(__LINE__, $query);

    $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
      ->setFields([
          'cms_tbl_conf_id' => TCMSLogChange::GetTableId('pkg_external_tracker'),
          'name' => 'class_type',
          'translation' => 'Klassenart',
          'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_LIST'),
          'cms_tbl_field_tab' => '',
          'isrequired' => '0',
          'fieldclass' => '',
          'modifier' => 'hidden',
          'field_default_value' => 'Customer',
          'length_set' => '\'Core\',\'Custom-Core\',\'Customer\'',
          'fieldtype_config' => '',
          'restrict_to_groups' => '0',
          'field_width' => '0',
          'position' => '3190',
          '049_helptext' => '@deprecated since 6.2.0 - field is no longer used, but preserved to ensure backwards compatibility',
          'row_hexcolor' => '',
          'is_translatable' => '0',
          'validation_regex' => '',
          'id' => '7cf22089-bd59-0a48-6085-bf3defa84a7f',
      ])
    ;
    TCMSLogChange::insert(__LINE__, $data);

    $query ="ALTER TABLE `pkg_external_tracker`
                            ADD `class_type` ENUM('Core','Custom-Core','Customer') DEFAULT 'Customer' NOT NULL COMMENT 'Klassenart: @deprecated since 6.2.0 - field is no longer used, but preserved to ensure backwards compatibility'";
    TCMSLogChange::RunQuery(__LINE__, $query);

    $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
      ->setFields([
          'cms_tbl_conf_id' => TCMSLogChange::GetTableId('pkg_external_tracker'),
          'name' => 'cms_portal_mlt',
          'translation' => 'Portals',
          'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_MULTITABLELIST_CHECKBOXES'),
          'cms_tbl_field_tab' => '',
          'isrequired' => '0',
          'fieldclass' => '',
          'modifier' => 'none',
          'field_default_value' => '',
          'length_set' => '',
          'fieldtype_config' => '',
          'restrict_to_groups' => '0',
          'field_width' => '0',
          'position' => '3191',
          '049_helptext' => 'The portal for which the web statistics service is used can be selected here.
    If no portal is selected, the web statistics service applies to all portals.
    If the same user/site code is used for several portals, the web statistics service can be used for several portals here.
    If a separate user/site code is used for each portal, the web statistics service must be created for each portal.',
          'row_hexcolor' => '',
          'is_translatable' => '0',
          'validation_regex' => '',
          'id' => 'bfbcd0fe-eca1-66bb-57c9-699c9c1ad3ec',
      ])
    ;
    TCMSLogChange::insert(__LINE__, $data);

    $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
      ->setFields([
//          'name' => 'cms_portal_mlt',
          'translation' => 'Portale',
          '049_helptext' => 'Hier kann ausgewählt werden, für welches Portal der Webstatistikdienst verwendet wird.
    Wird kein Portal ausgewählt, gilt der Webstatistikdienst für alle Portale.
    Wird für mehrere Portale derselbe Benutzer/Site-Code verwendet, kann der Webstatistikdienst hier für mehrere Portal angewandt werden.
    Wird für jedes Portal ein eigener Benutzer/Site-Code verwendet, muss der Webstatistikdienst für jedes Portal angelegt werden.',
          'row_hexcolor' => '',
      ])
      ->setWhereEquals([
          'id' => 'bfbcd0fe-eca1-66bb-57c9-699c9c1ad3ec',
      ])
    ;
    TCMSLogChange::update(__LINE__, $data);

    $query ="CREATE TABLE `pkg_external_tracker_cms_portal_mlt` (
                      `source_id` CHAR( 36 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
                      `target_id` CHAR( 36 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
                      `entry_sort` int(11) NOT NULL default '0',
                      PRIMARY KEY ( `source_id` , `target_id` ),
                      INDEX (target_id),
                      INDEX (entry_sort)
                    ) ENGINE = InnoDB";
    TCMSLogChange::RunQuery(__LINE__, $query);

    TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('pkg_external_tracker'), 'name', 'id');
    TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('pkg_external_tracker'), 'active', 'name');
    TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('pkg_external_tracker'), 'identifier', 'active');
    TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('pkg_external_tracker'), 'test_identifier', 'identifier');
    TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('pkg_external_tracker'), 'cms_portal_mlt', 'test_identifier');
    TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('pkg_external_tracker'), 'class', 'cms_portal_mlt');
    TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('pkg_external_tracker'), 'class_type', 'class');
    TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('pkg_external_tracker'), 'class_subtype', 'class_type');

    // load the cms_menu_category id from the table
    $connection = TCMSLogChange::getDatabaseConnection();
    $query = "SELECT `id` FROM `cms_menu_category` WHERE `system_name` = 'analytics'";
    $menuCategoryRecord = $connection->fetchAssociative($query);

    $menuCategoryId = '';
    if (false === $menuCategoryRecord) {
        TCMSLogChange::addInfoMessage('Could not find the menu category with the system name "analytics"', TCMSLogChange::INFO_MESSAGE_LEVEL_ERROR);
    } else {
        $menuCategoryId = $menuCategoryRecord['id'];
    }

    $data = TCMSLogChange::createMigrationQueryData('cms_menu_item', 'en')
      ->setFields([
          'name' => 'External Tracking-Services',
          'target' => '8bff1420-ef3f-c273-8e5b-3e9a87848fa0',
          'icon_font_css_class' => 'fas fa-chart-line',
          'position' => '51',
          'cms_menu_category_id' => $menuCategoryId,
          'id' => '2071c4ec-46cc-8690-9da1-accbd38803b6',
      ])
    ;
    TCMSLogChange::insert(__LINE__, $data);

    $data = TCMSLogChange::createMigrationQueryData('cms_menu_item', 'de')
      ->setFields([
          'name' => 'Externe Tracking-Dienste',
      ])
      ->setWhereEquals([
          'id' => '2071c4ec-46cc-8690-9da1-accbd38803b6',
      ])
    ;
    TCMSLogChange::update(__LINE__, $data);

    $data = TCMSLogChange::createMigrationQueryData('cms_menu_item', 'de')
      ->setFields([
          'target_table_name' => 'cms_tbl_conf',
      ])
      ->setWhereEquals([
          'id' => '2071c4ec-46cc-8690-9da1-accbd38803b6',
      ])
    ;
    TCMSLogChange::update(__LINE__, $data);

    TCMSLogChange::AddExtensionAutoParentToTable('pkg_external_tracker','TPkgExternalTracker', '','', 'TPkgExternalTrackerList');
}
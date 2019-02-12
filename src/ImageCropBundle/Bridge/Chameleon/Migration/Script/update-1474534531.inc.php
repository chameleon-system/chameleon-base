<h1>update - Build #1474534531</h1>
<h2>Date: 2016-09-22</h2>
<div class="changelog">
    add preset table<br/>
</div>
<?php
$query = "CREATE TABLE `cms_image_crop_preset` (
              `id` CHAR( 36 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
              `cmsident` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Key is used so that records can be easily identified in Chameleon',
              PRIMARY KEY ( `id` ),
              UNIQUE (`cmsident`)
            ) ENGINE = InnoDB";
TCMSLogChange::RunQuery(__LINE__, $query);

$query = "ALTER TABLE `cms_image_crop_preset` COMMENT = 'Images - Cutout sizes:\\n'";
TCMSLogChange::RunQuery(__LINE__, $query);

$tableIdCropPresets = TCMSLogChange::createUnusedRecordId('cms_tbl_conf');

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
    ->setFields(
        array(
            'name' => 'cms_image_crop_preset',
            'dbobject_type' => 'Customer',
            'translation' => 'Bilder - Ausschnittgrößen',
            'engine' => 'InnoDB',
            'cms_content_box_id' => '4',
            'icon_list' => 'picture_edit.png',
            'cms_usergroup_id' => TCMSLogChange::GetUserGroupIdByKey('cms_admin'),
            'frontend_auto_cache_clear_enabled' => '1',
            'id' => $tableIdCropPresets,
        )
    );
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'en')
    ->setFields(
        array(
            'translation' => 'Images - Cutout sizes',
        )
    )
    ->setWhereEquals(
        array(
            'id' => $tableIdCropPresets,
        )
    );
TCMSLogChange::update(__LINE__, $data);

TCMSLogChange::SetTableRolePermissions('cms_admin', 'cms_image_crop_preset', true, array(0, 1, 2, 3, 4, 6));
TCMSLogChange::SetTableRolePermissions('editor', 'cms_image_crop_preset', true, array(6));

$nameFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');
$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        array(
            'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_image_crop_preset'),
            'name' => 'name',
            'translation' => 'Name',
            'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING'),
            'cms_tbl_field_tab' => '',
            'isrequired' => '1',
            'fieldclass' => '',
            'fieldclass_subtype' => '',
            'modifier' => 'none',
            'field_default_value' => '',
            'length_set' => '',
            'fieldtype_config' => '',
            'restrict_to_groups' => '0',
            'field_width' => '0',
            'position' => '0',
            '049_helptext' => '',
            'row_hexcolor' => '',
            'is_translatable' => '0',
            'validation_regex' => '',
            'id' => $nameFieldId,
        )
    );
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields(
        array(
            'translation' => 'Name',
        )
    )
    ->setWhereEquals(
        array(
            'id' => $nameFieldId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

$query = "ALTER TABLE `cms_image_crop_preset`
                 ADD `name` VARCHAR(255) NOT NULL COMMENT 'Name: '";
TCMSLogChange::RunQuery(__LINE__, $query);

$widthFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');
$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        array(
            'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_image_crop_preset'),
            'name' => 'width',
            'translation' => 'Breite',
            'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_NUMBER'),
            'cms_tbl_field_tab' => '',
            'isrequired' => '0',
            'fieldclass' => '',
            'fieldclass_subtype' => '',
            'modifier' => 'none',
            'field_default_value' => '0',
            'length_set' => '',
            'fieldtype_config' => '',
            'restrict_to_groups' => '0',
            'field_width' => '0',
            'position' => '1',
            '049_helptext' => 'Breite in Pixeln',
            'row_hexcolor' => '',
            'is_translatable' => '0',
            'validation_regex' => '',
            'id' => $widthFieldId,
        )
    );
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields(
        array(
            'translation' => 'Width',
            '049_helptext' => 'Width in pixels',
        )
    )
    ->setWhereEquals(
        array(
            'id' => $widthFieldId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

$query = "ALTER TABLE `cms_image_crop_preset`
                 ADD `width` INT(11) DEFAULT '0' NOT NULL COMMENT 'Breite'";
TCMSLogChange::RunQuery(__LINE__, $query);

$heightFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');
$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        array(
            'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_image_crop_preset'),
            'name' => 'height',
            'translation' => 'Höhe',
            'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_NUMBER'),
            'cms_tbl_field_tab' => '',
            'isrequired' => '0',
            'fieldclass' => '',
            'fieldclass_subtype' => '',
            'modifier' => 'none',
            'field_default_value' => '0',
            'length_set' => '',
            'fieldtype_config' => '',
            'restrict_to_groups' => '0',
            'field_width' => '0',
            'position' => '2',
            '049_helptext' => 'Höhe in Pixeln',
            'row_hexcolor' => '',
            'is_translatable' => '0',
            'validation_regex' => '',
            'id' => $heightFieldId,
        )
    );
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields(
        array(
            'translation' => 'Height',
            '049_helptext' => 'Height in pixels',
        )
    )
    ->setWhereEquals(
        array(
            'id' => $heightFieldId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

$query = "ALTER TABLE `cms_image_crop_preset`
                 ADD `height` INT(11) DEFAULT '0' NOT NULL COMMENT 'Höhe'";
TCMSLogChange::RunQuery(__LINE__, $query);

$systemNameFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');
$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        array(
            'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_image_crop_preset'),
            'name' => 'system_name',
            'translation' => 'Systemname',
            'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING'),
            'cms_tbl_field_tab' => '',
            'isrequired' => '1',
            'fieldclass' => '',
            'fieldclass_subtype' => '',
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
            'id' => $systemNameFieldId,
        )
    );
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields(
        array(
            'translation' => 'System name',
        )
    )
    ->setWhereEquals(
        array(
            'id' => $systemNameFieldId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

$query = "ALTER TABLE `cms_image_crop_preset`
                     ADD `system_name` VARCHAR(255) NOT NULL COMMENT 'Systemname: '";
TCMSLogChange::RunQuery(__LINE__, $query);

$query = 'ALTER TABLE `cms_image_crop_preset` ADD INDEX ( `system_name` ) ';
TCMSLogChange::RunQuery(__LINE__, $query);

$nameListFieldId = TCMSLogChange::createUnusedRecordId('cms_tbl_display_list_fields');
$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'de')
    ->setFields(
        array(
            'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_image_crop_preset'),
            'title' => 'Name',
            'name' => '`cms_image_crop_preset`.`name`',
            'cms_translation_field_name' => '',
            'db_alias' => 'name',
            'show_in_list' => '1',
            'show_in_sort' => '0',
            'position' => '0',
            'id' => $nameListFieldId,
        )
    );
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'en')
    ->setFields(
        array(
            'title' => 'Name',
        )
    )
    ->setWhereEquals(
        array(
            'id' => $nameListFieldId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

$systemNameListFieldId = TCMSLogChange::createUnusedRecordId('cms_tbl_display_list_fields');
$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'de')
    ->setFields(
        array(
            'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_image_crop_preset'),
            'title' => 'System-Name',
            'name' => '`cms_image_crop_preset`.`system_name`',
            'cms_translation_field_name' => '',
            'db_alias' => 'system_name',
            'show_in_list' => '1',
            'show_in_sort' => '0',
            'position' => '1',
            'id' => $systemNameListFieldId,
        )
    );
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'en')
    ->setFields(
        array(
            'title' => 'System name',
        )
    )
    ->setWhereEquals(
        array(
            'id' => $systemNameListFieldId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

$widthListFieldId = TCMSLogChange::createUnusedRecordId('cms_tbl_display_list_fields');
$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'de')
    ->setFields(
        array(
            'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_image_crop_preset'),
            'title' => 'Breite',
            'name' => '`cms_image_crop_preset`.`width`',
            'cms_translation_field_name' => '',
            'db_alias' => 'width',
            'show_in_list' => '1',
            'show_in_sort' => '0',
            'position' => '2',
            'id' => $widthListFieldId,
        )
    );
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'en')
    ->setFields(
        array(
            'title' => 'Width',
        )
    )
    ->setWhereEquals(
        array(
            'id' => $widthListFieldId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

$heightListFieldId = TCMSLogChange::createUnusedRecordId('cms_tbl_display_list_fields');
$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'de')
    ->setFields(
        array(
            'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_image_crop_preset'),
            'title' => 'Höhe',
            'name' => '`cms_image_crop_preset`.`height`',
            'cms_translation_field_name' => '',
            'db_alias' => 'height',
            'show_in_list' => '1',
            'show_in_sort' => '0',
            'position' => '3',
            'id' => $heightListFieldId,
        )
    );
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'en')
    ->setFields(
        array(
            'title' => 'Height',
        )
    )
    ->setWhereEquals(
        array(
            'id' => $heightListFieldId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

$sortFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');
$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        array(
            'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_image_crop_preset'),
            'name' => 'position',
            'translation' => 'Sortierung',
            'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_SORTORDER'),
            'cms_tbl_field_tab' => '',
            'isrequired' => '0',
            'fieldclass' => '',
            'fieldclass_subtype' => '',
            'modifier' => 'none',
            'field_default_value' => '',
            'length_set' => '',
            'fieldtype_config' => '',
            'restrict_to_groups' => '0',
            'field_width' => '0',
            'position' => '4',
            '049_helptext' => '',
            'row_hexcolor' => '',
            'is_translatable' => '0',
            'validation_regex' => '',
            'id' => $sortFieldId,
        )
    );
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields(
        array(
            'translation' => 'Sort',
        )
    )
    ->setWhereEquals(
        array(
            'id' => $sortFieldId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

$query = "ALTER TABLE `cms_image_crop_preset`
                     ADD `position` INT NOT NULL COMMENT 'Sortierung: '";
TCMSLogChange::RunQuery(__LINE__, $query);

$query = 'ALTER TABLE `cms_image_crop_preset` ADD INDEX ( `position` ) ';
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_orderfields', 'de')
    ->setFields(
        array(
            'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_image_crop_preset'),
            'name' => 'position',
            'sort_order_direction' => 'ASC',
            'position' => '0',
            'id' => TCMSLogChange::createUnusedRecordId('cms_tbl_display_orderfields'),
        )
    );
TCMSLogChange::insert(__LINE__, $data);

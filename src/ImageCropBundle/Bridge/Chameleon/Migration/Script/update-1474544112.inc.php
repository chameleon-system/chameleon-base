<h1>update - Build #1474544112</h1>
<h2>Date: 2016-09-22</h2>
<div class="changelog">
    add image crop table<br/>
</div>
<?php
$imageCropTableId = TCMSLogChange::createUnusedRecordId('cms_tbl_conf');
$query = "CREATE TABLE `cms_image_crop` (
              `id` CHAR( 36 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
              `cmsident` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Key is used so that records can be easily identified in Chameleon',
              PRIMARY KEY ( `id` ),
              UNIQUE (`cmsident`)
            ) ENGINE = InnoDB";
TCMSLogChange::RunQuery(__LINE__, $query);

$query = "ALTER TABLE `cms_image_crop` COMMENT = 'Bildausschnitte:\\n'";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
    ->setFields(
        array(
            'name' => 'cms_image_crop',
            'dbobject_type' => 'Customer',
            'translation' => 'Bildausschnitte',
            'engine' => 'InnoDB',
            'cms_usergroup_id' => TCMSLogChange::GetUserGroupIdByKey('website_editor'),
            'frontend_auto_cache_clear_enabled' => '1',
            'id' => $imageCropTableId,
        )
    );
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'en')
    ->setFields(
        array(
            'translation' => 'Image crops',
        )
    )
    ->setWhereEquals(
        array(
            'id' => $imageCropTableId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

TCMSLogChange::SetTableRolePermissions('editor', 'cms_image_crop', true, array(0, 1, 2, 3, 4, 6));

$imageFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');
$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        array(
            'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_image_crop'),
            'name' => 'cms_media_id',
            'translation' => 'Bild',
            'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_EXTENDEDTABLELIST'),
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
            'id' => $imageFieldId,
        )
    );
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields(
        array(
            'translation' => 'Image',
        )
    )
    ->setWhereEquals(
        array(
            'id' => $imageFieldId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

$query = "ALTER TABLE `cms_image_crop`
                 ADD `cms_media_id` CHAR(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Bild: '";
TCMSLogChange::RunQuery(__LINE__, $query);

$query = 'ALTER TABLE `cms_image_crop` ADD INDEX ( `cms_media_id` ) ';
TCMSLogChange::RunQuery(__LINE__, $query);

$cropPresetFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');
$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        array(
            'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_image_crop'),
            'name' => 'cms_image_crop_preset_id',
            'translation' => 'Vorgabe',
            'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_TABLELIST'),
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
            'position' => '1',
            '049_helptext' => '',
            'row_hexcolor' => '',
            'is_translatable' => '0',
            'validation_regex' => '',
            'id' => $cropPresetFieldId,
        )
    );
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields(
        array(
            'translation' => 'Preset',
        )
    )
    ->setWhereEquals(
        array(
            'id' => $cropPresetFieldId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

$query = "ALTER TABLE `cms_image_crop`
                 ADD `cms_image_crop_preset_id` CHAR(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Vorgabe: '";
TCMSLogChange::RunQuery(__LINE__, $query);

$query = 'ALTER TABLE `cms_image_crop` ADD INDEX ( `cms_image_crop_preset_id` ) ';
TCMSLogChange::RunQuery(__LINE__, $query);

$query = 'ALTER TABLE `cms_image_crop` ADD INDEX `cms_media_and_preset` ( `cms_image_crop_preset_id`,  `cms_media_id` ) ';
TCMSLogChange::RunQuery(__LINE__, $query);

$xPositionFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');
$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        array(
            'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_image_crop'),
            'name' => 'pos_x',
            'translation' => 'X-Position des Ausschnitts',
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
            '049_helptext' => '',
            'row_hexcolor' => '',
            'is_translatable' => '0',
            'validation_regex' => '',
            'id' => $xPositionFieldId,
        )
    );
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields(
        array(
            'translation' => 'X position of crop',
        )
    )
    ->setWhereEquals(
        array(
            'id' => $xPositionFieldId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

$query = "ALTER TABLE `cms_image_crop`
                 ADD`pos_x` INT(11) DEFAULT '0' NOT NULL COMMENT 'X-Position des Ausschnitts: '";
TCMSLogChange::RunQuery(__LINE__, $query);

$yPositionFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');
$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        array(
            'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_image_crop'),
            'name' => 'pos_y',
            'translation' => 'Y-Position des Ausschnitts',
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
            'position' => '3',
            '049_helptext' => '',
            'row_hexcolor' => '',
            'is_translatable' => '0',
            'validation_regex' => '',
            'id' => $yPositionFieldId,
        )
    );
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields(
        array(
            'translation' => 'Y position of crop',
        )
    )
    ->setWhereEquals(
        array(
            'id' => $yPositionFieldId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

$query = "ALTER TABLE `cms_image_crop`
                 ADD `pos_y` INT(11) DEFAULT '0' NOT NULL COMMENT 'Y-Position des Ausschnitts: '";
TCMSLogChange::RunQuery(__LINE__, $query);

$widthFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');
$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        array(
            'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_image_crop'),
            'name' => 'width',
            'translation' => 'Breite des Ausschnitts',
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
            'position' => '4',
            '049_helptext' => '',
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
            'translation' => 'Crop width',
        )
    )
    ->setWhereEquals(
        array(
            'id' => '8011eda-c5b2-6212-a102-03d29c671a61',
        )
    );
TCMSLogChange::update(__LINE__, $data);

$query = "ALTER TABLE `cms_image_crop`
                 ADD `width` INT(11) DEFAULT '0' NOT NULL COMMENT 'Breite des Ausschnitts: '";
TCMSLogChange::RunQuery(__LINE__, $query);

$heightFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');
$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        array(
            'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_image_crop'),
            'name' => 'height',
            'translation' => 'Höhe des Ausschnitts',
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
            'position' => '5',
            '049_helptext' => '',
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
            'translation' => 'Crop height',
        )
    )
    ->setWhereEquals(
        array(
            'id' => $heightFieldId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

$query = "ALTER TABLE `cms_image_crop`
                ADD `height` INT(11) DEFAULT '0' NOT NULL COMMENT 'Höhe des Ausschnitts: '";
TCMSLogChange::RunQuery(__LINE__, $query);

$nameFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');
$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        array(
            'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_image_crop'),
            'name' => 'name',
            'translation' => 'Name',
            'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING'),
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
            'position' => '6',
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

$query = "ALTER TABLE `cms_image_crop`
                 ADD `name` VARCHAR(255) NOT NULL COMMENT 'Name: '";
TCMSLogChange::RunQuery(__LINE__, $query);

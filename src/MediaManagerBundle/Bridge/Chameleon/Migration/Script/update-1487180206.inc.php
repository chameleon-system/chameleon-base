<h1>update - Build #1487180206</h1>
<h2>Date: 2017-02-15</h2>
<div class="changelog">
    add icon field in media tree table<br/>
</div>
<?php
$iconFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');
$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        [
            'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_media_tree'),
            'name' => 'icon',
            'translation' => 'Icon',
            'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_ICON'),
            'cms_tbl_field_tab' => '',
            'isrequired' => '0',
            'fieldclass' => 'TCMSFieldSmallIconList',
            'fieldclass_subtype' => '',
            'modifier' => 'none',
            'field_default_value' => '',
            'length_set' => '',
            'fieldtype_config' => '',
            'restrict_to_groups' => '0',
            'field_width' => '0',
            '049_helptext' => '',
            'row_hexcolor' => '',
            'is_translatable' => '0',
            'validation_regex' => '',
            'id' => $iconFieldId,
        ]
    );
TCMSLogChange::insert(__LINE__, $data);

$query = "ALTER TABLE `cms_media_tree`
                 ADD `icon` VARCHAR(255) DEFAULT '' NOT NULL COMMENT 'Icon: '";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields(
        [
            'translation' => 'Icon',
        ]
    )
    ->setWhereEquals(
        [
            'id' => $iconFieldId,
        ]
    );
TCMSLogChange::update(__LINE__, $data);

TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('cms_media_tree'), 'icon', 'name');

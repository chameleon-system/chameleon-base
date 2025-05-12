<h1>update - Build #1502293246</h1>
<h2>Date: 2017-08-09</h2>
<div class="changelog">
    #38232<br/>
    add alt-tag field for images<br>
</div>
<?php
$altTagFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');
$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields(
        [
            'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_media'),
            'name' => 'alt_tag',
            'translation' => 'Alt-Tag',
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
            '049_helptext' => '',
            'row_hexcolor' => '',
            'is_translatable' => '0',
            'validation_regex' => '',
            'id' => $altTagFieldId,
        ]
    );
TCMSLogChange::insert(__LINE__, $data);

$query = "ALTER TABLE `cms_media`
                 ADD `alt_tag` VARCHAR(255) NOT NULL COMMENT 'Alt-Tag: '";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields(
        [
            'translation' => 'Alt tag',
        ]
    )
    ->setWhereEquals(
        [
            'id' => $altTagFieldId,
        ]
    );
TCMSLogChange::update(__LINE__, $data);

TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('cms_media'), 'alt_tag', 'filetypes');

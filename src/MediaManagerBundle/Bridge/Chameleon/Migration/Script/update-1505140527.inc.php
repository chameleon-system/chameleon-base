<h1>update - Build #1505140527</h1>
<h2>Date: 2017-09-11</h2>
<div class="changelog">
    add system name field for media<br/>
</div>
<?php
$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields(
        [
            'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_media'),
            'name' => 'systemname',
            'translation' => 'Systemname',
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
            '049_helptext' => '',
            'row_hexcolor' => '',
            'is_translatable' => '0',
            'validation_regex' => '',
            'id' => TCMSLogChange::createUnusedRecordId('cms_field_conf'),
        ]
    );
TCMSLogChange::insert(__LINE__, $data);

$query = "ALTER TABLE `cms_media`
                 ADD `systemname` VARCHAR(255) NOT NULL COMMENT 'Systemname: '";
TCMSLogChange::RunQuery(__LINE__, $query);

TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('cms_media'), 'systemname', 'alt_tag');

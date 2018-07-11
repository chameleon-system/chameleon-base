<h1>update - Build #1490106136</h1>
<h2>Date: 2017-03-21</h2>
<div class="changelog">
    #38232<br/>
    add date_changed field<br>
</div>
<?php
$changedFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');
$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        array(
            'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_media'),
            'name' => 'date_changed',
            'translation' => 'Zuletzt geändert',
            'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_DATETIME_NOW'),
            'cms_tbl_field_tab' => '',
            'isrequired' => '0',
            'fieldclass' => '',
            'fieldclass_subtype' => '',
            'class_type' => 'Core',
            'modifier' => 'none',
            'field_default_value' => '0000-00-00 00:00:00',
            'length_set' => '',
            'fieldtype_config' => '',
            'restrict_to_groups' => '',
            'field_width' => '',
            '049_helptext' => '',
            'row_hexcolor' => '',
            'is_translatable' => '0',
            'validation_regex' => '',
            'id' => $changedFieldId,
        )
    );
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields(
        array(
            'translation' => 'Last changed',
        )
    )
    ->setWhereEquals(
        array(
            'id' => $changedFieldId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

$query = "ALTER TABLE `cms_media`
                 ADD `date_changed` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL COMMENT 'Zuletzt geändert: '";
TCMSLogChange::RunQuery(__LINE__, $query);

$query = 'ALTER TABLE `cms_media` ADD INDEX ( `date_changed` ) ';
TCMSLogChange::RunQuery(__LINE__, $query);

TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('cms_media'), 'date_changed', 'time_stamp');

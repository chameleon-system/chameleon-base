<h1>update - Build #1488199875</h1>
<h2>Date: 2017-02-27</h2>
<div class="changelog">
    add media manager tags field<br/>
</div>
<?php

if (false === TCMSLogChange::FieldExists('cms_media', 'cms_tags_mlt')) {
    $tagsFieldId = TCMSLogChange::createUnusedRecordId('cms_field_conf');
    $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
        ->setFields(
            [
                'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_media'),
                'name' => 'cms_tags_mlt',
                'translation' => 'Tags',
                'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_TAGS'),
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
                '049_helptext' => '',
                'row_hexcolor' => '',
                'is_translatable' => '0',
                'validation_regex' => '',
                'id' => $tagsFieldId,
            ]
        );
    TCMSLogChange::insert(__LINE__, $data);

    $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
        ->setFields(
            [
                'translation' => 'Tags',
            ]
        )
        ->setWhereEquals(
            [
                'id' => $tagsFieldId,
            ]
        );
    TCMSLogChange::update(__LINE__, $data);

    $query = "CREATE TABLE `cms_media_cms_tags_mlt` (
                  `source_id` CHAR( 36 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
                  `target_id` CHAR( 36 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
                  `entry_sort` int(11) NOT NULL default '0',
                  PRIMARY KEY ( `source_id` , `target_id` ),
                  INDEX (target_id),
                  INDEX (entry_sort)
                )";
    TCMSLogChange::RunQuery(__LINE__, $query);

    TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('cms_media'), 'cms_tags_mlt', 'filetypes');
}

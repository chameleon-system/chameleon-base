<h1>Build #1713803713</h1>
<h2>Date: 2024-04-22</h2>comp
<div class="changelog">
    - ref #60689: add backend user field for preview mode token
</div>
<?php

$connection = TCMSLogChange::getDatabaseConnection();

if (false === $connection->fetchOne("SHOW COLUMNS FROM `cms_user` WHERE `Field` = 'preview_token'")) {
    $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
        ->setFields([
            'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_user'),
            'name' => 'preview_token',
            'translation' => 'Vorschau Token',
            'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING'),
            'cms_tbl_field_tab' => '',
            'isrequired' => '0',
            'fieldclass' => '',
            'modifier' => 'none',
            'field_default_value' => '',
            'threshold_notice' => '0',
            'threshold_warning' => '0',
            'length_set' => '',
            'fieldtype_config' => '',
            'restrict_to_groups' => '0',
            'field_width' => '0',
            '049_helptext' => '#63151: Dieser Token kann verwendet werden, um im Frontend den Vorschaumodus zu aktivieren.',
            'row_hexcolor' => '',
            'is_translatable' => '0',
            'validation_regex' => '',
            'id' => 'f492ced8-aa48-b9bb-a294-1c422c361913',
        ]);
    TCMSLogChange::insert(__LINE__, $data);

    $query = "ALTER TABLE `cms_user`
                       ADD `preview_token` VARCHAR(255) NOT NULL COMMENT 'Vorschau Token: #63151: Dieser Token kann verwendet werden, um im Frontend den Vorschaumodus zu aktivieren.'";
    TCMSLogChange::RunQuery(__LINE__, $query);
}

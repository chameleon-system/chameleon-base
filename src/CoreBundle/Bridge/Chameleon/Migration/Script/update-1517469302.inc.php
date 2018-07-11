<h1>update - Build #1517469302</h1>
<h2>Date: 2018-02-01</h2>
<div class="changelog">
    - #40685: Show class name in template module view
</div>
<?php

$conn = TCMSLogChange::getDatabaseConnection();

if (false === $conn->fetchColumn("SELECT `id` FROM `cms_tbl_display_list_fields` WHERE `name` = '`cms_tpl_module`.`classname`'")) {
    $id1 = TCMSLogChange::createUnusedRecordId('cms_tbl_display_list_fields');
    $data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'de')
        ->setFields(
            array(
                'title' => 'ID',
                'name' => '`cms_tpl_module`.`id`',
                'db_alias' => 'id',
                'position' => '1',
                'width' => '-1',
                'align' => 'left',
                'callback_fnc' => '',
                'use_callback' => '0',
                'show_in_list' => '1',
                'show_in_sort' => '0',
                'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_tpl_module'),
                'cms_translation_field_name' => '',
                'id' => $id1,
            )
        );
    TCMSLogChange::insert(__LINE__, $data);

    $data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'en')
        ->setFields(
            [
                'title' => 'ID',
            ]
        )
        ->setWhereEquals(['id' => $id1])
    ;
    TCMSLogChange::update(__LINE__, $data);

    $id2 = TCMSLogChange::createUnusedRecordId('cms_tbl_display_list_fields');
    $data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'de')
        ->setFields(
            array(
                'title' => 'Name',
                'name' => '`cms_tpl_module`.`name`',
                'db_alias' => 'name',
                'position' => '2',
                'width' => '-1',
                'align' => 'left',
                'callback_fnc' => '',
                'use_callback' => '0',
                'show_in_list' => '1',
                'show_in_sort' => '0',
                'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_tpl_module'),
                'cms_translation_field_name' => '',
                'id' => $id2,
            )
        );
    TCMSLogChange::insert(__LINE__, $data);

    $data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'en')
        ->setFields(
            [
                'title' => 'Name',
            ]
        )
        ->setWhereEquals(['id' => $id2])
    ;
    TCMSLogChange::update(__LINE__, $data);

    $id3 = TCMSLogChange::createUnusedRecordId('cms_tbl_display_list_fields');
    $data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'de')
        ->setFields(
            array(
                'title' => 'Klasse/Service-ID',
                'name' => '`cms_tpl_module`.`classname`',
                'db_alias' => 'classname',
                'position' => '3',
                'width' => '-1',
                'align' => 'left',
                'callback_fnc' => '',
                'use_callback' => '0',
                'show_in_list' => '1',
                'show_in_sort' => '0',
                'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_tpl_module'),
                'cms_translation_field_name' => '',
                'id' => $id3,
            )
        );
    TCMSLogChange::insert(__LINE__, $data);

    $data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'en')
        ->setFields(
           [
                'title' => 'Class name/Service ID',
           ]
        )
        ->setWhereEquals(['id' => $id3])
    ;
    TCMSLogChange::update(__LINE__, $data);

    $data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_orderfields', 'de')
        ->setFields(
            array(
                'name' => '`cms_tpl_module`.`classname`',
                'sort_order_direction' => 'ASC',
            )
        )
        ->setWhereEquals(
            array(
                'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_tpl_module'),
                'name' => '`cms_tpl_module`.`position`',
            )
        );
    TCMSLogChange::update(__LINE__, $data);
}

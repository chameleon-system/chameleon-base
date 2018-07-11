<h1>update - Build #1521184784</h1>
<h2>Date: 2018-03-16</h2>
<div class="changelog">
    #41274 - improve database indexes
</div>
<?php

$query = 'SELECT EXISTS (SELECT 1 FROM `cms_tbl_conf_index`
          WHERE `cms_tbl_conf_id` = :tableId
            AND `name` = :indexName
          )';
$indexEntryExists = TCMSLogChange::getDatabaseConnection()->fetchColumn(
    $query,
    [
        'tableId' => TCMSLogChange::GetTableId('t_country'),
        'indexName' => 'iso_code_2',
    ]
);
if (0 === (int) $indexEntryExists) {
    $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf_index', 'de')
        ->setFields(
            array(
                'cms_tbl_conf_id' => TCMSLogChange::GetTableId('t_country'),
                'name' => 'iso_code_2',
                'definition' => 'iso_code_2',
                'type' => 'INDEX',
                'id' => TCMSLogChange::createUnusedRecordId('cms_tbl_conf_index'),
            )
        );
    TCMSLogChange::insert(__LINE__, $data);
    $query = 'ALTER TABLE `t_country`
                        ADD INDEX  `iso_code_2` ( `iso_code_2` )';
    TCMSLogChange::RunQuery(__LINE__, $query);
}

$query = 'SELECT EXISTS (SELECT 1 FROM `cms_tbl_conf_index`
          WHERE `cms_tbl_conf_id` = :tableId
            AND `name` = :indexName
          )';
$indexEntryExists = TCMSLogChange::getDatabaseConnection()->fetchColumn(
    $query,
    [
        'tableId' => TCMSLogChange::GetTableId('cms_message_manager_message'),
        'indexName' => 'portal_id_name',
    ]
);
if (0 === (int) $indexEntryExists) {
    $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf_index', 'de')
        ->setFields(
            array(
                'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_message_manager_message'),
                'name' => 'portal_id_name',
                'definition' => '`cms_portal_id`,`name`',
                'type' => 'INDEX',
                'id' => TCMSLogChange::createUnusedRecordId('cms_tbl_conf_index'),
            )
        );
    TCMSLogChange::insert(__LINE__, $data);

    $query = 'ALTER TABLE `cms_message_manager_message`
                        ADD INDEX  `portal_id_name` ( `cms_portal_id`,`name` )';
    TCMSLogChange::RunQuery(__LINE__, $query);
}

$query = 'SELECT EXISTS (SELECT 1 FROM `cms_tbl_conf_index`
          WHERE `cms_tbl_conf_id` = :tableId
            AND `name` = :indexName
          )';
$indexEntryExists = TCMSLogChange::getDatabaseConnection()->fetchColumn(
    $query,
    [
        'tableId' => TCMSLogChange::GetTableId('cms_config_parameter'),
        'indexName' => 'cms_config_id_systemname',
    ]
);
if (0 === (int) $indexEntryExists) {
    $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf_index', 'de')
        ->setFields(
            array(
                'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_config_parameter'),
                'name' => 'cms_config_id_systemname',
                'definition' => '`cms_config_id`, `systemname`',
                'type' => 'INDEX',
                'id' => TCMSLogChange::createUnusedRecordId('cms_tbl_conf_index'),
            )
        );
    TCMSLogChange::insert(__LINE__, $data);
    $query = 'ALTER TABLE `cms_config_parameter`
                        ADD INDEX  `cms_config_id_systemname` ( `cms_config_id`, `systemname` )';
    TCMSLogChange::RunQuery(__LINE__, $query);
}

$query = 'SELECT EXISTS (SELECT 1 FROM `cms_tbl_conf_index`
          WHERE `cms_tbl_conf_id` = :tableId
            AND `name` = :indexName
          )';
$indexEntryExists = TCMSLogChange::getDatabaseConnection()->fetchColumn(
    $query,
    [
        'tableId' => TCMSLogChange::GetTableId('cms_url_alias'),
        'indexName' => 'active_source_url',
    ]
);
if (0 === (int) $indexEntryExists) {
    $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf_index', 'de')
        ->setFields(
            array(
                'name' => 'active_source_url',
                'definition' => '`active`, `source_url`',
                'type' => 'INDEX',
            )
        )
        ->setWhereEquals(
            array(
                'name' => 'source_url',
                'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_url_alias'),
            )
        );
    TCMSLogChange::update(__LINE__, $data);
    $query = 'ALTER TABLE `cms_url_alias` DROP INDEX  `source_url`';
    TCMSLogChange::RunQuery(__LINE__, $query);
    $query = 'ALTER TABLE `cms_url_alias`
                        ADD INDEX  `source_url` ( `active`, `source_url` )';
    TCMSLogChange::RunQuery(__LINE__, $query);
}

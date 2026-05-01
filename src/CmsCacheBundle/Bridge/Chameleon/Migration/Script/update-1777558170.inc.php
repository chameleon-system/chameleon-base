<h1>Build #1777558170</h1>
<h2>Date: 2026-04-30</h2>
<div class="changelog">
    -
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
        ->setFields([
                'name' => 'enable_query_cache', // prev.: 'new_field'
                'translation' => 'Query Cache aktivieren', // prev.: ''
                'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_BOOLEAN'), // prev.: '34'
                'cms_tbl_field_tab' => '13f11a67-6db4-64a3-9f8c-a48c6edba6e5', // prev.: ''
                'position' => '3248', // prev.: '0'
                '049_helptext' => 'Ist der Cache für die Webseite aktiv, dann werden Abfragen gegen diese Tabelle in den Cache geschrieben.', // prev.: ''
                'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_tbl_conf'),
                'id' => '03be901a-7c88-d130-0c4b-2d7501e9c516',
        ])
;
TCMSLogChange::insert(__LINE__, $data);

$query ="ALTER TABLE `cms_tbl_conf`
                        ADD `enable_query_cache` ENUM('0','1') DEFAULT '0' NOT NULL COMMENT 'Query Cache aktivieren: Ist der Cache für die Webseite aktiv, dann werden Abfragen gegen diese Tabelle in den Cache geschrieben.'";
TCMSLogChange::RunQuery(__LINE__, $query);
$query ="ALTER TABLE `cms_tbl_conf` ADD INDEX `enable_query_cache` (`enable_query_cache`)";
TCMSLogChange::RunQuery(__LINE__, $query);

$tableList = [
        'cms_language',
        'cms_portal',
        'cms_locals',
        'data_extranet',
        'esono_ab_test',
        'esono_ab_test_variant',
        'esono_ab_test_variant_page_mapping',
        'esono_ab_test_variant_static_module_mapping',
        'pkg_cms_theme',
        'cms_tbl_conf',
        'cms_field_conf',
        'cms_field_type',
        'cms_tpl_page',
        'cms_master_pagedef',
        'cms_master_pagedef_spot',
        'cms_master_pagedef_spot_parameter',
        'cms_master_pagedef_spot_access',
        'cms_tpl_page_cms_master_pagedef_spot',
        'cms_portal_system_page',
        'cms_tree',
        'pkg_cms_theme_block',
        'pkg_cms_theme_block_layout',
        'pkg_cms_theme_pkg_cms_theme_block_layout_mlt',
        'cms_config_imagemagick',
        'pkg_cms_text_block',
        'cms_portal_domains',
        'pkg_external_tracker',
];

foreach ($tableList as $tableName) {
    $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
            ->setFields([
                    'enable_query_cache' => '1', // prev.: 'new_field'
            ])->setWhereEquals(['name'=>$tableName]);
    ;
    TCMSLogChange::update(__LINE__, $data);
}
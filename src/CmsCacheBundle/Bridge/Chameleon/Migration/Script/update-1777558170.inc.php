<h1>Build #1777558170</h1>
<h2>Date: 2026-04-30</h2>
<div class="changelog">
    - #70143: add query cache toggle and activate it for given base tables
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
        ]);
TCMSLogChange::insert(__LINE__, $data);

$query = "ALTER TABLE `cms_tbl_conf`
                  ADD `enable_query_cache` ENUM('0','1') DEFAULT '0' NOT NULL COMMENT 'Query Cache aktivieren: Ist der Cache für die Webseite aktiv, dann werden Abfragen gegen diese Tabelle in den Cache geschrieben.'";
TCMSLogChange::RunQuery(__LINE__, $query);
$query = 'ALTER TABLE `cms_tbl_conf` ADD INDEX `enable_query_cache` (`enable_query_cache`)';
TCMSLogChange::RunQuery(__LINE__, $query);

$tableList = [
        'cms_config',
        'cms_config_cms_language_mlt',
        'cms_config_cmsmodule_extensions',
        'cms_config_imagemagick',
        'cms_config_parameter',
        'cms_config_themes',
        'cms_content_box',
        'cms_division',
        'cms_document_tree',
        'cms_export_profiles',
        'cms_export_profiles_fields',
        'cms_field_conf',
        'cms_field_type',
        'cms_filetype',
        'cms_font_image',
        'cms_image_crop',
        'cms_image_crop_preset',
        'cms_interface_manager',
        'cms_interface_manager_parameter',
        'cms_ip_whitelist',
        'cms_language',
        'cms_locals',
        'cms_master_pagedef',
        'cms_master_pagedef_spot',
        'cms_master_pagedef_spot_access',
        'cms_master_pagedef_spot_parameter',
        'cms_media',
        'cms_media_tree',
        'cms_menu_category',
        'cms_menu_custom_item',
        'cms_menu_item',
        'cms_message_manager_backend_message',
        'cms_message_manager_message',
        'cms_message_manager_message_type',
        'cms_module',
        'cms_portal',
        'cms_portal_domains',
        'cms_portal_navigation',
        'cms_portal_system_page',
        'cms_right',
        'cms_role',
        'cms_smart_url_handler',
        'cms_tags',
        'cms_tbl_conf',
        'cms_tbl_conf_index',
        'cms_tbl_conf_restrictions',
        'cms_tbl_display_list_fields',
        'cms_tbl_display_orderfields',
        'cms_tbl_extension',
        'cms_tbl_field_tab',
        'cms_tbl_list_class',
        'cms_text_block_simple',
        'cms_tpl_module',
        'cms_tpl_module_instance',
        'cms_tpl_page',
        'cms_tpl_page_cms_master_pagedef_spot',
        'cms_tpl_page_cms_usergroup_mlt',
        'cms_tree',
        'cms_tree_node',
        'cms_url_alias',
        'cms_user',
        'cms_usergroup',
        'cms_wizard_config',
        'cms_wizard_step',
        'data_country',
        'data_extranet',
        'data_extranet_salutation',
        'esono_ab_test',
        'esono_ab_test_variant',
        'esono_ab_test_variant_page_mapping',
        'esono_ab_test_variant_static_module_mapping',
        'pkg_article_category',
        'pkg_article_category_group',
        'pkg_article_tab',
        'pkg_article_type',
        'pkg_cms_routing',
        'pkg_cms_text_block',
        'pkg_cms_theme',
        'pkg_cms_theme_block',
        'pkg_cms_theme_block_layout',
        'pkg_cms_theme_pkg_cms_theme_block_layout_mlt',
        'pkg_comment_type',
        'pkg_external_tracker',
];

foreach ($tableList as $tableName) {
    $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
            ->setFields([
                    'enable_query_cache' => '1', // prev.: 'new_field'
            ])->setWhereEquals(['name' => $tableName]);

    TCMSLogChange::update(__LINE__, $data);
}

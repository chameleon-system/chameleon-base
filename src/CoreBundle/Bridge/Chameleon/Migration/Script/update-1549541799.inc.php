<h1>Build #1549541799</h1>
<h2>Date: 2019-02-07</h2>
<div class="changelog">
    - Add CMS main menu items.
</div>
<?php

// Define menu items in human-readably format (first column is c = custom menu item, t = table menu item, m = module menu item),

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\FieldTranslationUtil;

$menuItemDef = <<<EOT
c Navigation                            contents
t cms_tpl_page                          contents
c Media                                 contents
c Documents                             contents
t pkg_shop_primary_navi                 contents
t pkg_cms_text_block                    contents
t pkg_multi_module_set                  contents
t pkg_comment                           contents
t pkg_comment_type                      contents
t data_contact_topic                    contents
t shop_article                          products
t shop_category                         products
t shop_attribute                        products
t shop_article_marker                   products
t shop_article_type                     products
t shop_article_group                    products
t shop_variant_set                      products
t shop_manufacturer                     products
t pkg_shop_article_preorder             products
t shop_vat                              products
t shop_unit_of_measurement              products
t shop_contributor                      products
t shop_contributor_type                 products
t shop_article_document_type            products
t pkg_shop_listfilter                   productlists
t shop_module_articlelist_orderby       productlists
t shop_module_article_list_filter       productlists
t pkg_shop_listfilter_item_type         productlists
t shop_voucher_series                   discounts
t shop_voucher_series_sponsor           discounts
t shop_discount                         discounts
t data_extranet_user                    externalusers
t data_extranet_group                   externalusers
t data_extranet                         externalusers
t data_extranet_salutation              externalusers
t shop_order                            orders
t shop_order_basket                     orders
t pkg_shop_payment_transaction_type     orders
t shop_order_status_code                orders
t shop_order_step                       checkout
t shop_wrapping                         checkout
t shop_wrapping_card                    checkout
t shop_payment_handler_group            checkout
t shop_shipping_type                    checkout
t shop_shipping_group                   checkout
t shop_shipping_group_handler           checkout
t data_country                          internationalization
t cms_locals                            internationalization
t pkg_shop_currency                     internationalization
t pkg_shop_rating_service_rating        ratings
t pkg_shop_rating_service_history       ratings
t shop_article_review                   ratings
t pkg_shop_rating_service_widget_config ratings
t pkg_shop_rating_service               ratings
t shop_search_cloud_word                search
m articlesearchindex                    search
t shop_search_indexer                   search
t shop_search_query                     search
m shopstats                             analytics
t pkg_external_tracker                  analytics
t pkg_cms_changelog_set                 analytics
t pkg_shop_statistic_group              analytics
t shop_order_export_log                 logs
t shop_search_log                       logs
t pkg_shop_payment_ipn_message          logs
t pkg_shop_payment_ipn_message_trigger  logs
t data_mail_profile                     communication
t pkg_newsletter_user                   communication
t pkg_newsletter_robinson               communication
t pkg_newsletter_group                  communication
t pkg_newsletter_campaign               communication
t shop                                  system
t cms_portal                            system
t cms_cronjobs                          system
m sanitycheckbundle                     system
t cms_migration_counter                 system
m cmsupdatemanager                      system
t cms_tpl_module                        system
t pkg_cms_captcha                       system
t pkg_cms_counter                       system
t cms_filetype                          system
t cms_field_type                        system
t cms_config                            system
t cms_content_box                       system
t cms_menu_category                     system
t cms_menu_custom_item                  system
t cms_module                            system
t cms_tbl_conf                          system
t pkg_cms_class_manager                 system
m Interface                             dataexchange
t cms_interface_manager                 dataexchange
t cms_export_profiles                   dataexchange
t pkg_csv2sql                           dataexchange
t pkg_generic_table_export              dataexchange
t cms_master_pagedef                    layout
t pkg_cms_theme                         layout
t pkg_cms_theme_block                   layout
t cms_message_manager_message_type      layout
t shop_variant_display_handler          layout
t shop_variant_type_handler             layout
t cms_config_themes                     layout
t cms_font_image                        layout
t cms_url_alias                         routing
t pkg_cms_routing                       routing
t cms_smart_url_handler                 routing
t cms_user                              internalusers
t cms_usergroup                         internalusers
t cms_role                              internalusers
t cms_right                             internalusers
m CMSUserRightsOverview                 internalusers
EOT;

$customMenuItemIconFontCssClasses = [
    'Documents' => 'fas fa-file-alt',
    'Media' => 'far fa-image',
    'Navigation' => 'fas fa-leaf',
];

$databaseConnection = TCMSLogChange::getDatabaseConnection();

// Get data of all tables.

$statement = $databaseConnection->executeQuery('SELECT * FROM `cms_tbl_conf`');
if (false === $statement) {
    TCMSLogChange::addInfoMessage('Could not retrieve list of tables.', TCMSLogChange::INFO_MESSAGE_LEVEL_ERROR);

    return;
}

$tableList = [];
while (false !== $row = $statement->fetch()) {
    $tableList[$row['name']] = $row;
}
$statement->closeCursor();

// Get data of all backend modules.

$statement = $databaseConnection->executeQuery('SELECT * FROM `cms_module`');
if (false === $statement) {
    TCMSLogChange::addInfoMessage('Could not retrieve list of modules.', TCMSLogChange::INFO_MESSAGE_LEVEL_ERROR);

    return;
}

$moduleList = [];
while (false !== $row = $statement->fetch()) {
    $moduleList[$row['uniquecmsname']] = $row;
}
$statement->closeCursor();

// Get data of all custom menu items.

$statement = $databaseConnection->executeQuery('SELECT * FROM `cms_menu_custom_item`');
if (false === $statement) {
    TCMSLogChange::addInfoMessage('Could not retrieve list of custom main menu items.', TCMSLogChange::INFO_MESSAGE_LEVEL_ERROR);

    return;
}

/**
 * @var FieldTranslationUtil $fieldTranslationUtil
 */
$fieldTranslationUtil = ServiceLocator::get('chameleon_system_core.util.field_translation');
/**
 * @var LanguageServiceInterface $languageService
 */
$languageService = ServiceLocator::get('chameleon_system_core.language_service');
$nameFieldNameEn = $fieldTranslationUtil->getTranslatedFieldName(
        'main_menu_custom_item',
        'name',
        $languageService->getLanguageFromIsoCode('en')
);
$customMenuItemList = [];
while (false !== $row = $statement->fetch()) {
    $customMenuItemList[$row[$nameFieldNameEn]] = $row;
}
$statement->closeCursor();

// Get all supported languages.

$primaryLanguage = $databaseConnection->fetchColumn('SELECT `translation_base_language_id` FROM `cms_config`');

$languages = [];
$query = 'SELECT `iso_6391`
          FROM `cms_language` AS l
          JOIN `cms_config_cms_language_mlt` AS mlt ON l.`id` = mlt.`target_id`
          WHERE l.`id` <> ?';
$statement = $databaseConnection->executeQuery($query, [$primaryLanguage]);
if (false === $statement) {
    TCMSLogChange::addInfoMessage('Could not retrieve list of languages.', TCMSLogChange::INFO_MESSAGE_LEVEL_ERROR);

    return;
}

$languageList = [];
while (false !== $row = $statement->fetch()) {
    $languageList[] = $row['iso_6391'];
}
$statement->closeCursor();

// Get main menu category data

$statement = $databaseConnection->executeQuery('SELECT `id`, `system_name` FROM `cms_menu_category`');
if (false === $statement) {
    TCMSLogChange::addInfoMessage('Could not retrieve list of main menu categories.', TCMSLogChange::INFO_MESSAGE_LEVEL_ERROR);

    return;
}

$categoryList = [];
while (false !== $row = $statement->fetch()) {
    $categoryList[$row['system_name']] = $row;
}
$statement->closeCursor();
unset($statement);

// Create menu items.

$menuItemLines = \explode(PHP_EOL, $menuItemDef);
$handledTables = [];
$lastCategory = null;
$invalidTableNames = [];
$invalidModuleNames = [];
$invalidCustomMenuItemNames = [];

foreach ($menuItemLines as $menuItemLine) {
    [$type, $identifier, $category] = \preg_split('#\s+#', $menuItemLine);
    if ($category !== $lastCategory) {
        $position = 0;
        $lastCategory = $category;
    }
    $menuItemId = TCMSLogChange::createUnusedRecordId('cms_menu_item');
    switch ($type) {
        case 't':
            if (false === \array_key_exists($identifier, $tableList)) {
                $invalidTableNames[] = $identifier;
                continue 2;
            }
            $handledTables[$identifier] = true;
            $tableData = $tableList[$identifier];
            $menuItemData = [
                'id' => $menuItemId,
                'name' => $tableData['translation'],
                'target' => $tableData['id'],
                'target_table_name' => 'cms_tbl_conf',
                'icon_font_css_class' => $tableData['icon_font_css_class'],
                'position' => $position,
                'cms_menu_category_id' => $categoryList[$category]['id'],
            ];
            foreach ($languageList as $language) {
                if (true === \array_key_exists("translation__$language", $tableData)) {
                    $menuItemData["name__$language"] = $tableData["translation__$language"];
                }
            }
            break;
        case 'm':
            if (false === \array_key_exists($identifier, $moduleList)) {
                $invalidModuleNames[] = $identifier;
                continue 2;
            }
            $moduleData = $moduleList[$identifier];
            $menuItemData = [
                'id' => $menuItemId,
                'name' => $moduleData['name'],
                'target' => $moduleData['id'],
                'target_table_name' => 'cms_module',
                'icon_font_css_class' => $moduleData['icon_font_css_class'],
                'position' => $position,
                'cms_menu_category_id' => $categoryList[$category]['id'],
            ];
            foreach ($languageList as $language) {
                if (true === \array_key_exists("name__$language", $moduleData)) {
                    $menuItemData["name__$language"] = $moduleData["name__$language"];
                }
            }
            break;
        case 'c':
            if (false === \array_key_exists($identifier, $customMenuItemList)) {
                $invalidCustomMenuItemNames[] = $identifier;
                continue 2;
            }
            $customMenuItemData = $customMenuItemList[$identifier];
            $menuItemData = [
                'id' => $menuItemId,
                'name' => $customMenuItemData['name'],
                'target' => $customMenuItemData['id'],
                'target_table_name' => 'cms_menu_custom_item',
                'icon_font_css_class' => $customMenuItemIconFontCssClasses[$customMenuItemData['name']],
                'position' => $position,
                'cms_menu_category_id' => $categoryList[$category]['id'],
            ];

            foreach ($languageList as $language) {
                if (true === \array_key_exists("name__$language", $customMenuItemData)) {
                    $menuItemData["name__$language"] = $customMenuItemData["name__$language"];
                }
            }
            break;
    }

    $databaseConnection->insert('cms_menu_item', $menuItemData);

    ++$position;
}

if (\count($invalidTableNames)) {
    $tableNameString = \implode(', ', $invalidTableNames);
    TCMSLogChange::addInfoMessage(
            "While creating menu items, some tables could not be found. No menu items were created for these tables: $tableNameString",
            TCMSLogChange::INFO_MESSAGE_LEVEL_WARNING
    );
}

if (\count($invalidModuleNames)) {
    $moduleNameString = \implode(', ', $invalidModuleNames);
    TCMSLogChange::addInfoMessage(
        "While creating menu items, some backend modules could not be found. No menu items were created for these modules: $moduleNameString",
        TCMSLogChange::INFO_MESSAGE_LEVEL_WARNING
    );
}

if (\count($invalidCustomMenuItemNames)) {
    $customMenuItemNameString = \implode(', ', $invalidCustomMenuItemNames);
    TCMSLogChange::addInfoMessage(
        "While creating menu items, some custom menu items could not be found. No menu items were created for these custom menu items: $customMenuItemNameString",
        TCMSLogChange::INFO_MESSAGE_LEVEL_WARNING
    );
}

/*
 * Add remaining tables to "other" category, excluding all standard tables that are not intended to be accessed directly.
 * This is intended as a fallback for project-specific and third-party bundle so that menu items do not disappear
 * completely after migration.
 */

$excludeList = [
    'amazon_payment_id_mapping' => true,
    'cms_config_cmsmodule_extensions' => true,
    'cms_config_imagemagick' => true,
    'cms_config_parameter' => true,
    'cms_dark_site_content' => true,
    'cms_division' => true,
    'cms_document' => true,
    'cms_document_security_hash' => true,
    'cms_document_tree' => true,
    'cms_export_profiles_fields' => true,
    'cms_field_conf' => true,
    'cms_image_crop' => true,
    'cms_image_crop_preset' => true,
    'cms_interface_manager_parameter' => true,
    'cms_ip_whitelist' => true,
    'cms_language' => true,
    'cms_lock' => true,
    'cms_master_pagedef_spot' => true,
    'cms_master_pagedef_spot_access' => true,
    'cms_master_pagedef_spot_parameter' => true,
    'cms_media' => true,
    'cms_media_tree' => true,
    'cms_menu_item' => true,
    'cms_message_manager_backend_message' => true,
    'cms_message_manager_message' => true,
    'cms_migration_file' => true,
    'cms_portal_domains' => true,
    'cms_portal_navigation' => true,
    'cms_portal_system_page' => true,
    'cms_record_revision' => true,
    'cms_tags' => true,
    'cms_tbl_conf_index' => true,
    'cms_tbl_conf_restrictions' => true,
    'cms_tbl_display_list_fields' => true,
    'cms_tbl_display_orderfields' => true,
    'cms_tbl_extension' => true,
    'cms_tbl_field_tab' => true,
    'cms_tbl_list_class' => true,
    'cms_tpl_module_instance' => true,
    'cms_tpl_page_cms_master_pagedef_spot' => true,
    'cms_tree' => true,
    'cms_tree_node' => true,
    'cms_wizard_config' => true,
    'cms_wizard_step' => true,
    'data_atomic_lock' => true,
    'data_extranet_module_my_account' => true,
    'data_extranet_user_address' => true,
    'data_extranet_user_login_history' => true,
    'data_extranet_user_shop_article_history' => true,
    'data_img_teaser' => true,
    'module_cms_search' => true,
    'module_customlist_config' => true,
    'module_customlist_config_sortfields' => true,
    'module_feedback' => true,
    'module_list' => true,
    'module_list_cat' => true,
    'module_list_config' => true,
    'module_text' => true,
    'pkg_cms_changelog_item' => true,
    'pkg_cms_class_manager_extension' => true,
    'pkg_cms_core_log' => true,
    'pkg_cms_core_log_channel' => true,
    'pkg_cms_result_cache' => true,
    'pkg_cms_theme_block_layout' => true,
    'pkg_comment_module_config' => true,
    'pkg_image_hotspot' => true,
    'pkg_image_hotspot_item' => true,
    'pkg_image_hotspot_item_marker' => true,
    'pkg_image_hotspot_item_spot' => true,
    'pkg_multi_module_module_config' => true,
    'pkg_multi_module_set_item' => true,
    'pkg_newsletter_confirmation' => true,
    'pkg_newsletter_module_signout_config' => true,
    'pkg_newsletter_module_signup_config' => true,
    'pkg_newsletter_module_signup_teaser' => true,
    'pkg_newsletter_queue' => true,
    'pkg_run_frontend_action' => true,
    'pkg_shop_affiliate' => true,
    'pkg_shop_affiliate_parameter' => true,
    'pkg_shop_article_review_module_shop_article_review_configuration' => true,
    'pkg_shop_footer_category' => true,
    'pkg_shop_listfilter_item' => true,
    'pkg_shop_listfilter_module_config' => true,
    'pkg_shop_payment_ipn_status' => true,
    'pkg_shop_payment_ipn_trigger' => true,
    'pkg_shop_payment_transaction' => true,
    'pkg_shop_payment_transaction_position' => true,
    'pkg_shop_rating_service_teaser_cnf' => true,
    'pkg_shop_wishlist' => true,
    'pkg_shop_wishlist_article' => true,
    'pkg_shop_wishlist_mail_history' => true,
    'pkg_shop_wishlist_order_item' => true,
    'pkg_track_object' => true,
    'pkg_track_object_history' => true,
    'shop_article_catalog_conf' => true,
    'shop_article_catalog_conf_default_order' => true,
    'shop_article_contributor' => true,
    'shop_article_document' => true,
    'shop_article_image' => true,
    'shop_article_image_size' => true,
    'shop_article_preview_image' => true,
    'shop_article_stats' => true,
    'shop_article_stock' => true,
    'shop_attribute_value' => true,
    'shop_bank_account' => true,
    'shop_bundle_article' => true,
    'shop_category_tab' => true,
    'shop_manufacturer_module_conf' => true,
    'shop_module_article_list' => true,
    'shop_module_article_list_article' => true,
    'shop_order_bundle_article' => true,
    'shop_order_discount' => true,
    'shop_order_item' => true,
    'shop_order_payment_method_parameter' => true,
    'shop_order_shipping_group_parameter' => true,
    'shop_order_status' => true,
    'shop_order_status_item' => true,
    'shop_order_vat' => true,
    'shop_payment_handler' => true,
    'shop_payment_handler_group_config' => true,
    'shop_payment_handler_parameter' => true,
    'shop_payment_method' => true,
    'shop_search_cache' => true,
    'shop_search_cache_item' => true,
    'shop_search_field_weight' => true,
    'shop_search_ignore_word' => true,
    'shop_search_keyword_article' => true,
    'shop_stock_message' => true,
    'shop_stock_message_trigger' => true,
    'shop_suggest_article_log' => true,
    'shop_system_info' => true,
    'shop_system_info_module_config' => true,
    'shop_system_page' => true,
    'shop_user_notice_list' => true,
    'shop_user_purchased_voucher' => true,
    'shop_variant_type' => true,
    'shop_variant_type_value' => true,
    'shop_voucher' => true,
    'shop_voucher_use' => true,
    't_country' => true,
];

$position = 0;
foreach ($tableList as $tableName => $tableData) {
    if (true === \array_key_exists($tableName, $handledTables)) {
        continue;
    }
    if (true === \array_key_exists($tableName, $excludeList)) {
        continue;
    }

    $menuItemId = TCMSLogChange::createUnusedRecordId('cms_menu_item');
    $menuItemData = [
        'id' => $menuItemId,
        'name' => $tableData['translation'],
        'target' => $tableData['id'],
        'target_table_name' => 'cms_tbl_conf',
        'icon_font_css_class' => $tableData['icon_font_css_class'],
        'position' => $position,
        'cms_menu_category_id' => $categoryList['other']['id'],
    ];
    foreach ($languageList as $language) {
        if (true === \array_key_exists("translation__$language", $tableData)) {
            $menuItemData["name__$language"] = $tableData["translation__$language"];
        }
    }
    $databaseConnection->insert('cms_menu_item', $menuItemData);

    $position++;
}

<h1>Build #1547452866</h1>
<h2>Date: 2019-01-14</h2>
<div class="changelog">
    - https://github.com/chameleon-system/chameleon-system/issues/265
    - backend - main navigation: map icons
</div>

<?php
/**
 * @var ChameleonSystem\CoreBundle\Bridge\Chameleon\Migration\Service\MainMenuMigrator $mainMenuMigrator
 */
$mainMenuMigrator = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.service.main_menu_migrator');
$iconMapping = $mainMenuMigrator->getIconMapping();

foreach ($iconMapping as $oldIconName => $iconName) {
    $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'en')
        ->setFields([
            'icon_font_css_class' => $iconName,
        ])
        ->setWhereEquals([
            'icon_list' => $oldIconName,
        ])
    ;
    TCMSLogChange::update(__LINE__, $data);
}

// replace explicit table icons with new font icons based on the table names
$iconMapping = array(
    'cms_export_profiles' => 'far fa-save',
    'shop_variant_set' => 'fas fa-clone',
    'cms_content_box' => 'fas fa-project-diagram',
    'shop_search_query' => 'fab fa-searchengin',
    'pkg_shop_listfilter' => 'fas fa-list',
    'pkg_csv2sql' => 'fas fa-upload',
    'cms_url_alias' => 'fas fa-external-link-square-alt',
    'pkg_generic_table_export' => 'fas fa-file-export',
    'pkg_multi_module_set' => 'fas fa-boxes',
    'pkg_shop_listfilter_item_type' => 'fas fa-cogs',
    'shop_order_status_code' => 'fas fa-truck',
    'pkg_shop_payment_ipn_message_trigger' => 'far fa-arrow-alt-circle-right',
    'cms_interface_manager' => 'fas fa-exchange-alt',
    'shop_module_articlelist_orderby' => 'fas fa-sort-amount-down',
    'pkg_cms_routing' => 'fas fa-random',
    'pkg_shop_rating_service' => 'fas fa-medal',
    'shop_order_basket' => 'fas fa-shopping-basket',
    'shop_order' => 'fas fa-tasks',
    'shop_wrapping' => 'fas fa-gift',
    'pkg_cms_text_block' => 'fas fa-file-alt',
    'shop_attribute' => 'fas fa-palette',
    'pkg_cms_counter' => 'fas fa-sort-numeric-down',
    'shop_article' => 'fas fa-cubes',
    'cms_message_manager_message_type' => 'far fa-comments',
    'shop_vat' => 'far fa-object-ungroup',
    'shop_order_step' => 'fas fa-check-double',
    'pkg_shop_statistic_group' => 'fas fa-chart-bar',
    'pkg_external_tracker' => 'fas fa-chart-line',
    'pkg_shop_primary_navi' => 'fas fa-bars',
    'pkg_cms_class_manager' => 'fas fa-sitemap',
    'cms_cronjobs' => 'fas fa-business-time',
    'shop_shipping_group_handler' => 'fas fa-truck-monster',
    'shop_article_marker' => 'fas fa-braille',
    'cms_config_themes' => 'fas fa-desktop',
    'pkg_comment' => 'fas fa-comments',
    'data_contact_topic' => 'far fa-comment-alt',
    'shop_article_review' => 'fas fa-comment-dots',
    'pkg_comment_type' => 'far fa-comment',
    'data_extranet_salutation' => 'far fa-comment-dots',
    'shop_module_article_list_filter' => 'fas fa-filter',
    'pkg_shop_payment_ipn_message' => 'fas fa-poo-storm',
    'shop_search_indexer' => 'fas fa-search',
    'cms_tbl_conf' => 'fas fa-table',
    'pkg_shop_article_preorder' => 'fas fa-eye',
    'pkg_newsletter_group' => 'fas fa-mail-bulk',
    'pkg_newsletter_campaign' => 'fas fa-envelope',
    'shop_suggest_article_log' => 'fas fa-user-edit',
    'data_mail_profile' => 'fas fa-at',
    'shop_variant_display_handler' => 'far fa-eye',
    'pkg_cms_captcha' => 'fas fa-fingerprint',
    'shop_article_type' => 'fas fa-shapes',
    'shop_article_document_type' => 'fas fa-file-alt',
    'shop_voucher_series' => 'fas fa-gift',
    'data_extranet_group' => 'fas fa-users',
    'shop_contributor_type' => 'fas fa-user-cog',
    'cms_usergroup' => 'fas fa-users',
    'cms_filetype' => 'far fa-file',
    'cms_tpl_module' => 'fas fa-th-large',
    'cms_module' => 'fas fa-puzzle-piece',
    'pkg_shop_rating_service_history' => 'fas fa-history',
    'pkg_cms_core_log_channel' => 'fas fa-screwdriver',
    'cms_role' => 'fas fa-users-cog',
    'data_country' => 'fas fa-flag',
    'cms_font_image' => 'far fa-file-image',
    'shop_variant_type' => 'fab fa-usb',
    'shop_variant_type_handler' => 'fas fa-layer-group',
    'cms_master_pagedef' => 'fas fa-desktop',
    'pkg_shop_rating_service_widget_config' => 'fas fa-haykal',
    'shop_shipping_group' => 'far fa-money-bill-alt',
    'pkg_shop_rating_service_rating' => 'fas fa-star',
    'pkg_shop_payment_transaction_type' => 'fas fa-money-check-alt',
    'shop_payment_handler_group' => 'fas fa-hand-holding-usd',
    'shop_discount' => 'fas fa-money-bill-wave',
    'pkg_shop_currency' => 'fas fa-dollar-sign',
    'cms_config' => 'fas fa-cog',
    'pkg_newsletter_user' => 'fas fa-user-check',
    'shop_shipping_type' => 'fas fa-truck-loading',
    'pkg_cms_changelog_set' => 'fas fa-clipboard-list',
    'cms_tpl_page' => 'fas fa-globe-americas',
    'shop_order_export_log' => 'fas fa-file-export',
    'cms_migration_counter' => 'fas fa-terminal',
    'shop_search_log' => 'far fa-list-alt',
    'data_extranet' => 'fas fa-wrench',
    'pkg_cms_theme_block' => 'far fa-address-card',
    'pkg_cms_theme' => 'far fa-images',
    'shop_wrapping_card' => 'far fa-address-card',
    'shop_unit_of_measurement' => 'fas fa-pencil-ruler',
    'pkg_newsletter_robinson' => 'fas fa-user-slash',
    'pkg_cms_core_log' => 'fas fa-clipboard-list',
    'shop_article_group' => 'fas fa-layer-group',
    'shop' => 'fas fa-store-alt',
    'shop_category' => 'fas fa-boxes',
    'cms_smart_url_handler' => 'fas fa-share-square',
    'cms_right' => 'fas fa-user-shield',
    'cms_field_type' => 'fas fa-edit',
    'shop_contributor' => 'fas fa-user-edit',
    'shop_voucher_series_sponsor' => 'fas fa-user-tie',
    'cms_user' => 'fas fa-user',
    'data_extranet_user' => 'fas fa-user-lock',
    'shop_search_cloud_word' => 'fas fa-cloud',
    'cms_locals' => 'fas fa-map-marked-alt',
    'cms_portal' => 'fas fa-home',
    'shop_manufacturer' => 'fas fa-industry',
    'cms_dark_site_content' => 'fas fa-sign-out-alt',
    'cms_image_crop_preset' => 'fas fa-sign-out-alt',
);

foreach ($iconMapping as $tableName => $iconName) {
    $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'en')
        ->setFields([
            'icon_font_css_class' => $iconName,
        ])
        ->setWhereEquals([
            'name' => $tableName,
        ])
    ;
    TCMSLogChange::update(__LINE__, $data);
}

//set new icons for modules
$iconMapping = array(
    'cmsupdatemanager' => 'far fa-play-circle',
    'Interface' => 'fas fa-play',
    'CMSUserRightsOverview' => 'fas fa-user-shield',
    'articlesearchindex' => 'fas fa-search-plus',
    'shopstats' => 'fas fa-chart-pie',
    'sanitycheckbundle' => 'fas fa-bug',
);
foreach ($iconMapping as $moduleName => $iconName) {
    $data = TCMSLogChange::createMigrationQueryData('cms_module', 'en')
        ->setFields([
            'icon_font_css_class' => $iconName,
        ])
        ->setWhereEquals([
            'uniquecmsname' => $moduleName,
        ])
    ;
    TCMSLogChange::update(__LINE__, $data);
}

// get all main menu tables that got no mapped icon
$databaseConnection = TCMSLogChange::getDatabaseConnection();
$statement = $databaseConnection->executeQuery("SELECT * FROM `cms_tbl_conf` WHERE `icon_font_css_class` = '' AND `cms_content_box_id` != '' AND `cms_content_box_id` !='0'");

if (false === $statement->execute()) {
    return;
}

$tablesWithEmptyIcon = [];
while (false !== $row = $statement->fetch()) {
    $tablesWithEmptyIcon[] = $row['name'];
}

if (count($tablesWithEmptyIcon) > 0) {
    TCMSLogChange::addInfoMessage('Table icons were replaced with CSS icon classes, but for some tables no fitting icon could be found. Please refer to the upgrade guide for Chameleon 6.3 on what to do (section "Font Awesome Icons"). Tables: '.implode(', ', $tablesWithEmptyIcon));
}

$databaseConnection = TCMSLogChange::getDatabaseConnection();
$statement = $databaseConnection->executeQuery("SELECT * FROM `cms_module` WHERE `icon_font_css_class` = '' AND `cms_content_box_id` != '' AND `cms_content_box_id` !='0'");
if (false === $statement->execute()) {
    return;
}

$modulesWithEmptyIcon = [];
while (false !== $row = $statement->fetch()) {
    $modulesWithEmptyIcon[] = $row['name'];
}

if (count($modulesWithEmptyIcon) > 0) {
    TCMSLogChange::addInfoMessage('Backend module icons were replaced with CSS icon classes, but for some modules no fitting icon could be found. Please refer to the upgrade guide for Chameleon 6.3 on what to do (section "Font Awesome Icons").. Modules: '.implode(', ', $modulesWithEmptyIcon));
}

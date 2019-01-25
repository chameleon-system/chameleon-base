<h1>Build #1547452866</h1>
<h2>Date: 2019-01-14</h2>
<div class="changelog">
    - https://github.com/chameleon-system/chameleon-system/issues/265
    - backend - main navigation: map icons
</div>

<?php

// replace all table icons with new font icons based on the file names
$iconMapping = array(
    'accept.png' => 'fas fa-check-circle',
    'action_refresh.gif' => 'fas fa-sync',
    'action_save.gif' => 'far fa-save',
    'action_stop.gif' => 'fas fa-times-circle',
    'add.png' => 'fas fa-plus-circle',
    'application_cascade.png' => 'fas fa-clone',
    'application_form_magnify.png' => 'fab fa-searchengin',
    'application_form.png' => 'fas fa-list',
    'application_get.png' => 'fas fa-upload',
    'application_go.png' => 'fas fa-external-link-square-alt',
    'application_side_expand.png' => 'fas fa-file-export',
    'application_side_list.png' => 'fas fa-boxes',
    'application_side_tree.png' => 'fas fa-cogs',
    'application_view_detail.png' => 'fas fa-truck',
    'arrow_out.png' => 'far fa-arrow-alt-circle-right',
    'arrow_refresh.png' => 'fas fa-exchange-alt',
    'arrow_switch.png' => 'fas fa-random',
    'award_star_add.png' => 'fas fa-medal',
    'basket_error.png' => 'fas fa-shopping-basket',
    'basket.png' => 'fas fa-tasks',
    'box-orange.png' => 'fas fa-gift',
    'brick_edit.png' => 'fas fa-file-alt',
    'brick.png' => 'fas fa-palette',
    'bricks.png' => 'fas fa-cubes',
    'bug_error.png' => 'fas fa-bug',
    'building_edit.png' => 'far fa-comments',
    'calculator_edit.png' => 'far fa-object-ungroup',
    'cart_go.png' => 'fas fa-check-double',
    'chart_curve_edit.png' => 'fas fa-chart-bar',
    'chart_curve_go.png' => 'fas fa-chart-line',
    'chart_organisation.png' => 'fas fa-bars',
    'chart_pie.png' => 'fas fa-chart-pie',
    'clock_add.png' => 'fas fa-business-time',
    'cog.png' => 'fas fa-truck-monster',
    'color_swatch.png' => 'fas fa-braille',
    'color_wheel.png' => 'fas fa-desktop',
    'comment.gif' => 'fas fa-comments',
    'comments.png' => 'fas fa-comment-dots',
    'comment_yellow.gif' => 'far fa-comment',
    'database_gear.png' => 'fas fa-filter',
    'database_lightning.png' => 'fas fa-poo-storm',
    'database_link.png' => 'fas fa-search',
    'database_table.png' => 'fas fa-table',
    'date_go.png' => 'fas fa-eye',
    'email_add.png' => 'fas fa-mail-bulk',
    'email_edit.png' => 'fas fa-envelope',
    'email_go.png' => 'fas fa-user-edit',
    'email_link.png' => 'fas fa-at',
    'eye.png' => 'far fa-eye',
    'file_font_truetype.gif' => 'fas fa-fingerprint',
    'folder_brick.png' => 'fas fa-shapes',
    'folder_edit.png' => 'fas fa-file-alt',
    'folder_star.png' => 'fas fa-gift',
    'folder_user.png' => 'fas fa-users',
    'group_gear.png' => 'fas fa-user-cog',
    'group.png' => 'fas fa-users',
    'icon_attachment.gif' => 'far fa-file',
    'icon_component.gif' => 'fas fa-th-large',
    'icon_extension.gif' => 'fas fa-puzzle-piece',
    'icon_history.gif' => 'fas fa-history',
    'icon_package_get.gif' => 'fas fa-screwdriver',
    'icon_security.gif' => 'fas fa-users-cog',
    'icon_world.gif' => 'fas fa-flag',
    'image_edit.png' => 'far fa-file-image',
    'images.png' => 'fab fa-usb',
    'interface_installer.gif' => 'fas fa-layer-group',
    'layout_content.png' => 'fas fa-desktop',
    'layout.png' => 'fas fa-haykal',
    'lorry.png' => 'far fa-money-bill-alt',
    'medal_gold_2.png' => 'fas fa-star',
    'money_add.png' => 'fas fa-money-check-alt',
    'money_euro.png' => 'fas fa-hand-holding-usd',
    'money.png' => 'fas fa-money-bill-wave',
    'monitor_edit.png' => 'fas fa-cog',
    'newspaper.png' => 'fas fa-user-check',
    'package.png' => 'fas fa-truck-loading',
    'page_edit.png' => 'fas fa-clipboard-list',
    'page_html.gif' => 'fas fa-globe-americas',
    'page_next.gif' => 'fas fa-file-export',
    'page_package.gif' => 'far fa-play-circle',
    'page_text.gif' => 'fas fa-terminal',
    'page_white_find.png' => 'far fa-list-alt',
    'page_white_key.png' => 'fas fa-wrench',
    'photo.png' => 'far fa-address-card',
    'photos.png' => 'far fa-images',
    'pictures.png' => 'far fa-address-card',
    'pilcrow.png' => 'fas fa-pencil-ruler',
    'prohibited.png' => 'fas fa-user-slash',
    'report.png' => 'fas fa-clipboard-list',
    'resultset_next.png' => 'fas fa-play',
    'server.png' => 'fas fa-store-alt',
    'sitemap_color.png' => 'fas fa-boxes',
    'status_busy.png' => 'fas fa-user-shield',
    'textfield.png' => 'fas fa-edit',
    'user_comment.png' => 'fas fa-user-edit',
    'user_gray.png' => 'fas fa-user-tie',
    'user.png' => 'fas fa-user',
    'user_suit.png' => 'fas fa-user-lock',
    'weather_clouds.png' => 'fas fa-cloud',
    'world_edit.png' => 'fas fa-map-marked-alt',
    'world.png' => 'fas fa-home',
    'wrench.png' => 'fas fa-industry'
);

foreach ($iconMapping as $oldIconName => $iconName) {
    $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'en')
        ->setFields([
            'icon_font_awesome' => $iconName
        ])
        ->setWhereEquals([
            'icon_list' => $oldIconName
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
    'shop_manufacturer' => 'fas fa-industry'
);

foreach ($iconMapping as $tableName => $iconName) {
    $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'en')
        ->setFields([
            'icon_font_awesome' => $iconName
        ])
        ->setWhereEquals([
            'name' => $tableName
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
    'sanitycheckbundle' => 'fas fa-bug'
);
foreach ($iconMapping as $moduleName => $iconName) {
    $data = TCMSLogChange::createMigrationQueryData('cms_module', 'en')
        ->setFields([
            'icon_font_awesome' => $iconName
        ])
        ->setWhereEquals([
            'uniquecmsname' => $moduleName
        ])
    ;
    TCMSLogChange::update(__LINE__, $data);
}

// set standard icon for all missing mappings
$databaseConnection = TCMSLogChange::getDatabaseConnection();
$statement = $databaseConnection->executeQuery("SELECT * FROM `cms_tbl_conf` WHERE `icon_font_awesome` = '' AND `cms_content_box_id` != '' AND `cms_content_box_id` !='0'");

if (false === $statement->execute()) {
    return;
}

$tablesWithEmptyIcon = [];
while (false !== $row = $statement->fetch()) {
    $tablesWithEmptyIcon[] = $row['name'];
}

if (count($tablesWithEmptyIcon) > 0) {
    TCMSLogChange::addInfoMessage('Some of your custom table icons were replaced with a standard icon. Please refer to font awesome and select a matching icon. Tables: '.implode(', ',$tablesWithEmptyIcon));
}


$databaseConnection = TCMSLogChange::getDatabaseConnection();
$statement = $databaseConnection->executeQuery("SELECT * FROM `cms_module` WHERE `icon_font_awesome` = '' AND `cms_content_box_id` != '' AND `cms_content_box_id` !='0'");
if (false === $statement->execute()) {
    return;
}

$modulesWithEmptyIcon = [];
while (false !== $row = $statement->fetch()) {
    $modulesWithEmptyIcon[] = $row['name'];
}

if (count($modulesWithEmptyIcon) > 0) {
    TCMSLogChange::addInfoMessage('Some of your module icons were replaced with a standard icon. Please refer to font awesome and select a matching icon. Modules: '.implode(', ',$modulesWithEmptyIcon));
}



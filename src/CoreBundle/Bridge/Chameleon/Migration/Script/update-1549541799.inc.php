<h1>Build #1549541799</h1>
<h2>Date: 2019-02-07</h2>
<div class="changelog">
    - Add CMS main menu items.
</div>
<?php

// Define menu items in human-readably format (second column is c = custom menu item, t = table menu item, m = module menu item),

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\FieldTranslationUtil;

$menuItemDef = <<<EOT
base c Navigation                            contents
base t cms_tpl_page                          contents
base c Media                                 contents
base c Documents                             contents
shop t pkg_shop_primary_navi                 contents
base t pkg_cms_text_block                    contents
base t pkg_multi_module_set                  contents
base t pkg_comment                           contents
base t pkg_comment_type                      contents
base t data_contact_topic                    contents
shop t shop_article                          products
shop t shop_category                         products
shop t shop_attribute                        products
shop t shop_article_marker                   products
shop t shop_article_type                     products
shop t shop_article_group                    products
shop t shop_variant_set                      products
shop t shop_manufacturer                     products
shop t pkg_shop_article_preorder             products
shop t shop_vat                              products
shop t shop_unit_of_measurement              products
shop t shop_contributor                      products
shop t shop_contributor_type                 products
shop t shop_article_document_type            products
shop t pkg_shop_listfilter                   productlists
shop t shop_module_articlelist_orderby       productlists
shop t shop_module_article_list_filter       productlists
shop t pkg_shop_listfilter_item_type         productlists
shop t shop_voucher_series                   discounts
shop t shop_voucher_series_sponsor           discounts
shop t shop_discount                         discounts
base t data_extranet_user                    externalusers
base t data_extranet_group                   externalusers
base t data_extranet                         externalusers
base t data_extranet_salutation              externalusers
shop t shop_order                            orders
shop t shop_order_basket                     orders
shop t pkg_shop_payment_transaction_type     orders
shop t shop_order_status_code                orders
shop t shop_order_step                       checkout
shop t shop_wrapping                         checkout
shop t shop_wrapping_card                    checkout
shop t shop_payment_handler_group            checkout
shop t shop_shipping_type                    checkout
shop t shop_shipping_group                   checkout
shop t shop_shipping_group_handler           checkout
base t data_country                          internationalization
base t cms_locals                            internationalization
shop t pkg_shop_currency                     internationalization
shop t pkg_shop_rating_service_rating        ratings
shop t pkg_shop_rating_service_history       ratings
shop t shop_article_review                   ratings
shop t pkg_shop_rating_service_widget_config ratings
shop t pkg_shop_rating_service               ratings
shop t shop_search_cloud_word                search
shop m articlesearchindex                    search
shop t shop_search_indexer                   search
shop t shop_search_query                     search
shop m shopstats                             analytics
base t pkg_external_tracker                  analytics
base t pkg_cms_changelog_set                 analytics
shop t pkg_shop_statistic_group              analytics
shop t shop_order_export_log                 logs
shop t shop_search_log                       logs
shop t pkg_shop_payment_ipn_message          logs
shop t pkg_shop_payment_ipn_message_trigger  logs
base t data_mail_profile                     communication
base t pkg_newsletter_user                   communication
base t pkg_newsletter_robinson               communication
base t pkg_newsletter_group                  communication
base t pkg_newsletter_campaign               communication
shop t shop                                  system
base t cms_portal                            system
base t cms_cronjobs                          system
base m sanitycheckbundle                     system
base t cms_migration_counter                 system
base m cmsupdatemanager                      system
base t cms_tpl_module                        system
base t pkg_cms_captcha                       system
base t pkg_cms_counter                       system
base t cms_filetype                          system
base t cms_field_type                        system
base t cms_config                            system
base t cms_content_box                       system
base t cms_menu_category                     system
base t cms_menu_custom_item                  system
base t cms_module                            system
base t cms_tbl_conf                          system
base t pkg_cms_class_manager                 system
base m Interface                             dataexchange
base t cms_interface_manager                 dataexchange
base t cms_export_profiles                   dataexchange
base t pkg_csv2sql                           dataexchange
base t pkg_generic_table_export              dataexchange
base t cms_master_pagedef                    layout
base t pkg_cms_theme                         layout
base t pkg_cms_theme_block                   layout
base t cms_message_manager_message_type      layout
shop t shop_variant_display_handler          layout
shop t shop_variant_type_handler             layout
base t cms_config_themes                     layout
base t cms_font_image                        layout
base t cms_url_alias                         routing
base t pkg_cms_routing                       routing
base t cms_smart_url_handler                 routing
base t cms_user                              internalusers
base t cms_usergroup                         internalusers
base t cms_role                              internalusers
base t cms_right                             internalusers
base m CMSUserRightsOverview                 internalusers
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
        'cms_menu_custom_item',
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
$lastCategory = null;
$invalidTableNames = [];
$invalidModuleNames = [];
$invalidCustomMenuItemNames = [];

$isShopSystem = \class_exists(TShop::class);

foreach ($menuItemLines as $menuItemLine) {
    [$systemType, $itemType, $identifier, $category] = \preg_split('#\s+#', $menuItemLine);
    if (false === $isShopSystem && 'shop' === $systemType) {
        continue;
    }

    if ($category !== $lastCategory) {
        $position = 0;
        $lastCategory = $category;
    }
    $menuItemId = TCMSLogChange::createUnusedRecordId('cms_menu_item');
    switch ($itemType) {
        case 't':
            if (false === \array_key_exists($identifier, $tableList)) {
                $invalidTableNames[] = $identifier;
                continue 2;
            }
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
                'icon_font_css_class' => $customMenuItemIconFontCssClasses[$customMenuItemData[$nameFieldNameEn]],
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

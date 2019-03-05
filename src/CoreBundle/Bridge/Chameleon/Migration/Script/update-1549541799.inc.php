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
t pkg_cms_text_block                    contents
t pkg_multi_module_set                  contents
t data_extranet_user                    externalusers
t data_extranet_group                   externalusers
t data_extranet                         externalusers
t data_extranet_salutation              externalusers
t data_country                          internationalization
t cms_locals                            internationalization
t pkg_cms_changelog_set                 analytics
t data_mail_profile                     communication
t pkg_newsletter_user                   communication
t pkg_newsletter_robinson               communication
t pkg_newsletter_group                  communication
t pkg_newsletter_campaign               communication
t cms_portal                            system
t cms_cronjobs                          system
m sanitycheckbundle                     system
t cms_migration_counter                 system
m cmsupdatemanager                      system
t cms_tpl_module                        system
t pkg_cms_captcha                       system
t cms_filetype                          system
t cms_field_type                        system
t cms_config                            system
t cms_content_box                       system
t cms_menu_category                     system
t cms_menu_custom_item                  system
t cms_module                            system
t cms_tbl_conf                          system
t pkg_cms_class_manager                 system
t cms_export_profiles                   dataexchange
t pkg_generic_table_export              dataexchange
t cms_master_pagedef                    layout
t pkg_cms_theme                         layout
t pkg_cms_theme_block                   layout
t cms_message_manager_message_type      layout
t cms_config_themes                     layout
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

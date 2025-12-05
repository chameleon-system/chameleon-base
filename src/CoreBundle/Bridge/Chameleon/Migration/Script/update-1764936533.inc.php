<h1>Build #1764936533</h1>
<h2>Date: 2025-12-05</h2>
<div class="changelog">
    - #65693 : ensure log viewer menu item is present in the logs category
</div>
<?php

$databaseConnection = TCMSLogChange::getDatabaseConnection();

$logCategoryId = $databaseConnection->fetchOne("SELECT `id` FROM `cms_menu_category` WHERE `system_name` = 'logs'");
if (false === $logCategoryId || null === $logCategoryId) {
    TCMSLogChange::addInfoMessage('Menu category "logs" not found - cannot ensure log viewer menu item.', TCMSLogChange::INFO_MESSAGE_LEVEL_ERROR);

    return;
}

$logViewerUrl = '/cms?pagedef=logViewer&_pagedefType=@ChameleonSystemCoreBundle';
$customItemRow = $databaseConnection->fetchAssociative(
    'SELECT `id` FROM `cms_menu_custom_item` WHERE `url` = :url',
    ['url' => $logViewerUrl]
);

if (false === $customItemRow) {
    $customMenuItemId = TCMSLogChange::createUnusedRecordId('cms_menu_custom_item');

    $data = TCMSLogChange::createMigrationQueryData('cms_menu_custom_item', 'en')
        ->setFields([
            'name' => 'Logging Viewer',
            'url' => $logViewerUrl,
            'id' => $customMenuItemId,
        ])
    ;
    TCMSLogChange::insert(__LINE__, $data);

    $data = TCMSLogChange::createMigrationQueryData('cms_menu_custom_item', 'de')
        ->setFields([
            'name' => 'Logging Viewer',
        ])
        ->setWhereEquals([
            'id' => $customMenuItemId,
        ])
    ;
    TCMSLogChange::update(__LINE__, $data);
} else {
    $customMenuItemId = $customItemRow['id'];

    $data = TCMSLogChange::createMigrationQueryData('cms_menu_custom_item', 'en')
        ->setFields([
            'url' => $logViewerUrl,
        ])
        ->setWhereEquals([
            'id' => $customMenuItemId,
        ])
    ;
    TCMSLogChange::update(__LINE__, $data);
}

$menuItemRow = $databaseConnection->fetchAssociative(
    'SELECT `id`, `position` FROM `cms_menu_item` WHERE `target_table_name` = :targetTable AND `target` = :target LIMIT 1',
    [
        'targetTable' => 'cms_menu_custom_item',
        'target' => $customMenuItemId,
    ]
);

$highestPosition = $databaseConnection->fetchOne('SELECT MAX(`position`) FROM `cms_menu_item` WHERE `cms_menu_category_id` = ?', [$logCategoryId]);
if (null === $highestPosition || false === $highestPosition) {
    $highestPosition = 0;
}
$newPosition = (int) $highestPosition + 1;

if (false === $menuItemRow) {
    $menuItemId = TCMSLogChange::createUnusedRecordId('cms_menu_item');

    $data = TCMSLogChange::createMigrationQueryData('cms_menu_item', 'en')
        ->setFields([
            'name' => 'Log Overview',
            'cms_menu_category_id' => $logCategoryId,
            'target' => $customMenuItemId,
            'target_table_name' => 'cms_menu_custom_item',
            'icon_font_css_class' => 'fas fa-clipboard-list',
            'position' => $newPosition,
            'id' => $menuItemId,
        ])
    ;
    TCMSLogChange::insert(__LINE__, $data);

    $data = TCMSLogChange::createMigrationQueryData('cms_menu_item', 'de')
        ->setFields([
            'name' => 'Log Übersicht',
        ])
        ->setWhereEquals([
            'id' => $menuItemId,
        ])
    ;
    TCMSLogChange::update(__LINE__, $data);
} else {
    $menuItemId = $menuItemRow['id'];
    $positionToUse = (int) $menuItemRow['position'];
    if ($positionToUse <= 0) {
        $positionToUse = $newPosition;
    }

    $data = TCMSLogChange::createMigrationQueryData('cms_menu_item', 'en')
        ->setFields([
            'name' => 'Log Overview',
            'cms_menu_category_id' => $logCategoryId,
            'target' => $customMenuItemId,
            'target_table_name' => 'cms_menu_custom_item',
            'icon_font_css_class' => 'fas fa-clipboard-list',
            'position' => $positionToUse,
        ])
        ->setWhereEquals([
            'id' => $menuItemId,
        ])
    ;
    TCMSLogChange::update(__LINE__, $data);

    $data = TCMSLogChange::createMigrationQueryData('cms_menu_item', 'de')
        ->setFields([
            'name' => 'Log Übersicht',
        ])
        ->setWhereEquals([
            'id' => $menuItemId,
        ])
    ;
    TCMSLogChange::update(__LINE__, $data);
}

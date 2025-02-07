<h1>Build #1738335430</h1>
<h2>Date: 2025-01-31</h2>
<div class="changelog">
    - #65530: add table pkg_cms_core_log and pkg_cms_core_log_channel to cms navigation category "Logs"
</div>
<?php

use ChameleonSystem\CoreBundle\ServiceLocator;
$databaseConnection = ServiceLocator::get('database_connection');

$logCategoryRow = $databaseConnection->fetchAssociative("SELECT `id` FROM `cms_menu_category` WHERE `system_name` = 'logs'");
if (false === $logCategoryRow) {
    return;
}

$logCategoryId = $logCategoryRow['id'];

$query = "SELECT `id`, `name`, `target`, `position` FROM `cms_menu_item` WHERE `cms_menu_category_id` = :logCatId";
$menuItemRows = $databaseConnection->fetchAllAssociative($query, ['logCatId' => $logCategoryId]);

$itemLogExists = false;
$itemLogChannelExists = false;
$highestPosition = 0;
foreach ($menuItemRows as $menuItemRow) {
    if ($menuItemRow['target'] === TCMSLogChange::GetTableId('pkg_cms_core_log')) {
        $itemLogExists = true;
    }

    if ($menuItemRow['target'] === TCMSLogChange::GetTableId('pkg_cms_core_log_channel')) {
        $itemLogChannelExists = true;
    }

    if ($highestPosition < $menuItemRow['position']) {
        $highestPosition = $menuItemRow['position'];
    }
}

if (false === $itemLogExists) {
    $newMenuItemId = TCMSLogChange::createUnusedRecordId('cms_menu_item');
    $highestPosition++;

    $data = TCMSLogChange::createMigrationQueryData('cms_menu_item', 'en')
        ->setFields([
            'name' => 'Logs',
            'cms_menu_category_id' => $logCategoryId,
            'id' => $newMenuItemId,
        ])
    ;
    TCMSLogChange::insert(__LINE__, $data);

    $data = TCMSLogChange::createMigrationQueryData('cms_menu_item', 'de')
        ->setFields([
            'name' => 'Logs',
            'target' => TCMSLogChange::GetTableId('pkg_cms_core_log'),
            'target_table_name' => 'cms_tbl_conf',
            'icon_font_css_class' => 'fas fa-table',
            'position' => $highestPosition,
        ])
        ->setWhereEquals([
            'id' => $newMenuItemId,
        ])
    ;
    TCMSLogChange::update(__LINE__, $data);

    $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
        ->setFields([
            'cms_usergroup_id' => TCMSLogChange::GetUserGroupIdByKey('cms_admin'),
        ])
        ->setWhereEquals([
            'name' => 'pkg_cms_core_log',
        ])
    ;
    TCMSLogChange::update(__LINE__, $data);

    TCMSLogChange::SetTableRolePermissions('', 'pkg_cms_core_log', true, [0, 1, 3, 4, 5]);
    TCMSLogChange::SetTableRolePermissions('cms_admin', 'pkg_cms_core_log', true, [2, 6]);
}

if (false === $itemLogChannelExists) {
    $newMenuItemId = TCMSLogChange::createUnusedRecordId('cms_menu_item');
    $highestPosition++;

    $data = TCMSLogChange::createMigrationQueryData('cms_menu_item', 'en')
        ->setFields([
            'name' => 'Logs Channel',
            'cms_menu_category_id' => $logCategoryId,
            'id' => $newMenuItemId,
        ]);
    TCMSLogChange::insert(__LINE__, $data);

    $data = TCMSLogChange::createMigrationQueryData('cms_menu_item', 'de')
        ->setFields([
            'name' => 'Log Channel',
            'target' => TCMSLogChange::GetTableId('pkg_cms_core_log_channel'),
            'target_table_name' => 'cms_tbl_conf',
            'icon_font_css_class' => 'fas fa-box',
            'position' => $highestPosition,
        ])
        ->setWhereEquals([
            'id' => $newMenuItemId,
        ]);
    TCMSLogChange::update(__LINE__, $data);

    $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
        ->setFields([
            'cms_usergroup_id' => TCMSLogChange::GetUserGroupIdByKey('cms_admin'),
        ])
        ->setWhereEquals([
            'name' => 'pkg_cms_core_log_channel',
        ]);
    TCMSLogChange::update(__LINE__, $data);

    TCMSLogChange::SetTableRolePermissions('', 'pkg_cms_core_log_channel', true, [0, 1, 3, 4, 5]);
    TCMSLogChange::SetTableRolePermissions('cms_admin', 'pkg_cms_core_log_channel', true, [2, 6]);
}

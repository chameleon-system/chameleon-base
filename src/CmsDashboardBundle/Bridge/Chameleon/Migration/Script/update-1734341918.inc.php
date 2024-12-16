<h1>Build #000</h1>
<h2>Date: 2024-12-16</h2>
<div class="changelog">
    - add dashboard menu item
</div>
<?php
$dashboardMenuItem = TCMSLogChange::createUnusedRecordId('cms_menu_custom_item');

$data = TCMSLogChange::createMigrationQueryData('cms_menu_custom_item', 'en')
    ->setFields([
        'name' => 'Dashboard',
        'url' => '/cms?pagedef=dashboard&_pagedefType=@ChameleonSystemCmsDashboardBundle',
        'id' => $dashboardMenuItem,
    ])
;
TCMSLogChange::insert(__LINE__, $data);
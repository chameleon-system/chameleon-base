<h1>Build #1738303434</h1>
<h2>Date: 2025-01-31</h2>
<div class="changelog">
    - #65693: add custom menu item
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_menu_custom_item', 'en')
  ->setFields([
      'name' => '',
      'id' => '0f71bd50-e242-155f-7e46-0cb047b9c4f4',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_menu_custom_item', 'en')
  ->setFields([
      'name' => 'Logging Viewer', // prev.: ''
      'url' => '/cms?pagedef=logViewer&_pagedefType=@ChameleonSystemCoreBundle', // prev.: ''
  ])
  ->setWhereEquals([
      'id' => '0f71bd50-e242-155f-7e46-0cb047b9c4f4',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_menu_custom_item', 'de')
    ->setFields([
        'name' => 'Logging Viewer',
    ])
    ->setWhereEquals([
        'id' => '0f71bd50-e242-155f-7e46-0cb047b9c4f4',
    ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_menu_item', 'en')
    ->setFields([
        'name' => '',
        'cms_menu_category_id' => 'e942cc37-4327-92ae-16a1-74e32a191d00',
        'id' => '9a3e5544-8c45-3219-9b89-29c3bdb5bcd9',
    ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_menu_item', 'en')
    ->setFields([
        'name' => 'Log Overview', // prev.: ''
        'target' => '0f71bd50-e242-155f-7e46-0cb047b9c4f4', // prev.: ''
        'icon_font_css_class' => 'fas fa-clipboard-list', // prev.: ''
        'position' => '23', // prev.: '0'
    ])
    ->setWhereEquals([
        'id' => '9a3e5544-8c45-3219-9b89-29c3bdb5bcd9',
    ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_menu_item', 'de')
    ->setFields([
        'name' => 'Log Übersicht',
    ])
    ->setWhereEquals([
        'id' => '9a3e5544-8c45-3219-9b89-29c3bdb5bcd9',
    ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_menu_item', 'en')
    ->setFields([
        'target_table_name' => 'cms_menu_custom_item',
    ])
    ->setWhereEquals([
        'id' => '9a3e5544-8c45-3219-9b89-29c3bdb5bcd9',
    ])
;
TCMSLogChange::update(__LINE__, $data);

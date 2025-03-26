<h1>Build #1742295668</h1>
<h2>Date: 2025-03-18</h2>
<div class="changelog">
    - #65693: change log overview icon
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_menu_item', 'en')
    ->setFields([
//        'name' => 'Log Overview', // prev.: ''
        'icon_font_css_class' => 'fas fa-clipboard-list', // prev.: ''
    ])
    ->setWhereEquals([
        'id' => '9a3e5544-8c45-3219-9b89-29c3bdb5bcd9',
    ])
;
TCMSLogChange::update(__LINE__, $data);
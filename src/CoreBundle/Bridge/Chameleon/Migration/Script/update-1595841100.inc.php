<h1>Build #1595841100</h1>
<h2>Date: 2020-07-27</h2>
<div class="changelog">
    - #574: Hide "rights" for custom menu items: now handled for the page; not used anymore
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'modifier' => 'hidden',
      '049_helptext' => '@deprecated since 6.3.12 - It makes no sense for menu items to have rights (instead pages now have).',
  ])
  ->setWhereEquals([
      'name' => 'cms_right_mlt',
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_menu_custom_item'),
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        '049_helptext' => '@deprecated since 6.3.12 - Menu-Einträge benötigen keine Rechte, sondern nur das, worauf sie zeigen (jetzt Seiten).',
    ])
    ->setWhereEquals([
        'name' => 'cms_right_mlt',
        'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_menu_custom_item'),
    ])
;
TCMSLogChange::update(__LINE__, $data);

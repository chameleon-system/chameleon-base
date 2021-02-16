<h1>Build #1613471248</h1>
<h2>Date: 2021-02-16</h2>
<div class="changelog">
    - #648: Rename/delete menu entries
</div>
<?php

// TODO does this work? referencing a translatable field?
$data = TCMSLogChange::createMigrationQueryData('cms_menu_item', 'de')
  ->setWhereEquals([
      'name' => 'CMS Themes'
  ])
;
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_menu_item', 'de')
  ->setFields([
      'name' => 'Themes',
  ])
  ->setWhereEquals([
      'name' => 'Website Themes',
  ])
;
TCMSLogChange::update(__LINE__, $data);

// TODO does this work? Or are there no entries left after the above statement?
$data = TCMSLogChange::createMigrationQueryData('cms_menu_item', 'en')
    ->setFields([
        'name' => 'Themes',
    ])
    ->setWhereEquals([
        'name' => 'Website themes',
    ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'en')
    ->setFields([
        'translation' => 'Themes',
    ])
    ->setWhereEquals([
        'name' => 'pkg_cms_theme',
    ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
  ->setFields([
      'translation' => 'Themes',
  ])
  ->setWhereEquals([
      'name' => 'pkg_cms_theme',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        '049_helptext' => '@Deprecated Only "Themes" are now used for front- and backend.',
    ])
    ->setWhereEquals([
        'id' => TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_config'), 'cms_config_themes_id'),
    ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      '049_helptext' => '@Deprecated Nur noch "Themes" werden jetzt für Front- und Backend verwendet.',
  ])
  ->setWhereEquals([
      'id' => TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_config'), 'cms_config_themes_id'),
  ])
;
TCMSLogChange::update(__LINE__, $data);


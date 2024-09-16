<h1>Build #1704292025</h1>
<h2>Date: 2024-01-03</h2>
<div class="changelog">
    - ref #838: set new icon field for main menu icon fields
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
//      'translation' => 'Icon-Font CSS-Klasse',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_ICON'),
      'fieldtype_config' => 'iconFontCssUrls=/chameleon/blackbox/iconFonts/fontawesome-free-5.8.1/css/all.css',
  ])
  ->setWhereEquals([
      'name' => 'icon_font_css_class',
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_menu_category')
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
//      'translation' => 'Icon Font CSS-Klasse',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_ICON'),
      'fieldtype_config' => 'iconFontCssUrls=/chameleon/blackbox/iconFonts/fontawesome-free-5.8.1/css/all.css',
  ])
  ->setWhereEquals([
      'name' => 'icon_font_css_class',
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_menu_item')
  ])
;
TCMSLogChange::update(__LINE__, $data);

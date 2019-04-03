<h1>Build #1554106531</h1>
<h2>Date: 2019-04-01</h2>
<div class="changelog">
    - add CSS icon font field, deprecate old icon field in frontend modules
</div>
<?php

$fieldId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_tpl_module'), 'icon_list');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
//      'name' => 'icon_list',
//      'translation' => 'Icon',
      '049_helptext' => '@deprecated since 7.0.0 - no longer used - use icon_font_css_class instead',
  ])
  ->setWhereEquals([
      'id' => $fieldId,
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
//      'name' => 'icon_list',
        '049_helptext' => '@deprecated since 7.0.0 - no longer used - use icon_font_css_class instead',
    ])
    ->setWhereEquals([
        'id' => $fieldId,
    ])
;
TCMSLogChange::update(__LINE__, $data);

$query = "ALTER TABLE `cms_tpl_module`
                     CHANGE `icon_list`
                            `icon_list` VARCHAR(255) DEFAULT 'application.png' NOT NULL COMMENT 'Icon: @deprecated since 7.0.0 - no longer used - use icon_font_css_class instead'";
TCMSLogChange::RunQuery(__LINE__, $query);

$recordId = TCMSLogChange::createUnusedRecordId();

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_tpl_module'),
      'name' => 'icon_font_css_class',
      'translation' => 'Icon-Font CSS-Klasse',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING'),
      'cms_tbl_field_tab' => '',
      'isrequired' => '0',
      'fieldclass' => '',
      'fieldclass_subtype' => '',
      'class_type' => 'Core',
      'modifier' => 'none',
      'field_default_value' => '',
      'length_set' => '',
      'fieldtype_config' => '',
      'restrict_to_groups' => '0',
      'field_width' => '',
      'position' => '0',
      '049_helptext' => 'In diesem Feld kann ein Font-Icon angegeben werden. Tragen Sie hier den Klassennamen ein, z.B. für Font Awesome: fas fa-check',
      'row_hexcolor' => '',
      'is_translatable' => '0',
      'validation_regex' => '',
      'id' => $recordId,
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$query = "ALTER TABLE `cms_tpl_module`
                        ADD `icon_font_css_class` VARCHAR(255) NOT NULL COMMENT 'Icon-Font CSS-Class: This field is used for a Font-Icon. Fill in the class name here, for example for Font Awesome: fas fa-check'";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
//        'name' => 'icon_font_css_class',
        'translation' => 'Icon font CSS class',
        '049_helptext' => 'This field is used for a font icon. Fill in the class name here, for example for Font Awesome: fas fa-check',
    ])
    ->setWhereEquals([
        'id' => $recordId,
    ])
;
TCMSLogChange::update(__LINE__, $data);

TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('cms_tpl_module'), 'icon_font_css_class', 'icon_list');

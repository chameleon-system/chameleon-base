<h1>Build #1549624862</h1>
<h2>Date: 2019-02-08</h2>
<div class="changelog">
    - Mark old main menu rendering fields as deprecated.
</div>
<?php

// cms_tbl_conf

$fieldId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_tbl_conf'), 'cms_content_box_id');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      '049_helptext' => '@deprecated since 6.3.0 - use sidebar main menu items instead',
  ])
  ->setWhereEquals([
      'id' => $fieldId,
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        '049_helptext' => '@deprecated since 6.3.0 - use sidebar main menu items instead',
    ])
    ->setWhereEquals([
        'id' => $fieldId,
    ])
;
TCMSLogChange::update(__LINE__, $data);

$fieldId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_tbl_conf'), 'icon_font_css_class');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      '049_helptext' => 'The field is used to display a font icon to the menu item of the table. Fill in the class name here, for example for Font Awesome: fas fa-check

@deprecated since 6.3.0 - configure CSS classes in the main menu item configuration instead.',
  ])
  ->setWhereEquals([
      'id' => $fieldId,
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        '049_helptext' => 'Dieses Feld wird verwendet, um bei Menüeinträgen ein Icon aus dem genutzten Icon Font anzuzeigen. Beispiel für ein Icon aus Font Awesome: fas fa-check

@deprecated since 6.3.0 - configure CSS classes in the main menu item configuration instead.',
    ])
    ->setWhereEquals([
        'id' => $fieldId,
    ])
;
TCMSLogChange::update(__LINE__, $data);

// cms_module

$fieldId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_module'), 'cms_content_box_id');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        '049_helptext' => '@deprecated since 6.3.0 - use sidebar main menu items instead',
    ])
    ->setWhereEquals([
        'id' => $fieldId,
    ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        '049_helptext' => '@deprecated since 6.3.0 - use sidebar main menu items instead',
    ])
    ->setWhereEquals([
        'id' => $fieldId,
    ])
;
TCMSLogChange::update(__LINE__, $data);

$fieldId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_module'), 'icon_font_css_class');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        '049_helptext' => 'The field is used to display a font icon to the menu item of the module. Fill in the class name here, for example for Font Awesome: fas fa-check

@deprecated since 6.3.0 - configure CSS classes in the main menu item configuration instead.',
    ])
    ->setWhereEquals([
        'id' => $fieldId,
    ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        '049_helptext' => 'Dieses Feld wird verwendet, um bei Menüeinträgen ein Icon aus dem genutzten Icon Font anzuzeigen. Beispiel für ein Icon aus Font Awesome: fas fa-check

@deprecated since 6.3.0 - configure CSS classes in the main menu item configuration instead.',
    ])
    ->setWhereEquals([
        'id' => $fieldId,
    ])
;
TCMSLogChange::update(__LINE__, $data);

<h1>Build #1574688419</h1>
<h2>Date: 2019-11-25</h2>
<div class="changelog">
    - #508: Connect user to
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'name' => 'cms_menu_item_mlt',
      'translation' => 'Verwendete Menü-Einträge',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_MULTITABLELIST'),
      'cms_tbl_field_tab' => '',
      'isrequired' => '0',
      'fieldclass' => '',
      'modifier' => 'none',
      'field_default_value' => '',
      'length_set' => '',
      'fieldtype_config' => '',
      'restrict_to_groups' => '0',
      'field_width' => '0',
      'position' => '2180',
      '049_helptext' => '',
      'row_hexcolor' => '',
      'is_translatable' => '0',
      'validation_regex' => '',
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_user'),
      'fieldclass_subtype' => '',
      'class_type' => 'Core',
      'id' => 'd0cdc23c-d752-5fc3-88a4-2fc49b5fb909',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('cms_user'), 'cms_menu_item_mlt', 'show_as_rights_template');

$query ="CREATE TABLE `cms_user_cms_menu_item_mlt` (
                  `source_id` CHAR( 36 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
                  `target_id` CHAR( 36 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
                  `entry_sort` int(11) NOT NULL default '0',
                  PRIMARY KEY ( `source_id` , `target_id` ),
                  INDEX (target_id),
                  INDEX (entry_sort)
                ) ENGINE = InnoDB";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields(
        [
            'translation' => 'Used menu entries',
        ]
    )
    ->setWhereEquals(
        [
            'id' => 'd0cdc23c-d752-5fc3-88a4-2fc49b5fb909',
        ]
    );
TCMSLogChange::update(__LINE__, $data);

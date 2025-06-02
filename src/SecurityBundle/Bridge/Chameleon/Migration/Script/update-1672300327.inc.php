<h1>Build #1672300327</h1>
<h2>Date: 2022-12-29</h2>
<div class="changelog">
    - add a last modified field to the cms user so that we can reload the user in the session only if the users data changed.
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_user'),
      'fieldclass_subtype' => '',
      'class_type' => 'Core',
      'id' => '1b827bd1-b3bb-dd1f-ab54-293c08ab3c73',
      'name' => 'date_modified',
      'translation' => 'Letzte änderung',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_TIMESTAMP'),
      'cms_tbl_field_tab' => '',
      'isrequired' => '0',
      'fieldclass' => '',
      'modifier' => 'none',
      'field_default_value' => '',
      'length_set' => '',
      'fieldtype_config' => '',
      'restrict_to_groups' => '0',
      'field_width' => '0',
      'position' => '2174',
      '049_helptext' => 'Wann wurde der Datensatz zuletzt geändert',
      'row_hexcolor' => '',
      'is_translatable' => '0',
      'validation_regex' => '',
  ])
;
TCMSLogChange::insert(__LINE__, $data);
$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'translation' => 'Last modified',
        '049_helptext' => 'When was the record last changed',
    ])->setWhereEquals(['id' => '1b827bd1-b3bb-dd1f-ab54-293c08ab3c73'])
;
TCMSLogChange::update(__LINE__, $data);

$query = "ALTER TABLE `cms_user`
                        ADD `date_modified` TIMESTAMP NOT NULL COMMENT 'Letzte änderung: Wann wurde der Datensatz zuletzt geändert'";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf_cms_usergroup_mlt', 'de')
  ->setFields([
      'source_id' => '1b827bd1-b3bb-dd1f-ab54-293c08ab3c73',
      'target_id' => '6',
      'entry_sort' => '0',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

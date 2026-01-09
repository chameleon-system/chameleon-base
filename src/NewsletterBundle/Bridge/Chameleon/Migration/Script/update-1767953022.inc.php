<h1>Build #1767953022</h1>
<h2>Date: 2026-01-09</h2>
<div class="changelog">
    - #69137: add pre header field to newsletters
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('pkg_newsletter_campaign'),
      'name' => 'new_field',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING'),
      'id' => 'a81dfb71-e9bf-bcd8-53d3-8db2577501cb',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$query ="ALTER TABLE `pkg_newsletter_campaign`
                        ADD `new_field` VARCHAR(255) NOT NULL";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'name' => 'preheader', // prev.: 'new_field'
      'translation' => 'Pre-Header', // prev.: ''
      'position' => '3247', // prev.: '0'
  ])
  ->setWhereEquals([
      'id' => 'a81dfb71-e9bf-bcd8-53d3-8db2577501cb',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
        ->setFields([
                'translation' => 'Pre-Header', // prev.: ''
        ])
        ->setWhereEquals([
                'id' => 'a81dfb71-e9bf-bcd8-53d3-8db2577501cb',
        ])
;
TCMSLogChange::update(__LINE__, $data);

$query ="ALTER TABLE `pkg_newsletter_campaign`
                     CHANGE `new_field`
                            `preheader` VARCHAR(255) NOT NULL COMMENT 'Pre-Header: '";
TCMSLogChange::RunQuery(__LINE__, $query);

TCMSLogChange::SetFieldPosition('pkg_newsletter_campaign', 'preheader', 'cms_portal_id');
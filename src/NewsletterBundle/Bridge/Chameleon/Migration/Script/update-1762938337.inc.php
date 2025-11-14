<h1>Build #1762938337</h1>
<h2>Date: 2025-11-12</h2>
<div class="changelog">
    - #68412: add newsletter with import field to newsletter campaigns
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('pkg_newsletter_campaign'),
      'name' => 'new_field',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING'),
      'id' => 'af3cdf0b-328d-69eb-e590-87a93c2d182a',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$query ="ALTER TABLE `pkg_newsletter_campaign`
                        ADD `new_field` VARCHAR(255) NOT NULL";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'name' => 'newsletter_with_import', // prev.: 'new_field'
      'translation' => 'Newsletter Import', // prev.: ''
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_WYSIWYG_WITH_NEWSLETTER_IMPORT'), // prev.: '34'
      'position' => '3245', // prev.: '0'
      'modifier' => 'readonly',
  ])
  ->setWhereEquals([
      'id' => 'af3cdf0b-328d-69eb-e590-87a93c2d182a',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'translation' => 'Import Newsletter', // prev.: ''
    ])
    ->setWhereEquals([
        'id' => 'af3cdf0b-328d-69eb-e590-87a93c2d182a',
    ])
;
TCMSLogChange::update(__LINE__, $data);

$query ="ALTER TABLE `pkg_newsletter_campaign`
                     CHANGE `new_field`
                            `newsletter_with_import` LONGTEXT NOT NULL COMMENT 'Importierter Newsletter: '";
TCMSLogChange::RunQuery(__LINE__, $query);


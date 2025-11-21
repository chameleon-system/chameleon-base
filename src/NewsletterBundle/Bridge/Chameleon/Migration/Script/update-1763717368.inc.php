<h1>Build #1763717368</h1>
<h2>Date: 2025-11-21</h2>
<div class="changelog">
    -
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('pkg_newsletter_campaign'),
      'name' => 'new_field',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING'),
      'id' => 'd84b7e97-141e-f18b-17d5-966ed4f1c46c',
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
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_WYSIWYG'), // prev.: '34'
      'position' => '3246', // prev.: '0'
  ])
  ->setWhereEquals([
      'id' => 'd84b7e97-141e-f18b-17d5-966ed4f1c46c',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$query ="ALTER TABLE `pkg_newsletter_campaign`
                     CHANGE `new_field`
                            `newsletter_with_import` LONGTEXT NOT NULL COMMENT 'Newsletter Import: '";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      // 'name' => 'newsletter_with_html_import',
      'fieldclass' => 'ChameleonSystem\NewsletterBundle\Bridge\Chameleon\TCMSFields\FieldWysiwygNewsletterHtmlImport', // prev.: ''
  ])
  ->setWhereEquals([
      'id' => 'd84b7e97-141e-f18b-17d5-966ed4f1c46c',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$query ="ALTER TABLE `pkg_newsletter_campaign`
                     CHANGE `newsletter_with_import`
                            `newsletter_with_import` LONGTEXT NOT NULL COMMENT 'Newsletter Import: '";
TCMSLogChange::RunQuery(__LINE__, $query);

TCMSLogChange::addToSnippetChain('@ChameleonSystemNewsletterBundle/Resources/views', '@ChameleonSystemCoreBundle/Resources/views', ['5f047d9b-0c20-0bfb-2dce-f8193653965c']);
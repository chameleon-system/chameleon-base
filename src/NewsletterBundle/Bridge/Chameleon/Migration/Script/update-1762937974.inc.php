<h1>Build #1762937974</h1>
<h2>Date: 2025-11-12</h2>
<div class="changelog">
    - #68412: add new field type "WYSIWYG mit Newsletter Import"
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'de')
  ->setFields([
      'constname' => '_COPY',
      'id' => '26adde88-5924-c539-58b4-8cbd346c6fd1',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'de')
  ->setFields([
      '049_trans' => 'WYSIWYG mit Newsletter Import', // prev.: ''
      'constname' => 'CMSFIELD_WYSIWYG_WITH_NEWSLETTER_IMPORT', // prev.: '_COPY'
      'mysql_type' => 'LONGTEXT', // prev.: ''
      'fieldclass' => 'ChameleonSystem\NewsletterBundle\Bridge\Chameleon\TCMSFields\TCMSFieldsWysiwygWithNewsletterImport_TCMSField', // prev.: ''
      'contains_images' => '1', // prev.: '0'
  ])
  ->setWhereEquals([
      'id' => '26adde88-5924-c539-58b4-8cbd346c6fd1',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'en')
    ->setFields([
        '049_trans' => 'WYSIWYG with Newsletter Import',
    ])
    ->setWhereEquals([
        'id' => '26adde88-5924-c539-58b4-8cbd346c6fd1',
    ])
;
TCMSLogChange::update(__LINE__, $data);

TCMSLogChange::addToSnippetChain('@ChameleonSystemNewsletterBundle/Resources/views', '@ChameleonSystemCoreBundle/Resources/views', ['5f047d9b-0c20-0bfb-2dce-f8193653965c']); //Add to Backend Theme

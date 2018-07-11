<h1>Build #1530091957</h1>
<h2>Date: 2018-06-27</h2>
<div class="changelog">
    - Add English names for modules
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'en')
  ->setFields([
      'name' => 'Wizard',
  ])
  ->setWhereEquals([
      'classname' => 'MTCMSWizard',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'en')
  ->setFields([
      'name' => 'CMS navigation',
  ])
  ->setWhereEquals([
      'classname' => 'MTPkgCmsNavigation',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'en')
  ->setFields([
      'name' => 'Sub-navigation',
  ])
  ->setWhereEquals([
      'classname' => 'MTPkgCmsSubNavigation',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'en')
  ->setFields([
      'name' => 'Comments',
  ])
  ->setWhereEquals([
      'classname' => 'MTPkgComment',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'en')
  ->setFields([
      'name' => 'Search',
  ])
  ->setWhereEquals([
      'classname' => 'MTSearch',
  ])
;
TCMSLogChange::update(__LINE__, $data);

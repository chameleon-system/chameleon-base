<h1>Build #1520413560</h1>
<h2>Date: 2018-03-07</h2>
<div class="changelog">
    - Set module location for core CMS modules
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_module', 'en')
  ->setFields([
      'module_location' => '@ChameleonSystemCoreBundle',
  ])
  ->setWhereEquals([
      'module_location' => 'Core',
      'uniquecmsname' => 'cmsupdatemanager',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_module', 'en')
  ->setFields([
      'module_location' => '@ChameleonSystemCoreBundle',
  ])
  ->setWhereEquals([
      'module_location' => 'Core',
      'uniquecmsname' => 'Interface',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_module', 'en')
  ->setFields([
      'module_location' => '@ChameleonSystemCoreBundle',
  ])
  ->setWhereEquals([
      'module_location' => 'Core',
      'uniquecmsname' => 'CMSUserRightsOverview',
  ])
;
TCMSLogChange::update(__LINE__, $data);

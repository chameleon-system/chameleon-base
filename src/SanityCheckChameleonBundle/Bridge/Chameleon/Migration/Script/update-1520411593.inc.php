<h1>Build #1520411593</h1>
<h2>Date: 2018-03-07</h2>
<div class="changelog">
    - Set module location for CMS module.
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_module', 'de')
  ->setFields([
      'module_location' => '@ChameleonSystemSanityCheckChameleonBundle',
  ])
  ->setWhereEquals([
      'uniquecmsname' => 'sanitycheckbundle',
  ])
;
TCMSLogChange::update(__LINE__, $data);

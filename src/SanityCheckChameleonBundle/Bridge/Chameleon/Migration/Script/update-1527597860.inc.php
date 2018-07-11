<h1>Build #1527597860</h1>
<h2>Date: 2018-05-29</h2>
<div class="changelog">
    - Add English name for SanityCheck module.
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_module', 'en')
  ->setFields([
      'name' => 'SanityCheck',
  ])
  ->setWhereEquals([
      'uniquecmsname' => 'sanitycheckbundle',
  ])
;
TCMSLogChange::update(__LINE__, $data);


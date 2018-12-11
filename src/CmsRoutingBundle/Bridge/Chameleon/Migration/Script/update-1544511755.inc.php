<h1>Build #1544511755</h1>
<h2>Date: 2018-12-11</h2>
<div class="changelog">
    - #95: Add a table editor for routing (for clear event)
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
  ->setFields([
      'table_editor_class' => '\ChameleonSystem\CmsRoutingBundle\Bridge\Chameleon\CmsRoutingTableEditor',
  ])
  ->setWhereEquals([
      'name' => 'pkg_cms_routing',
  ])
;
TCMSLogChange::update(__LINE__, $data);


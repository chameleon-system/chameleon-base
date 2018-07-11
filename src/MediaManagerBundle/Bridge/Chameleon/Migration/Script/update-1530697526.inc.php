<h1>Build #1530697526</h1>
<h2>Date: 2018-07-04</h2>
<div class="changelog">
    - Fix media manager table editor
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
  ->setFields([
      'table_editor_class' => '\ChameleonSystem\MediaManagerBundle\Bridge\Chameleon\TableEditor\CmsMediaTreeTableEditor',
  ])
  ->setWhereEquals([
      'name' => 'cms_media_tree',
  ])
;
TCMSLogChange::update(__LINE__, $data);

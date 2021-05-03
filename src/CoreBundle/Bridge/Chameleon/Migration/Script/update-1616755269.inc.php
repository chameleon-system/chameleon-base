<h1>Build #1616755269</h1>
<h2>Date: 2021-03-26</h2>
<div class="changelog">
    - 695: Validate Twig Syntax in Mail Templates
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
  ->setFields([
      'table_editor_class' => '\ChameleonSystem\CoreBundle\Bridge\Chameleon\TableEditor\DataMailProfileTableEditor',
  ])
  ->setWhereEquals([
      'name' => 'data_mail_profile',
  ])
;
TCMSLogChange::update(__LINE__, $data);


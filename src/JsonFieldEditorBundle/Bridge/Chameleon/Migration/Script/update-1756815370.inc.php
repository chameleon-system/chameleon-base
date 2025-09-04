<h1>Build #1756815370</h1>
<h2>Date: 2025-09-02</h2>
<div class="changelog">
    - #67553: add new cms field for jeson editor
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'de')
  ->setFields([
      '049_trans' => 'Json Editor',
      'fieldclass' => '\ChameleonSystem\JsonFieldEditorBundle\Bridge\Chameleon\Field\TCMSFieldJsonEditor',
      'constname' => 'CMSFIELD_JSON_EDITOR',
      'mysql_type' => 'CHAR',
      'id' => '2508ec05-20d4-05ee-2c7a-08eb97658933',
  ])
;
TCMSLogChange::insert(__LINE__, $data);


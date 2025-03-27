<h1>Build #1743063257</h1>
<h2>Date: 2025-03-27</h2>
<div class="changelog">
    - ref #66153: add FieldMapperChain to cms_tpl_module.mapper_chain
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      // 'name' => 'mapper_chain',
      'fieldclass' => 'ChameleonSystem\CoreBundle\Field\FieldMapperChain',
  ])
  ->setWhereEquals([
      'name' => 'mapper_chain',
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_tpl_module'),
  ])
;
TCMSLogChange::update(__LINE__, $data);


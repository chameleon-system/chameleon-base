<h1>Build #1743066432</h1>
<h2>Date: 2025-03-27</h2>
<div class="changelog">
    - ref #66153: add FieldThemePaths to pkg_cms_theme.snippet_chain
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      // 'name' => 'snippet_chain',
      'fieldclass' => 'ChameleonSystem\CoreBundle\Field\FieldThemePaths', // prev.: ''
  ])
  ->setWhereEquals([
      'name' => 'snippet_chain',
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('pkg_cms_theme'),
  ])
;
TCMSLogChange::update(__LINE__, $data);


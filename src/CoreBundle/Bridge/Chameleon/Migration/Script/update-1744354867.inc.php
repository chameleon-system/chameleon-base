<h1>Build #1744354867</h1>
<h2>Date: 2025-04-11</h2>
<div class="changelog">
    - ref #66330: set helptext field in fields to markdown
</div>
<?php
TCMSLogChange::requireBundleUpdates('ChameleonSystemMarkdownCmsBundle', 1705324190);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      // 'name' => '049_helptext',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_MARKDOWNTEXT'), // prev.: '5'
  ])
  ->setWhereEquals([
      'id' => '646',
  ])
;
TCMSLogChange::update(__LINE__, $data);
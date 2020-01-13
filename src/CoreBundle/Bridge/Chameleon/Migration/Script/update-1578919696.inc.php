<h1>Build #1578919696</h1>
<h2>Date: 2020-01-13</h2>
<div class="changelog">
    - #107: new jstree-plugin V3.3.8 with checkboxes: FieldTreeNodePortalSelect allows only selection on portals
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
//      'translation' => 'Navigationsstartpunkt',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_TREE'),
      'fieldclass' => 'ChameleonSystem\CoreBundle\Field\FieldTreeNodePortalSelect',
  ])
  ->setWhereEquals([
      'name' => 'main_node_tree',
  ])
;
TCMSLogChange::update(__LINE__, $data);


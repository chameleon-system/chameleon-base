<h1>Build #1591105578</h1>
<h2>Date: 2020-06-02</h2>
<div class="changelog">
    - #574: (somewhat related) Document groups field for template modules correctly
</div>
<?php

$userGroupsFieldId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_tpl_module'), 'cms_usergroup_mlt');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      '049_helptext' => 'Nur für diese (Backend-) Gruppen im Template-Engine-Editor verfügbar.',
  ])
  ->setWhereEquals([
      'id' => $userGroupsFieldId,
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        '049_helptext' => 'Only shown for these (backend) groups in the template engine editor.',
    ])
    ->setWhereEquals([
        'id' => $userGroupsFieldId,
    ])
;
TCMSLogChange::update(__LINE__, $data);

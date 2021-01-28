<h1>Build #1554723572</h1>
<h2>Date: 2019-04-08</h2>
<div class="changelog">
    - Remove workflow and revision management user groups.
</div>
<?php

use Doctrine\Common\Collections\Expr\Comparison;

$databaseConnection = TCMSLogChange::getDatabaseConnection();

$groupsRaw = $databaseConnection->fetchAll("SELECT `id` FROM `cms_usergroup` WHERE `internal_identifier` IN ('cms_revision_management', 'publishing_workflow')");
$groups = [];
foreach ($groupsRaw as $groupRow) {
    $groups[] = $groupRow['id'];
}

if (0 === \count($groups)) {
    return;
}

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'en')
  ->setFields([
      'cms_usergroup_id' => '',
  ])
  ->setWhereExpressions([
      new Comparison('cms_usergroup_id', Comparison::IN, $groups),
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_user_cms_usergroup_mlt', 'en')
    ->setWhereExpressions([
        new Comparison('target_id', Comparison::IN, $groups),
    ])
;
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_usergroup', 'en')
    ->setWhereExpressions([
        new Comparison('id', Comparison::IN, $groups),
    ])
;
TCMSLogChange::delete(__LINE__, $data);

<h1>Build #1554724063</h1>
<h2>Date: 2019-04-08</h2>
<div class="changelog">
    - Remove workflow and revision management roles.
</div>
<?php

use Doctrine\Common\Collections\Expr\Comparison;

$databaseConnection = TCMSLogChange::getDatabaseConnection();

$rolesRaw = $databaseConnection->fetchAll("SELECT `id` FROM `cms_role` WHERE `name` IN ('cms_revision_author', 'workflow_publisher', 'workflow_user')");
$roles = [];
foreach ($rolesRaw as $roleRow) {
    $roles[] = $roleRow['id'];
}

if (0 === \count($roles)) {
    return;
}

$data = TCMSLogChange::createMigrationQueryData('cms_role_cms_role_mlt', 'en')
    ->setWhereExpressions([
        new Comparison('source_id', Comparison::IN, $roles),
    ])
;
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_role_cms_right_mlt', 'en')
    ->setWhereExpressions([
        new Comparison('source_id', Comparison::IN, $roles),
    ])
;
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf_cms_role_mlt', 'en')
    ->setWhereExpressions([
        new Comparison('target_id', Comparison::IN, $roles),
    ])
;
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf_cms_role1_mlt', 'en')
    ->setWhereExpressions([
        new Comparison('target_id', Comparison::IN, $roles),
    ])
;
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf_cms_role2_mlt', 'en')
    ->setWhereExpressions([
        new Comparison('target_id', Comparison::IN, $roles),
    ])
;
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf_cms_role3_mlt', 'en')
    ->setWhereExpressions([
        new Comparison('target_id', Comparison::IN, $roles),
    ])
;
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_user_cms_role_mlt', 'en')
    ->setWhereExpressions([
        new Comparison('target_id', Comparison::IN, $roles),
    ])
;
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_role', 'en')
    ->setWhereExpressions([
        new Comparison('id', Comparison::IN, $roles),
    ])
;
TCMSLogChange::delete(__LINE__, $data);

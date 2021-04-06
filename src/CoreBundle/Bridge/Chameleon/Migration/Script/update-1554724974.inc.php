<h1>Build #1554724974</h1>
<h2>Date: 2019-04-08</h2>
<div class="changelog">
    - Remove workflow and revision management rights.
</div>
<?php

use Doctrine\Common\Collections\Expr\Comparison;

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'modifier' => 'hidden',
    ])
    ->setWhereEquals([
        'id' => TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_tbl_conf'), 'cms_role7_mlt'),
    ])
;
TCMSLogChange::update(__LINE__, $data);

$databaseConnection = TCMSLogChange::getDatabaseConnection();

$rightsRaw = $databaseConnection->fetchAll("SELECT `id` FROM `cms_right` WHERE `name` IN ('cms_revision_management', 'cms_revision_on_workflow_publish', 'workflow_active', 'workflow_publish_own_changes')");
$rights = [];
foreach ($rightsRaw as $rightRow) {
    $rights[] = $rightRow['id'];
}

if (0 === \count($rights)) {
    return;
}

$data = TCMSLogChange::createMigrationQueryData('cms_role_cms_right_mlt', 'en')
    ->setWhereExpressions([
        new Comparison('target_id', Comparison::IN, $rights),
    ])
;
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_right', 'en')
    ->setWhereExpressions([
        new Comparison('id', Comparison::IN, $rights),
    ])
;
TCMSLogChange::delete(__LINE__, $data);

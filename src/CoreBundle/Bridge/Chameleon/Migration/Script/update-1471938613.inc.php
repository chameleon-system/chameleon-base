<h1>core - Build #1471938613</h1>
<h2>Date: 2016-08-23</h2>
<div class="changelog">
    - remove workflow tables
</div>
<?php

use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\DBAL\Connection;

$tableNameList = [
    'cms_workflow_action',
    'cms_workflow_actiontype',
    'cms_workflow_status',
    'cms_workflow_transaction',
    'cms_workflow_transaction_log',
];

$tableIdList = [];
foreach ($tableNameList as $tableName) {
    $tableIdList[] = TCMSLogChange::GetTableId($tableName);
}

$databaseConnection = TCMSLogChange::getDatabaseConnection();
$query = 'SELECT `id` FROM `cms_field_conf` WHERE `cms_tbl_conf_id` IN (:tableIdList)';
$result = $databaseConnection->executeQuery($query, [
      'tableIdList' => $tableIdList,
  ], [
      'tableIdList' => Connection::PARAM_STR_ARRAY,
  ]
);

$fieldIdList = [];
while ($row = $result->fetchOne()) {
    $fieldIdList[] = $row;
}

$tablesToCleanWithFieldIdList = [
    'cms_field_conf_cms_usergroup_mlt' => 'source_id',
    'cms_field_conf' => 'id',
];

foreach ($tablesToCleanWithFieldIdList as $tableName => $identifier) {
    $data = TCMSLogChange::createMigrationQueryData($tableName, 'en')
        ->setWhereExpressions([
            new Comparison($identifier, Comparison::IN, $fieldIdList),
        ]);
    TCMSLogChange::delete(__LINE__, $data);
}

$tablesToCleanWithTableIdList = [
    'cms_tbl_conf_cms_role_mlt' => 'source_id',
    'cms_tbl_conf_cms_role1_mlt' => 'source_id',
    'cms_tbl_conf_cms_role2_mlt' => 'source_id',
    'cms_tbl_conf_cms_role3_mlt' => 'source_id',
    'cms_tbl_conf_cms_role4_mlt' => 'source_id',
    'cms_tbl_conf_cms_role5_mlt' => 'source_id',
    'cms_tbl_conf_cms_role7_mlt' => 'source_id',
    'cms_tbl_display_list_fields' => 'cms_tbl_conf_id',
    'cms_tbl_list_class' => 'cms_tbl_conf_id',
    'cms_tbl_conf' => 'id',
];

foreach ($tablesToCleanWithTableIdList as $tableName => $identifier) {
    $data = TCMSLogChange::createMigrationQueryData($tableName, 'en')
        ->setWhereExpressions([
            new Comparison($identifier, Comparison::IN, $tableIdList),
        ]);
    TCMSLogChange::delete(__LINE__, $data);
}

foreach ($tableNameList as $tableName) {
    $query = sprintf('DROP TABLE %s', $databaseConnection->quoteIdentifier($tableName));
    TCMSLogChange::RunQuery(__LINE__, $query);
}

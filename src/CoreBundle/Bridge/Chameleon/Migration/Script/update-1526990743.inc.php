<h1>Build #1526990743</h1>
<h2>Date: 2018-05-22</h2>
<div class="changelog">
    - Convert tables from MyISAM to InnoDB
</div>
<?php

use ChameleonSystem\CoreBundle\ServiceLocator;

$databaseConnection = TCMSLogChange::getDatabaseConnection();

$databaseName = ServiceLocator::getParameter('database_name');
$fieldQuotedDatabaseName = $databaseConnection->quoteIdentifier($databaseName);
$valueQuotedDatabaseName = $databaseConnection->quote($databaseName);

$query = "(SELECT chameleon_tables.`name`
          FROM `INFORMATION_SCHEMA`.`TABLES` AS mysql_tables
          JOIN $fieldQuotedDatabaseName.`cms_tbl_conf` AS chameleon_tables
          ON mysql_tables.`TABLE_NAME` = chameleon_tables.`name`
          WHERE mysql_tables.ENGINE = 'MyISAM'
          AND mysql_tables.`TABLE_SCHEMA` = $valueQuotedDatabaseName)
          UNION
          (SELECT mysql_tables.`TABLE_NAME` AS name
          FROM `INFORMATION_SCHEMA`.`TABLES` AS mysql_tables
          WHERE mysql_tables.`ENGINE` = 'MyISAM'
          AND mysql_tables.`TABLE_NAME` LIKE '%\_mlt'
          AND mysql_tables.`TABLE_SCHEMA` = $valueQuotedDatabaseName)
          ";

$tablesToConvert = $databaseConnection->fetchAll($query);
foreach ($tablesToConvert as $row) {
    $quotedTableName = $databaseConnection->quoteIdentifier($row['name']);
    TCMSLogChange::RunQuery(__LINE__, "ALTER TABLE $quotedTableName ENGINE = InnoDB");
}

TCMSLogChange::RunQuery(__LINE__, "UPDATE `cms_tbl_conf` SET `engine` = 'InnoDB'");

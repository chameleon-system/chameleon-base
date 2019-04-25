<h1>Build #1556200994</h1>
<h2>Date: 2019-04-25</h2>
<div class="changelog">
    - #403: add missing index for [fieldname]_table_name of TCMSFieldExtendedLookupMultiTable fields if used
</div>
<?php

$databaseConnection = TCMSLogChange::getDatabaseConnection();

// find all extended lookup multi table fields
$query = 'SELECT `id` FROM `cms_field_type` WHERE `constname` = '.$databaseConnection->quote(TCMSFieldExtendedLookupMultiTable::FIELD_SYSTEM_NAME);
$fieldTypeId = $databaseConnection->fetchColumn($query);

$fieldConfigQuery = '
                  SELECT 
                         `cms_field_conf`.`name` AS fieldName,
                         `cms_tbl_conf`.`name` AS tableName
                    FROM `cms_field_conf` 
               LEFT JOIN `cms_tbl_conf` ON `cms_tbl_conf`.`id` = `cms_field_conf`.`cms_tbl_conf_id` 
                   WHERE `cms_field_conf`.`cms_field_type_id` = :fieldTypeId';

$fieldConfigResult = $databaseConnection->fetchAll($fieldConfigQuery, ['fieldTypeId' => $fieldTypeId]);

foreach ($fieldConfigResult as $row) {
    $quotedTableName = $databaseConnection->quoteIdentifier($row['tableName']);

    $indexName = $row['fieldName'].TCMSFieldExtendedLookupMultiTable::TABLE_NAME_FIELD_SUFFIX;
    $indexExistsQuery = 'SHOW INDEX FROM '.$quotedTableName.' WHERE KEY_NAME = '.$databaseConnection->quote($indexName);
    $indexExistsResult = $databaseConnection->query($indexExistsQuery);

    if (0 !== $indexExistsResult->rowCount()) {
        continue;
    }

    // index is missing, so create it
    $quotedIndexName = $databaseConnection->quoteIdentifier($indexName);

    $addIndexQuery = 'ALTER TABLE '.$quotedTableName.' ADD INDEX '.$quotedIndexName.' ('.$quotedIndexName.')';
    $databaseConnection->query($addIndexQuery);
}
<h1>Build #1676964683</h1>
<h2>Date: 2023-02-21</h2>
<div class="changelog">
    - null foreign keys on foreign keys
</div>
<?php

$query = "SELECT cms_field_conf.name as field_name, cms_tbl_conf.name as table_name, cms_field_conf.translation as field_translation
 FROM cms_field_conf
 INNER JOIN cms_tbl_conf ON cms_field_conf.cms_tbl_conf_id = cms_tbl_conf.id
 INNER JOIN cms_field_type ON cms_field_conf.cms_field_type_id = cms_field_type.id
 WHERE cms_field_type.constname IN ('CMSFIELD_PROPERTY_PARENT_ID', 'CMSFIELD_CMSUSER', 'CMSFIELD_COUNTRY', 'CMSFIELD_DOCUMENTS', 'CMSFIELD_TREE', 'CMSFIELD_EXTENDEDTABLELIST', 'CMSFIELD_MODULEINSTANCE', 'CMSFIELD_EXTENDEDTABLELIST_MEDIA', 'CMSFIELD_SINGLE_DOCUMENT') AND cms_tbl_conf.name NOT IN ('cms_migration_file')
 ORDER BY cms_tbl_conf.name, cms_field_conf.name
 ";

$rows = TCMSLogChange::getDatabaseConnection()->fetchAllAssociative($query);

$table = null;
$alterList = [];
$updateList = [];
foreach ($rows as $row) {
    $alterQuery = sprintf(
        'ALTER TABLE `%1$s`
                        CHANGE `%2$s` `%2$s` char(36) COLLATE \'latin1_general_ci\' NULL DEFAULT NULL COMMENT \'%3$s\'',
        $row['table_name'],
        $row['field_name'],
        addslashes($row['field_translation'])
    );
    TCMSLogChange::getDatabaseConnection()->executeQuery($alterQuery);
    $query = "UPDATE `{$row['table_name']}` SET `{$row['field_name']}` = NULL WHERE `{$row['field_name']}` = ''";
    TCMSLogChange::RunQuery(__LINE__, $query);
}
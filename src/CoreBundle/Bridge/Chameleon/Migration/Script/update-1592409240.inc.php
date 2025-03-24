<h1>Build #1592409240</h1>
<h2>Date: 2020-06-17</h2>
<div class="changelog">
    - ref #595 add missing target table config to document tree parent field
</div>
<?php
$documentTreeParentFieldId = TCMSLogChange::GetTableFieldId(
    TCMSLogChange::GetTableId('cms_document_tree'),
    'parent_id'
);
$query = "SELECT `fieldtype_config` FROM `cms_field_conf` WHERE `id` = :id";
$fieldConfig = TCMSLogChange::getDatabaseConnection()->fetchOne($query, ['id' => $documentTreeParentFieldId]);
if (false === stripos($fieldConfig, 'connectedTableName')) {
    $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
        ->setFields(
            [
                'fieldtype_config' => 'connectedTableName=cms_document_tree',
            ]
        )
        ->setWhereEquals(
            [
                'id' => $documentTreeParentFieldId,
            ]
        );
    TCMSLogChange::update(__LINE__, $data);
}

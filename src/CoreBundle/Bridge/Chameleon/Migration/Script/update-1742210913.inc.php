<h1>Build #1742210913</h1>
<h2>Date: 2025-03-17</h2>
<div class="changelog">
    - removed translateable from name field of table cms_right
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        'is_translatable' => '0', // prev.: '1'
    ])
    ->setWhereEquals([
        'name' => 'name',
        'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_right'),
    ])
;
TCMSLogChange::update(__LINE__, $data);

$query ="ALTER TABLE cms_right
                     CHANGE name
                            name VARCHAR(40) NOT NULL COMMENT 'CMS-Kürzel des Rechtetyps: '";
TCMSLogChange::RunQuery(__LINE__, $query);

$query ="ALTER TABLE cms_right DROP IF EXISTS name__en ";
TCMSLogChange::RunQuery(__LINE__, $query);
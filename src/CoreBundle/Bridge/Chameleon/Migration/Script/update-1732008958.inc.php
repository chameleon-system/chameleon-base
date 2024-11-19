<h1>Build #1732008958</h1>
<h2>Date: 2024-11-19</h2>
<div class="changelog">
    - ref #65030: shift preview token field at end of field list, and make it readonly
</div>
<?php

$connection = TCMSLogChange::getDatabaseConnection();

$position = $connection->fetchOne('SELECT MAX(`position`) FROM `cms_field_conf`');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        //'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_user'),
        //'name' => 'preview_token',
        //'translation' => 'Vorschau Token',
        'modifier' => 'readonly',
        'fieldclass_subtype' => '',
        'class_type' => 'Core',
        'position' => $position + 1,
    ])
    ->setWhereEquals([
        'id' => 'f492ced8-aa48-b9bb-a294-1c422c361913',
    ])
;
TCMSLogChange::update(__LINE__, $data);

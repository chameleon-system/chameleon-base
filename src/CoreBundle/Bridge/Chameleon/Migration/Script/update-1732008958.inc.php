<h1>Build #1732008958</h1>
<h2>Date: 2024-11-19</h2>
<div class="changelog">
    - ref #65030: shift preview token field at end of field list, and make it readonly
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        //'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_user'),
        //'name' => 'preview_token',
        //'translation' => 'Vorschau Token',
        //'position' => 0, // is set below
        'modifier' => 'readonly',
        'fieldclass_subtype' => '',
        'class_type' => 'Core',
    ])
    ->setWhereEquals([
        'id' => 'f492ced8-aa48-b9bb-a294-1c422c361913',
    ])
;
TCMSLogChange::update(__LINE__, $data);

TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('cms_user'), 'preview_token', 'date_modified');

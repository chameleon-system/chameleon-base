<h1>Build #1553768043</h1>
<h2>Date: 2019-03-28</h2>
<div class="changelog">
    - Reset field class and fix translations for cms_media.width
</div>
<?php

$fieldId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_media'), 'width');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'translation' => 'Width',
        'fieldclass' => '',
    ])
    ->setWhereEquals([
        'id' => $fieldId,
    ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        'translation' => 'Breite',
    ])
    ->setWhereEquals([
        'id' => $fieldId,
    ])
;
TCMSLogChange::update(__LINE__, $data);

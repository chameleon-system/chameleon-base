<h1>Build #1532942270</h1>
<h2>Date: 2018-07-30</h2>
<div class="changelog">
    - hide old media properties field
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
//      'name' => 'width',
//      'translation' => 'Properties',
        'fieldclass' => '', // remove custom field type
        'modifier' => 'hidden',
    ])
    ->setWhereEquals([
        'name' => 'width',
        'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_media')
    ])
;
TCMSLogChange::update(__LINE__, $data);


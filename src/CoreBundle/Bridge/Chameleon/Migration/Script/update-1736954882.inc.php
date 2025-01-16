<h1>Build #1736954882</h1>
<h2>Date: 2025-01-15</h2>
<div class="changelog">
    - ref #65487: fix name/049_trans field position in cms_role table
</div>
<?php

// fix name field position
TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('cms_role'), '049_trans', 'id');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        // 'name' => '049_trans',
        'translation' => 'Name', // prev.: 'German translation'
    ])
    ->setWhereEquals([
        'id' => '360',
    ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        // 'name' => '049_trans',
        'translation' => 'Name', // prev.: 'German translation'
    ])
    ->setWhereEquals([
        'id' => '360',
    ])
;
TCMSLogChange::update(__LINE__, $data);

$query ="ALTER TABLE `cms_role`
                     CHANGE `049_trans`
                            `049_trans` VARCHAR(40) NOT NULL COMMENT 'Name: '";
TCMSLogChange::RunQuery(__LINE__, $query);
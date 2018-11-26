<h1>Build #1542967206</h1>
<h2>Date: 2018-11-23</h2>
<div class="changelog">
    - #167: Make sure every field type index type has a value
</div>
<?php

TCMSLogChange::RunQuery(__LINE__, "UPDATE `cms_field_type` SET `indextype` = 'none' WHERE `indextype` = ''");

$query ="ALTER TABLE `cms_field_type`
                     CHANGE `indextype`
                            `indextype` ENUM('none','index','unique') DEFAULT 'none' NOT NULL COMMENT 'Field index: Defines what type of index is to be created for this field type.'";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'isrequired' => '1',
  ])
  ->setWhereEquals([
      'id' => TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_field_type'), 'indextype'),
  ])
;
TCMSLogChange::update(__LINE__, $data);


<h1>update - Build #1507559334</h1>
<h2>Date: 2017-10-09</h2>
<div class="changelog">
    - set default MySQL engine to InnoDB.
</div>
<?php

  $query = "ALTER TABLE `cms_tbl_conf`
                     CHANGE `engine`
                            `engine` ENUM('MyISAM', 'InnoDB', 'MEMORY', 'ARCHIVE') DEFAULT 'InnoDB' NOT NULL";
  TCMSLogChange::RunQuery(__LINE__, $query);

  $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
      ->setFields(array(
          'field_default_value' => 'InnoDB',
          'length_set' => '\'MyISAM\', \'InnoDB\', \'MEMORY\', \'ARCHIVE\'',
      ))
      ->setWhereEquals(array(
          'id' => TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_tbl_conf'), 'engine'),
      ))
  ;
  TCMSLogChange::update(__LINE__, $data);

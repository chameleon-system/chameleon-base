<h1>Build #1732093885</h1>
<h2>Date: 2024-11-20</h2>
<div class="changelog">
    - ref #64995: deprecate callback activation field for list view items
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      //'name' => 'use_callback',
      //'translation' => 'Callbackfunktion aktivieren',
      'modifier' => 'hidden',
      '049_helptext' => '@deprecated #64995',
  ])
  ->setWhereEquals([
      'id' => '998',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$query ="ALTER TABLE `cms_tbl_display_list_fields`
                     CHANGE `use_callback`
                            `use_callback` ENUM('0','1') DEFAULT '0' NOT NULL COMMENT 'Callbackfunktion aktivieren: @deprecated #64995'";
TCMSLogChange::RunQuery(__LINE__, $query);

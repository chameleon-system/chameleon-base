<h1>Build #1734424888</h1>
<h2>Date: 2024-12-17</h2>
<div class="changelog">
    - #65296: page list: set pagename text-align left
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'de')
  ->setFields([
//      'title' => 'Seite',
      'align' => 'left',
  ])
  ->setWhereEquals([
      'name' => '`cms_tpl_page`.`name`',
      'db_alias' => 'pagename',
  ])
;
TCMSLogChange::update(__LINE__, $data);


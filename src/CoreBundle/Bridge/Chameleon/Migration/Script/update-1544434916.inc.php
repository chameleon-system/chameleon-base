<h1>Build #1544434916</h1>
<h2>Date: 2018-12-10</h2>
<div class="changelog">
    - #95: Show portal in routing list
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_display_list_fields', 'en')
  ->setFields([
      'title' => 'Portal',
      'name' => '`cms_portal`.`name`',
      'db_alias' => 'cms_portal__name',
      'position' => '270',
      'width' => '-1',
      'align' => 'left',
      'callback_fnc' => '',
      'use_callback' => '0',
      'show_in_list' => '1',
      'show_in_sort' => '0',
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('pkg_cms_routing'),
      'cms_translation_field_name' => '',
      'id' => 'fc6f7c91-c197-fdda-869e-deb35eda1b98',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

// TODO how to write this?
//TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('cms_tbl_display_list_fields'))


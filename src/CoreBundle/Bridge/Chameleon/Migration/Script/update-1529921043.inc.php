<h1>Build #1529921043</h1>
<h2>Date: 2018-06-25</h2>
<div class="changelog">
    - Fix web page list view for other languages than the base language.
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'en')
  ->setFields([
      'list_query' => 'SELECT DISTINCT `cms_tpl_page`.*, `cms_tpl_page`.`name` AS pagename
FROM `cms_tpl_page`
LEFT JOIN `cms_tpl_page_cms_usergroup_mlt` ON `cms_tpl_page`.`id` = `cms_tpl_page_cms_usergroup_mlt`.`source_id`
',
  ])
  ->setWhereEquals([
      'name' => 'cms_tpl_page',
  ])
;
TCMSLogChange::update(__LINE__, $data);


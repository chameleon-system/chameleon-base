<h1>Build #1548329811</h1>
<h2>Date: 2019-01-24</h2>
<div class="changelog">
    - Add new list view to change log item table definition.<br/>
    - Establish joined in fields in list query for change log item table used in secondary list view.<br/>
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_list_class', 'en')
    ->setFields([
        'name' => 'TCMSListManagerFieldHistory.class',
        'classname' => 'TCMSListManagerFieldHistory.class',
        'cms_tbl_conf_id' => TCMSLogChange::GetTableId('pkg_cms_changelog_set'),
        'class_subtype' => '',
        'classlocation' => 'Core',
        'id' => 'b1afbe25-56e8-e7a1-8c18-6129b2abd564',
    ]);
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
    ->setFields([
        'list_query' => 'SELECT
`pkg_cms_changelog_item`.`id`,
`pkg_cms_changelog_item`.`cmsident`,
`pkg_cms_changelog_item`.`cms_field_conf`,
`pkg_cms_changelog_item`.`value_old`,
`pkg_cms_changelog_item`.`value_new`,
`pkg_cms_changelog_set`.`change_type` AS `pkg_cms_changelog_set_change_type`,
`pkg_cms_changelog_set`.`modify_date` AS `pkg_cms_changelog_set_modify_date`
FROM `pkg_cms_changelog_item`
LEFT JOIN `pkg_cms_changelog_set`
ON `pkg_cms_changelog_set`.`id` = `pkg_cms_changelog_item`.`pkg_cms_changelog_set_id`',
    ])
    ->setWhereEquals([
        'name' => 'pkg_cms_changelog_item',
    ]);
TCMSLogChange::update(__LINE__, $data);
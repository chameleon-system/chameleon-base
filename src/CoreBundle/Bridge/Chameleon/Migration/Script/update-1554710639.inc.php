<h1>Build #1554106544</h1>
<h2>Date: 2019-04-01</h2>
<div class="changelog">
    - Remove CMS search related modules
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_module', 'en')
    ->setWhereEquals([
            'uniquecmsname' => 'cmssearchindexer',
    ]);
TCMSLogChange::delete(__LINE__, $data);

$query = "SELECT `id` FROM `cms_tpl_module` WHERE `classname` = 'MTSearch'";
$moduleId = TCMSLogChange::getDatabaseConnection()->fetchColumn($query);
if (false === $moduleId) {
    return;
}

$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module_cms_usergroup_mlt', 'de')
    ->setWhereEquals([
        'source_id' => $moduleId,
    ])
;
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module_cms_portal_mlt', 'de')
    ->setWhereEquals([
        'source_id' => $moduleId,
    ])
;
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module_cms_tbl_conf_mlt', 'de')
    ->setWhereEquals([
        'source_id' => $moduleId,
    ])
;
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'de')
    ->setWhereEquals([
        'id' => $moduleId,
    ])
;
TCMSLogChange::delete(__LINE__, $data);

<h1>Build #1748241190</h1>
<h2>Date: 2025-05-26</h2>
<div class="changelog">
    - #66581: add Breadcrumb template module
</div>
<?php

$id = TCMSLogChange::GetTableId('cms_tpl_module');
$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'de')
    ->setFields([
        'name' => 'Breadcrumb',
        'classname' => 'chameleon_system_breadcrumb.module.breadcrumb',
        'description' => 'Breadcrumb',
        'view_mapper_config' => 'standard=breadcrumb/standard.html.twig',
        'view_mapping' => 'standard=Standard',
        'position' => '0',
        'id' => $id,
    ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'en')
    ->setFields([
        'name' => 'Breadcrumb',
        'description' => 'Breadcrumb',
        'view_mapping' => 'standard=Standard',
    ])
    ->setWhereEquals([
        'id' => $id,
    ])
;
TCMSLogChange::update(__LINE__, $data);

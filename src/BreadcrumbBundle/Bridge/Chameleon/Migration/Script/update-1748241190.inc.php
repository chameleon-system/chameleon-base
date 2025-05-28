<h1>Build #1748241190</h1>
<h2>Date: 2025-05-26</h2>
<div class="changelog">
    - #66581: add Breadcrumb template module
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'de')
    ->setFields([
        // 'name' => 'Breadcrumb',
        'view_mapper_config' => 'standard=breadcrumb/standard.html.twig', // prev.: 'standard=standard.html.twig'
    ])
    ->setWhereEquals([
        'classname' => 'chameleon_system_breadcrumb.module.breadcrumb',
    ])
;
TCMSLogChange::update(__LINE__, $data);

<h1>Build #1738247573</h1>
<h2>Date: 2025-01-30</h2>
<div class="changelog">
    - #65693: add cms module for log viewer
</div>
<?php

$backendModuleId = TCMSLogChange::createUnusedRecordId('cms_tpl_module');
$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'de')
    ->setFields(
        [
            'name' => 'Log Übersicht',
            'description' => '',
            'icon_list' => 'image.gif',
            'classname' => 'chameleon_system_core.bridge_chameleon_backend_module.log_viewer_backend_module',
            'view_mapper_config' => 'logViewer=LogViewer/logViewer.html.twig',
            'mapper_chain' => '',
            'view_mapping' => '',
            'revision_management_active' => '0',
            'is_copy_allowed' => '0',
            'show_in_template_engine' => '0',
            'is_restricted' => '0',
            'id' => $backendModuleId,
        ]
    );
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'en')
    ->setFields(
        [
            'name' => 'Log Viewer',
        ]
    )
    ->setWhereEquals(
        [
            'id' => $backendModuleId,
        ]
    );
TCMSLogChange::update(__LINE__, $data);

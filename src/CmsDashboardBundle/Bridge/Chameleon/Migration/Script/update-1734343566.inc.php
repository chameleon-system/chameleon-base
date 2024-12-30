
<h1>update - Build #000</h1>
<h2>Date: 2024-12-45</h2>
<div class="changelog">
    register dahboard module
</div>
<?php
$backendModuleId = TCMSLogChange::createUnusedRecordId('cms_tpl_module');
$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'de')
    ->setFields(
        array(
            'name' => 'CMS Dashboard',
            'description' => '',
            'icon_list' => 'image.gif',
            'classname' => 'chameleon_system_cms_dashboard.backend_module.dashboard',
            'view_mapper_config' => 'dashboard=CmsDashboard/dashboard.html.twig',
            'mapper_chain' => '',
            'view_mapping' => '',
            'revision_management_active' => '0',
            'is_copy_allowed' => '0',
            'show_in_template_engine' => '0',
            'is_restricted' => '0',
            'id' => $backendModuleId,
        )
    );
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'en')
    ->setFields(
        array(
            'name' => 'CMS Dashboard',
        )
    )
    ->setWhereEquals(
        array(
            'id' => $backendModuleId,
        )
    );
TCMSLogChange::update(__LINE__, $data);
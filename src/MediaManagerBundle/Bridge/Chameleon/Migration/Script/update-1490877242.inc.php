<h1>update - Build #1490877242</h1>
<h2>Date: 2017-03-30</h2>
<div class="changelog">
    #38232<br/>
    add media manager module<br>
</div>
<?php
$backendModuleId = TCMSLogChange::createUnusedRecordId('cms_tpl_module');
$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'de')
    ->setFields(
        [
            'name' => 'Medienverwaltung Backendmodul',
            'description' => '',
            'icon_list' => 'image.gif',
            'classname' => 'chameleon_system_media_manager.backend_module.media_manager',
            'view_mapper_config' => 'full=mediaManager/module/full.html.twig',
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
            'name' => 'Media manager backend module',
        ]
    )
    ->setWhereEquals(
        [
            'id' => $backendModuleId,
        ]
    );
TCMSLogChange::update(__LINE__, $data);

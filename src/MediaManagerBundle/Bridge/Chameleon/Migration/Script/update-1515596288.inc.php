<h1>update - Build #1515596288</h1>
<h2>Date: 2018-01-10</h2>
<div class="changelog">
    add module to dosplay legacy media list<br/>
</div>
<?php
$moduleId = TCMSLogChange::createUnusedRecordId('cms_tpl_module');
$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'en')
    ->setFields(
        [
            'description' => 'This module shows the media list via the Chameleon ListManager',
            'icon_list' => 'application.png',
            'classname' => 'chameleon_system_media_manager.backend_module.media_manager_legacy_list',
            'view_mapper_config' => 'standard=mediaManager/module/legacyListModule.html.twig',
            'mapper_chain' => '',
            'view_mapping' => '',
            'revision_management_active' => '0',
            'is_copy_allowed' => '0',
            'show_in_template_engine' => '0',
            'is_restricted' => '0',
            'name' => 'Media manager: list via ListManager',
            'position' => '',
            'id' => $moduleId,
        ]
    );
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'de')
    ->setFields(
        [
            'description' => 'Dieses Modul zeigt Medien als Liste über den Chameleon ListManager',
            'name' => 'Medienverwaltung: Liste per ListManager',
        ]
    )
    ->setWhereEquals(
        [
            'id' => $moduleId,
        ]
    );
TCMSLogChange::update(__LINE__, $data);

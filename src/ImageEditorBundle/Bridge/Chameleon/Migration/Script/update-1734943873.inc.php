<h1>update - Build #1734943873</h1>
<h2>Date: 2024-12-23</h2>
<div class="changelog">
    add image editor backend module<br/>
</div>
<?php
$backendModuleId = TCMSLogChange::createUnusedRecordId('cms_tpl_module');
$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'en')
    ->setFields(
        [
            'description' => '',
            'icon_list' => 'cut.gif',
            'classname' => 'chameleon_system_image_editor.bridge_chameleon_backend_module.image_editor_module',
            'view_mapper_config' => 'standard=imageEditor/mediaManager/imageEditor.html.twig',
            'mapper_chain' => '',
            'view_mapping' => '',
            'revision_management_active' => '0',
            'is_copy_allowed' => '0',
            'show_in_template_engine' => '0',
            'is_restricted' => '0',
            'name' => 'Image editor backend module',
            'id' => $backendModuleId,
        ]
    );
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'de')
    ->setFields(
        [
            'name' => 'Bildbearbeitung Backendmodul',
        ]
    )
    ->setWhereEquals(
        [
            'id' => $backendModuleId,
        ]
    );
TCMSLogChange::update(__LINE__, $data);

<h1>update - Build #1502367589</h1>
<h2>Date: 2017-08-10</h2>
<div class="changelog">
    add image crop backend module<br/>
</div>
<?php
$backendModuleId = TCMSLogChange::createUnusedRecordId('cms_tpl_module');
$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'en')
    ->setFields(
        array(
            'description' => '',
            'icon_list' => 'cut.gif',
            'classname' => 'chameleon_system_image_crop.backend_module.image_crop',
            'view_mapper_config' => 'standard=imageCrop/imageCropEditor/module.html.twig',
            'mapper_chain' => '',
            'view_mapping' => '',
            'revision_management_active' => '0',
            'is_copy_allowed' => '0',
            'show_in_template_engine' => '0',
            'is_restricted' => '0',
            'name' => 'Image crop editor backend module',
            'id' => $backendModuleId,
        )
    );
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'de')
    ->setFields(
        array(
            'name' => 'Bildausschnittverwaltung Backendmodul',
        )
    )
    ->setWhereEquals(
        array(
            'id' => $backendModuleId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

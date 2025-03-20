<h1>pkgnewsletter - Build #1527064958</h1>
<div class="changelog">
    - turn mappers into services
</div>
<?php

$mappers = array(
    'ChameleonSystem\CoreBundle\TemplateEngine\Mapper\ModuleChooserModuleListMapper' => 'chameleon_system_core.mapper.template_engine.module_chooser_module_list',
    'MTFeedbackMapper_AdditionalFields' => 'chameleon_system_core.mapper.feedback_additional_fields',
    'MTFeedbackMapper_StandardForm' => 'chameleon_system_core.mapper.feedback_standard_form',
    'MTFeedbackMapper_Success' => 'chameleon_system_core.mapper.feedback_success',
    'MTTextFieldMapper_Text' => 'chameleon_system_core.mapper.text_field_text',
    'TCMSMediaFieldImageBoxMapper' => 'chameleon_system_core.mapper.media_field_image_box',
    'TCMSMediaFieldMapper' => 'chameleon_system_core.mapper.media_field',
    'TCMSMediaFieldUploadMapper' => 'chameleon_system_core.mapper.media_field_upload',
    'TCMSMediaMultiFieldMapper' => 'chameleon_system_core.mapper.media_multi_field',
    'TCMSMessageManagerMapper' => 'chameleon_system_core.mapper.message_manager',
    'TCMSMessageManagerMapper_Overlay' => 'chameleon_system_core.mapper.message_manager_overlay',
    'TMapper_ViewPortMeta' => 'chameleon_system_core.mapper.view_port_meta',
    'TMapper_ViewPortSwitch' => 'chameleon_system_core.mapper.view_port_switch',
);

$databaseConnection = TCMSLogChange::getDatabaseConnection();
$result = $databaseConnection->executeQuery("SELECT `classname` FROM `cms_tpl_module` WHERE `view_mapper_config` != '' OR `mapper_chain` != ''");

while (false !== $row = $result->fetchNumeric()) {
    $moduleManager = TCMSLogChange::getModuleManager($row[0]);

    $mapperConfig = $moduleManager->getMapperConfig();
    $hasChanges = false;
    foreach ($mappers as $oldMapper => $newMapper) {
        $hasChanges = $mapperConfig->replaceMapper($oldMapper, $newMapper) || $hasChanges;
        $hasChanges = $mapperConfig->replaceMapper('\\'.$oldMapper, $newMapper) || $hasChanges;
    }
    if (true === $hasChanges) {
        $moduleManager->updateMapperConfig($mapperConfig);
    }

    foreach ($mappers as $oldMapper => $newMapper) {
        $moduleManager->replaceMapperInMapperChain($oldMapper, $newMapper);
        $moduleManager->replaceMapperInMapperChain('\\'.$oldMapper, $newMapper);
    }
}

<h1>pkgnewsletter - Build #1527064992</h1>
<div class="changelog">
    - turn mappers into services
</div>
<?php

$mappers = array(
    'TPkgViewRendererConfigToLessMapper' => 'chameleon_system_view_renderer.mapper.config_to_less',
    'TPkgViewRendererMapper_ListHandler' => 'chameleon_system_view_renderer.mapper.list_handler',
    'TPkgViewRendererSnippetGallery_To_List_Mapper' => 'chameleon_system_view_renderer.mapper.snippet_gallery_to_list',
    'TPkgViewRendererSnippetGalleryNaviTree_To_Navigation_Mapper' => 'chameleon_system_view_renderer.mapper.snippet_gallery_navi_tree_to_navigation',
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

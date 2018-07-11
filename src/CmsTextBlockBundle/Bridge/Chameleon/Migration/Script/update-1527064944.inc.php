<h1>pkgnewsletter - Build #1527064944</h1>
<div class="changelog">
    - turn mappers into services
</div>
<?php

$mappers = array(
    'TPkgCmsTextBlockMapper_GetText' => 'chameleon_system_cms_text_block.mapper.get_text',
);

$databaseConnection = TCMSLogChange::getDatabaseConnection();
$statement = $databaseConnection->executeQuery("SELECT `classname` FROM `cms_tpl_module` WHERE `view_mapper_config` != '' OR `mapper_chain` != ''");
if (false === $statement->execute()) {
    return;
}

while (false !== $row = $statement->fetch(PDO::FETCH_NUM)) {
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

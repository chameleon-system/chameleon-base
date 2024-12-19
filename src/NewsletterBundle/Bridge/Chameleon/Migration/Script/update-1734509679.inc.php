<h1>Build #1734509679</h1>
<h2>Date: 2024-12-19</h2>
<div class="changelog">
    - ref #65248: remove deprecated newsletter module
</div>
<?php
$mappers = [
    'TPkgNewsletterMapper_QuickSignup' => '',
    'chameleon_system_newsletter.mapper.quick_signup' => '',
];

$databaseConnection = TCMSLogChange::getDatabaseConnection();
$statement = $databaseConnection->executeQuery("SELECT `classname` FROM `cms_tpl_module` WHERE `view_mapper_config` != '' OR `mapper_chain` != ''");
if (false === $statement->execute()) {
    return;
}

while (false !== ($row = $statement->fetchNumeric())) {
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

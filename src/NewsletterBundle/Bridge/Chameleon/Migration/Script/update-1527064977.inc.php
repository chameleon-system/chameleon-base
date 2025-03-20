<h1>pkgnewsletter - Build #1527064977</h1>
<div class="changelog">
    - turn mappers into services
</div>
<?php

$mappers = array(
    'TPkgNewsletterMapper_PkgNewsletterModuleSingOutConfig' => 'chameleon_system_newsletter.mapper.signout_config',
    'TPkgNewsletterMapper_PkgNewsletterModuleSingOutConfig_Form' => 'chameleon_system_newsletter.mapper.signout_config_form',
    'TPkgNewsletterMapper_PkgNewsletterModuleSingupConfig' => 'chameleon_system_newsletter.mapper.signup_config',
    'TPkgNewsletterMapper_PkgNewsletterModuleSingupConfig_Form' => 'chameleon_system_newsletter.mapper.signup_config_form',
    'TPkgNewsletterMapper_QuickSignup' => 'chameleon_system_newsletter.mapper.quick_signup',
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

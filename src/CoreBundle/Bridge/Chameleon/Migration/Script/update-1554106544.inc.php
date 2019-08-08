<h1>Build #1554106544</h1>
<h2>Date: 2019-04-01</h2>
<div class="changelog">
    - migrate frontend module icons
</div>
<?php

/**
 * @var ChameleonSystem\CoreBundle\Bridge\Chameleon\Migration\Service\ModuleIconMigrator $moduleIconMigrator
 */
$moduleIconMigrator = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.service.module_icon_migrator');
$moduleIconMigrator->migrateUnhandledModules();

TCMSLogChange::addInfoMessage('Please note that custom frontend modules may need a migration for the icon. See the updated 6.3 upgrade guide for details.');

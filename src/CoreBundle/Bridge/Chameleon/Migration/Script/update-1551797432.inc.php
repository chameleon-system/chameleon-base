<h1>Build #1551797432</h1>
<h2>Date: 2019-03-06</h2>
<div class="changelog">
    - ref #335 migrate unhandled main menu items to sidebar
</div>
<?php

/**
 * @var ChameleonSystem\CoreBundle\Bridge\Chameleon\Migration\Service\Migrator63 $migration63Service
 */
$migration63Service = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.service.migrator63');
$migration63Service->migrateUnhandledTableMenuItems();

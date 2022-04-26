<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\UpdateCounterMigrationBundle\EventListener;

use ChameleonSystem\UpdateCounterMigrationBundle\Exception\CounterMigrationException;
use ChameleonSystem\UpdateCounterMigrationBundle\Migrator\UpdateCounterMigrator;
use ChameleonSystem\UpdateCounterMigrationBundle\Migrator\UpdateCounterVersionMigrator;

class MigrateCountersListener
{
    /**
     * @var UpdateCounterVersionMigrator
     */
    private $versionMigrator;
    /**
     * @var UpdateCounterMigrator
     */
    private $migrator;

    /**
     * @param UpdateCounterVersionMigrator $versionMigrator
     * @param UpdateCounterMigrator        $migrator
     */
    public function __construct(UpdateCounterVersionMigrator $versionMigrator, UpdateCounterMigrator $migrator)
    {
        $this->versionMigrator = $versionMigrator;
        $this->migrator = $migrator;
    }

    /**
     * @throws CounterMigrationException
     *
     * @return void
     */
    public function onBeforeUpdateCollection()
    {
        $this->versionMigrator->migrate();
        $this->migrator->migrate();
    }
}

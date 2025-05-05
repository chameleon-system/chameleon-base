<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DatabaseMigration\Counter;

/**
 * MigrationCounterManagerInterface defines a service that provides methods for migration counter manipulation and information
 * retrieval.
 */
interface MigrationCounterManagerInterface
{
    /**
     * Returns true if a migration counter for the passed $bundleName exists.
     *
     * @param string $bundleName
     *
     * @return bool
     */
    public function doesCounterExist($bundleName);

    /**
     * Creates a migration counter for the passed $bundleName. If the counter already exists, no further action is taken.
     *
     * @param string $bundleName
     *
     * @return void
     */
    public function createMigrationCounter($bundleName);

    /**
     * Deletes a migration counter for the passed $bundleName. If the counter does not exist, no further action is taken.
     *
     * @param string $bundleName
     *
     * @return void
     */
    public function deleteMigrationCounter($bundleName);

    /**
     * Marks a migration file with the passed $bundleName and $buildNumber as processed in the data storage.
     *
     * @param string $bundleName
     * @param int $buildNumber
     *
     * @return void
     */
    public function markMigrationFileAsProcessed($bundleName, $buildNumber);
}

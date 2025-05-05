<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DatabaseMigration\DataAccess;

/**
 * MigrationDataAccessInterface provides methods for data access for migration script related data.
 */
interface MigrationDataAccessInterface
{
    /**
     * Returns a mapping of bundle names to corresponding counter ID in the form [ "bundle name" => "counter ID" ].
     *
     * @return array
     */
    public function getMigrationCounterIdsByBundle();

    /**
     * Returns a list of migration files that were already processed. Each array item is an array that contains
     * "bundle_name" and "build_number" .
     *
     * @return array
     */
    public function getProcessedMigrationData();

    /**
     * Marks a migration file as processed, so that it is not executed twice.
     *
     * @param string $counterId
     * @param int $buildNumber
     *
     * @return void
     *
     * @throws \InvalidArgumentException if the bundle name could not be found
     */
    public function markMigrationFileAsProcessed($counterId, $buildNumber);

    /**
     * Creates an update counter in the underlying data storage, generating a random UUID as ID.
     * Call getMigrationCounterIdsByBundle() to retrieve the new ID.
     *
     * @param string $bundleName
     *
     * @return void
     */
    public function createMigrationCounter($bundleName);

    /**
     * Deletes the update counter with the given ID from the underlying data storage.
     *
     * @param string $counterId
     *
     * @return void
     */
    public function deleteMigrationCounter($counterId);
}

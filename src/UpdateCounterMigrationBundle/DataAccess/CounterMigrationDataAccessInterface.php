<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\UpdateCounterMigrationBundle\DataAccess;

use ChameleonSystem\UpdateCounterMigrationBundle\Exception\CounterMigrationException;

interface CounterMigrationDataAccessInterface
{
    /**
     * @param string $from
     * @param string $to
     *
     * @return void
     *
     * @throws CounterMigrationException
     */
    public function copyCounter($from, $to);

    /**
     * @param array  $updates
     * @param string $counter
     *
     * @return void
     *
     * @throws CounterMigrationException
     */
    public function addUpdatesToCounter(array $updates, $counter);

    /**
     * @param string $counterName
     *
     * @return bool
     *
     * @throws CounterMigrationException
     */
    public function counterExists($counterName);

    /**
     * @return int
     */
    public function getMigrationCounterVersion();

    /**
     * @param int $version
     *
     * @return void
     */
    public function saveMigrationCounterVersion($version);

    /**
     * @return array
     */
    public function getAllCountersVersionOne();

    /**
     * @param array $counterData
     *
     * @return void
     */
    public function createCountersVersionTwo(array $counterData);

    /**
     * @param string   $systemNamePattern
     * @param string[] $excludePatterns
     *
     * @return void
     */
    public function deleteCountersVersionOne($systemNamePattern, array $excludePatterns = array());

    /**
     * Creates base tables for migration counter data.
     *
     * @return void
     */
    public function createMigrationTablesVersionTwo();
}

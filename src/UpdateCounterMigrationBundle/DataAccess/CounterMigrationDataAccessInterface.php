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
     * @throws CounterMigrationException
     */
    public function copyCounter($from, $to);

    /**
     * @param array  $updates
     * @param string $counter
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
     */
    public function saveMigrationCounterVersion($version);

    /**
     * @return array
     */
    public function getAllCountersVersionOne();

    /**
     * @param array $counterData
     */
    public function createCountersVersionTwo(array $counterData);

    /**
     * @param string   $systemNamePattern
     * @param string[] $excludePatterns
     */
    public function deleteCountersVersionOne($systemNamePattern, array $excludePatterns = array());

    /**
     * Creates base tables for migration counter data.
     */
    public function createMigrationTablesVersionTwo();
}

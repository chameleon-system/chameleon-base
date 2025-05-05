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

use ChameleonSystem\DatabaseMigration\DataAccess\MigrationDataAccessInterface;

/**
 * {@inheritdoc}
 */
class MigrationCounterManager implements MigrationCounterManagerInterface
{
    /**
     * @var MigrationDataAccessInterface
     */
    private $migrationDataAccess;

    public function __construct(MigrationDataAccessInterface $migrationDataAccess)
    {
        $this->migrationDataAccess = $migrationDataAccess;
    }

    /**
     * {@inheritdoc}
     */
    public function doesCounterExist($bundleName)
    {
        return array_key_exists($bundleName, $this->migrationDataAccess->getMigrationCounterIdsByBundle());
    }

    /**
     * {@inheritdoc}
     */
    public function createMigrationCounter($bundleName)
    {
        if (true === $this->doesCounterExist($bundleName)) {
            return;
        }

        $this->migrationDataAccess->createMigrationCounter($bundleName);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMigrationCounter($bundleName)
    {
        if (false === $this->doesCounterExist($bundleName)) {
            return;
        }

        $this->migrationDataAccess->deleteMigrationCounter($this->getCounterIdForBundle($bundleName));
    }

    /**
     * @param string $bundleName
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    private function getCounterIdForBundle($bundleName)
    {
        $countersByBundle = $this->migrationDataAccess->getMigrationCounterIdsByBundle();
        if (false === array_key_exists($bundleName, $countersByBundle)) {
            throw new \InvalidArgumentException('Invalid bundle name: '.$bundleName);
        }

        return $countersByBundle[$bundleName];
    }

    /**
     * {@inheritdoc}
     */
    public function markMigrationFileAsProcessed($bundleName, $buildNumber)
    {
        $this->createMigrationCounter($bundleName);
        $this->migrationDataAccess->markMigrationFileAsProcessed($this->getCounterIdForBundle($bundleName), $buildNumber);
    }
}

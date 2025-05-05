<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\DataAccess;

use ChameleonSystem\DatabaseMigration\DataAccess\MigrationDataAccessInterface;

class MigrationDataAccessRequestLevelCacheDecorator implements MigrationDataAccessInterface
{
    /**
     * @var MigrationDataAccessInterface
     */
    private $subject;
    /**
     * @var array
     */
    private $cache = [];

    public function __construct(MigrationDataAccessInterface $subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return void
     */
    private function clearCache()
    {
        $this->cache = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getMigrationCounterIdsByBundle()
    {
        $key = __METHOD__;
        if (false === array_key_exists($key, $this->cache)) {
            $this->cache[$key] = $this->subject->getMigrationCounterIdsByBundle();
        }

        return $this->cache[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessedMigrationData()
    {
        $key = __METHOD__;
        if (false === array_key_exists($key, $this->cache)) {
            $this->cache[$key] = $this->subject->getProcessedMigrationData();
        }

        return $this->cache[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function markMigrationFileAsProcessed($counterId, $buildNumber)
    {
        $this->subject->markMigrationFileAsProcessed($counterId, $buildNumber);
        $this->clearCache();
    }

    /**
     * {@inheritdoc}
     */
    public function createMigrationCounter($bundleName)
    {
        $this->subject->createMigrationCounter($bundleName);
        $this->clearCache();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMigrationCounter($counterId)
    {
        $this->subject->deleteMigrationCounter($counterId);
        $this->clearCache();
    }
}

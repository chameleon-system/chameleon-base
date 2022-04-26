<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\UpdateCounterMigrationBundle\Bridge\Chameleon\DataAccess;

use ChameleonSystem\UpdateCounterMigrationBundle\DataAccess\CounterMigrationDataAccessInterface;
use TCMSConfig;
use TdbCmsConfig;

/**
 * CounterMigrationDataAccessCacheDecorator will clear the config cache after a counter has been copied
 * to avoid subsequent false cache misses when checking for existing counters.
 */
class CounterMigrationDataAccessCacheDecorator implements CounterMigrationDataAccessInterface
{
    /**
     * @var CounterMigrationDataAccessInterface
     */
    private $subject;
    /**
     * @var TdbCmsConfig
     */
    private $config;

    /**
     * @param CounterMigrationDataAccessInterface $subject
     */
    public function __construct(CounterMigrationDataAccessInterface $subject)
    {
        $this->subject = $subject;
        $this->config = TCMSConfig::GetInstance();
    }

    /**
     * {@inheritdoc}
     */
    public function copyCounter($from, $to)
    {
        $this->subject->copyCounter($from, $to);
        $this->refreshConfigCacheForCreatedCounter($to);
    }

    /**
     * {@inheritdoc}
     */
    public function addUpdatesToCounter(array $updates, $counter)
    {
        $this->subject->addUpdatesToCounter($updates, $counter);
    }

    /**
     * {@inheritdoc}
     */
    public function counterExists($counter)
    {
        return $this->subject->counterExists($counter);
    }

    /**
     * @param string $counter
     *
     * @return void
     */
    private function refreshConfigCacheForCreatedCounter($counter)
    {
        $this->config->GetConfigParameter($counter, true, true);
    }

    /**
     * {@inheritdoc}
     */
    public function getMigrationCounterVersion()
    {
        return $this->subject->getMigrationCounterVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function saveMigrationCounterVersion($version)
    {
        $this->subject->saveMigrationCounterVersion($version);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllCountersVersionOne()
    {
        return $this->subject->getAllCountersVersionOne();
    }

    /**
     * {@inheritdoc}
     */
    public function createCountersVersionTwo(array $counterData)
    {
        $this->subject->createCountersVersionTwo($counterData);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCountersVersionOne($systemNamePattern, array $excludePatterns = array())
    {
        $this->subject->deleteCountersVersionOne($systemNamePattern, $excludePatterns);
    }

    /**
     * {@inheritdoc}
     */
    public function createMigrationTablesVersionTwo()
    {
        $this->subject->createMigrationTablesVersionTwo();
    }
}

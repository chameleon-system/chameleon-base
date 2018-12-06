<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\CronJob;

use ChameleonSystem\CoreBundle\Exception\CronjobHandlingException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use esono\pkgCmsCache\CacheInterface;
use TdbCmsConfig;

class CronjobEnablingService implements CronjobEnablingServiceInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(Connection $connection, CacheInterface $cache)
    {
        $this->connection = $connection;
        $this->cache = $cache;
    }

    public function isCronjobExecutionEnabled(): bool
    {
        $config = TdbCmsConfig::GetInstance();

        return true === $config->fieldCronjobsEnabled;
    }

    /**
     * @throws CronjobHandlingException
     */
    public function enableCronjobExecution(): void
    {
        try {
            $this->connection->executeUpdate("UPDATE `cms_config` SET `cronjobs_enabled` = '1'");
            $this->cache->callTrigger('cms_config');
        } catch (DBALException $exception) {
            throw new CronjobHandlingException('Cannot save cron jobs enable flag in database.', 0, $exception);
        }
    }

    /**
     * @throws CronjobHandlingException
     */
    public function disableCronjobExecution(): void
    {
        try {
            $this->connection->executeUpdate("UPDATE `cms_config` SET `cronjobs_enabled` = '0'");
            $this->cache->callTrigger('cms_config');
        } catch (DBALException $exception) {
            throw new CronjobHandlingException('Cannot reset cron jobs enable flag in database.', 0, $exception);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isOneCronjobRunning(): bool
    {
        try {
            return $this->connection->fetchColumn("SELECT COUNT(*) FROM `cms_cronjobs` WHERE `lock` = '1'") > 0;
        } catch (DBALException $exception) {
            throw new CronjobHandlingException('Cannot check for cron jobs in database.', 0, $exception);
        }
    }
}

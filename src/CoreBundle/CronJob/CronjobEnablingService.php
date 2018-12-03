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

use ChameleonSystem\CoreBundle\Exception\CronjobEnableException;
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
        $config = TdbCmsConfig::GetInstance(true);

        return true === $config->fieldCronjobsEnabled;
    }

    /**
     * @throws CronjobEnableException
     */
    public function enableAllCronjobExecution(): void
    {
        try {
            $this->connection->executeUpdate("UPDATE `cms_config` SET `cronjobs_enabled` = '1'");
            $this->cache->callTrigger('cms_config');
        } catch (DBALException $exception) {
            throw new CronjobEnableException('Cannot save cron jobs enable flag in database.', 0, $exception);
        }
    }

    /**
     * @throws CronjobEnableException
     */
    public function disableAllCronjobExecution(): void
    {
        try {
            $this->connection->executeUpdate("UPDATE `cms_config` SET `cronjobs_enabled` = '0'");
            $this->cache->callTrigger('cms_config');
        } catch (DBALException $exception) {
            throw new CronjobEnableException('Cannot reset cron jobs enable flag in database.', 0, $exception);
        }
    }
}

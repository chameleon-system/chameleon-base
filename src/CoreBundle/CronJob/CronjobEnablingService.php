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
use Doctrine\DBAL\Exception;
use esono\pkgCmsCache\CacheInterface;

readonly class CronjobEnablingService implements CronjobEnablingServiceInterface
{
    public function __construct(
        private Connection $connection,
        private CacheInterface $cache)
    {
    }

    public function isCronjobExecutionEnabled(): bool
    {
        $config = \TdbCmsConfig::GetInstance();

        if (null === $config) {
            return false;
        }

        return true === $config->fieldCronjobsEnabled;
    }

    /**
     * @throws CronjobHandlingException
     */
    public function enableCronjobExecution(): void
    {
        try {
            $this->connection->executeStatement("UPDATE `cms_config` SET `cronjobs_enabled` = '1'");
            $this->cache->callTrigger('cms_config');
        } catch (Exception $exception) {
            throw new CronjobHandlingException('Cannot save cron jobs enable flag in database.', 0, $exception);
        }
    }

    /**
     * @throws CronjobHandlingException
     */
    public function disableCronjobExecution(): void
    {
        try {
            $this->connection->executeStatement("UPDATE `cms_config` SET `cronjobs_enabled` = '0'");
            $this->cache->callTrigger('cms_config');
        } catch (Exception $exception) {
            throw new CronjobHandlingException('Cannot reset cron jobs enable flag in database.', 0, $exception);
        }
    }
}

<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Maintenance\MaintenanceMode;

use ChameleonSystem\CoreBundle\Exception\MaintenanceModeErrorException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use esono\pkgCmsCache\CacheInterface;

class MaintenanceModeService implements MaintenanceModeServiceInterface
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

    public function isActive(): bool
    {
        if (true === \TdbCmsConfig::GetInstance()->fieldShutdownWebsites) {
            /*
             * This location should not have been reached if maintenance mode is on.
             * The reason could be that the file/stat cache is outdated. So we refresh it here for the next request / the imminent redirect.
             */
            clearstatcache(true, PATH_MAINTENANCE_MODE_MARKER);

            return true;
        }

        return false;
    }

    public function activate(): void
    {
        try {
            $this->connection->executeUpdate("UPDATE `cms_config` SET `shutdown_websites` = '1'");

            $this->cache->callTrigger('cms_config');
        } catch (DBALException $exception) {
            throw new MaintenanceModeErrorException('Cannot save maintenance mode flag in database.', 0, $exception);
        }

        $this->createMarkerFile();
    }

    public function deactivate(): void
    {
        $this->removeMarkerFile();

        try {
            $this->connection->executeUpdate("UPDATE `cms_config` SET `shutdown_websites` = '0'");

            $this->cache->callTrigger('cms_config');
        } catch (DBALException $exception) {
            throw new MaintenanceModeErrorException('Cannot reset maintenance mode flag in database.', 0, $exception);
        }
    }

    /**
     * @throws MaintenanceModeErrorException
     */
    private function createMarkerFile(): void
    {
        $markerDir = \dirname(PATH_MAINTENANCE_MODE_MARKER);

        if (false === \is_dir($markerDir)) {
            if (false === \mkdir($markerDir, 0777, true) && false === \is_dir($markerDir)) {
                throw new MaintenanceModeErrorException('Cannot create maintenance mode flag directory.');
            }
        }

        $fileSuccess = touch(PATH_MAINTENANCE_MODE_MARKER);

        if (false === $fileSuccess) {
            throw new MaintenanceModeErrorException('Cannot save maintenance mode flag in file system.');
        }
    }

    /**
     * @throws MaintenanceModeErrorException
     */
    private function removeMarkerFile(): void
    {
        if (false === file_exists(PATH_MAINTENANCE_MODE_MARKER)) {
            return;
        }

        $fileSuccess = unlink(PATH_MAINTENANCE_MODE_MARKER);

        if (false === $fileSuccess) {
            throw new MaintenanceModeErrorException('Cannot delete maintenance mode flag in file system.');
        }
    }
}

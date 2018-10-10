<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\MaintenanceMode;

use ChameleonSystem\CoreBundle\Exception\MaintenanceModeErrorException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;

class MaintenanceModeService implements MaintenanceModeServiceInterface
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function isActivated(): bool
    {
        return true === file_exists(PATH_MAINTENANCE_MODE_MARKER);
    }

    public function isActivatedInDb(): bool
    {
        try {
            $col = $this->connection->fetchColumn('SELECT `shutdown_websites` FROM `cms_config`');
        } catch (DBALException $exception) {
            throw new MaintenanceModeErrorException('Cannot determine maintenance mode flag from database', 0, $exception);
        }

        if (false === $col) {
            return false;
        }

        return '1' === $col['shutdown_websites'];
    }

    public function activate(): void
    {
        try {
            $this->connection->executeUpdate("UPDATE `cms_config` SET `shutdown_websites` = '1'");
        } catch (DBALException $exception) {
            throw new MaintenanceModeErrorException('Cannot save maintenance mode flag in database', 0, $exception);
        }

        $this->createMarkerFile();
    }

    public function deactivate(): void
    {
        $this->removeMarkerFile();

        try {
            $this->connection->executeUpdate("UPDATE `cms_config` SET `shutdown_websites` = '0'");
        } catch (DBALException $exception) {
            throw new MaintenanceModeErrorException('Cannot reset maintenance mode flag in database', 0, $exception);
        }
    }

    /**
     * @throws MaintenanceModeErrorException
     */
    protected function createMarkerFile(): void
    {
        $markerDir = \dirname(PATH_MAINTENANCE_MODE_MARKER);

        if (false === \is_dir($markerDir)) {
            if (false === \mkdir($markerDir, 0777, true) && false === \is_dir($markerDir)) {
                throw new MaintenanceModeErrorException('Cannot create maintenance mode flag directory');
            }
        }

        $fileSuccess = touch(PATH_MAINTENANCE_MODE_MARKER);

        if (false === $fileSuccess) {
            throw new MaintenanceModeErrorException('Cannot save maintenance mode flag in file system');
        }
    }

    /**
     * @throws MaintenanceModeErrorException
     */
    protected function removeMarkerFile(): void
    {
        if (false === file_exists(PATH_MAINTENANCE_MODE_MARKER)) {
            return;
        }

        $fileSuccess = unlink(PATH_MAINTENANCE_MODE_MARKER);

        if (false === $fileSuccess) {
            throw new MaintenanceModeErrorException('Cannot delete maintenance mode flag in file system');
        }
    }
}

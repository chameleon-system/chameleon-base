<?php

namespace ChameleonSystem\CoreBundle\Service;

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
        return file_exists(PATH_MAINTENANCE_MODE_MARKER);
    }

    public function isActivatedInDb(): bool
    {
        $col = $this->connection->fetchColumn("SELECT `shutdown_websites` FROM `cms_config`");

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
        
        $fileSuccess = touch(PATH_MAINTENANCE_MODE_MARKER);

        if (false === $fileSuccess) {
            throw new MaintenanceModeErrorException('Cannot save maintenance mode flag in file system');
        }
    }

    public function deactivate(): void
    {
        if (true === file_exists(PATH_MAINTENANCE_MODE_MARKER)) {
            $fileSuccess = unlink(PATH_MAINTENANCE_MODE_MARKER);

            if (false === $fileSuccess) {
                throw new MaintenanceModeErrorException('Cannot delete maintenance mode flag in file system');
            }
        }

        try {
            $this->connection->executeUpdate("UPDATE `cms_config` SET `shutdown_websites` = '0'");
        } catch (DBALException $exception) {
            throw new MaintenanceModeErrorException('Cannot reset maintenance mode flag in database', 0, $exception);
        }
    }
}

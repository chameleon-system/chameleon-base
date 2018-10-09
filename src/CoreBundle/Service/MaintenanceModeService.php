<?php

namespace ChameleonSystem\CoreBundle\Service;

use Doctrine\DBAL\Connection;

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
        $this->connection->executeUpdate("UPDATE `cms_config` SET `shutdown_websites` = '1'");
        
        touch(PATH_MAINTENANCE_MODE_MARKER);
    }

    public function deactivate(): void
    {
        if (file_exists(PATH_MAINTENANCE_MODE_MARKER)) {
            unlink(PATH_MAINTENANCE_MODE_MARKER);
        }

        $this->connection->executeUpdate("UPDATE `cms_config` SET `shutdown_websites` = '0'");
    }
}

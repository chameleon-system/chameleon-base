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
use Doctrine\DBAL\Connection;

class MigrationDataAccess implements MigrationDataAccessInterface
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessedMigrationData()
    {
        $query = 'SELECT c.`name` AS bundle_name, f.`build_number` 
                  FROM `cms_migration_counter` AS c
                  JOIN `cms_migration_file` AS f
                  ON c.`id` = f.`cms_migration_counter_id`
                  ORDER BY c.`name`, f.`build_number`';

        return $this->connection->fetchAllAssociative($query);
    }

    /**
     * {@inheritdoc}
     */
    public function markMigrationFileAsProcessed($counterId, $buildNumber)
    {
        $this->connection->insert('cms_migration_file', [
            'id' => \TTools::GetUUID(),
            'cms_migration_counter_id' => $counterId,
            'build_number' => $buildNumber,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getMigrationCounterIdsByBundle()
    {
        $data = $this->connection->fetchAllAssociative('SELECT `id`, `name` FROM `cms_migration_counter`');
        $result = [];
        foreach ($data as $row) {
            $result[$row['name']] = $row['id'];
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function createMigrationCounter($bundleName)
    {
        $this->connection->insert('cms_migration_counter', [
           'id' => \TTools::GetUUID(),
           'name' => $bundleName,
       ]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMigrationCounter($counterId)
    {
        $this->connection->delete('cms_migration_counter', [
            'id' => $counterId,
        ]);
    }
}

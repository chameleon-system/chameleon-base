<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\UpdateCounterMigrationBundle\DataAccess;

use ChameleonSystem\UpdateCounterMigrationBundle\Exception\CounterMigrationException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use TdbCmsConfig;
use TTools;

class CounterMigrationDataAccess implements CounterMigrationDataAccessInterface
{
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var TTools
     */
    private $tools;

    /**
     * @param Connection $connection
     * @param TTools     $tools
     */
    public function __construct(Connection $connection, TTools $tools)
    {
        $this->connection = $connection;
        $this->tools = $tools;
    }

    /**
     * {@inheritdoc}
     */
    public function copyCounter($from, $to)
    {
        $query = 'SELECT `value`, `cms_config_id` FROM `cms_config_parameter` WHERE `systemname` = :systemname';
        $sourceData = $this->connection->fetchAssoc($query, array(
            'systemname' => $from,
        ));

        if (empty($sourceData)) {
            throw new CounterMigrationException("Expected existing counter '$from', but did not find it.");
        }
        $targetData = $this->connection->fetchAssoc($query, array(
            'systemname' => $to,
        ));
        if (empty($targetData)) {
            $query = 'INSERT INTO `cms_config_parameter` SET `value` = :value, `cms_config_id` = :configId, `id` = :id, `systemname` = :systemname';
            $id = $this->tools->GetUUID();
            try {
                $this->connection->executeQuery($query, array(
                    'value' => $sourceData['value'],
                    'configId' => $sourceData['cms_config_id'],
                    'id' => $id,
                    'systemname' => $to,
                ));
            } catch (DBALException $e) {
                throw new CounterMigrationException('Error while copying counter.', 0, $e);
            }
        } else {
            $sourceBuildNumbers = json_decode($sourceData['value'], true);
            $targetBuildNumbers = json_decode($targetData['value'], true);
            $targetBuildNumbers = array_merge($targetBuildNumbers, $sourceBuildNumbers);
            $targetBuildNumbers = array_unique($targetBuildNumbers);
            sort($targetBuildNumbers);

            $query = 'UPDATE `cms_config_parameter` SET `value` = :value WHERE `id` = :id';
            try {
                $this->connection->executeQuery($query, array(
                    'value' => json_encode($targetBuildNumbers),
                    'id' => $targetData['id'],
                ));
            } catch (DBALException $e) {
                throw new CounterMigrationException("Error while merging counter with systemname $from", 0, $e);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addUpdatesToCounter(array $updates, $counter)
    {
        $query = 'SELECT `value` FROM `cms_config_parameter` WHERE `systemname` = :systemname';
        $sourcedata = $this->connection->fetchAssoc($query, array('systemname' => $counter));

        if (empty($sourcedata)) {
            throw new CounterMigrationException("Expected existing counter '$counter', but did not find it.");
        }
        $rawValue = $sourcedata['value'];
        $decodedValue = json_decode($rawValue, true);
        foreach ($updates as $newUpdate) {
            if (!in_array($newUpdate, $decodedValue['buildNumbers'])) {
                $decodedValue['buildNumbers'][] = strval($newUpdate);
            }
        }
        $newValue = json_encode($decodedValue);
        $query = 'UPDATE `cms_config_parameter` SET `value` = :value WHERE `systemname` = :systemname';
        try {
            $this->connection->executeQuery($query, array('value' => $newValue, 'systemname' => $counter));
        } catch (DBALException $e) {
            throw new CounterMigrationException('Error while adding updates to counter.', 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function counterExists($counterName)
    {
        $query = 'SELECT * FROM `cms_migration_counter` WHERE `name` = :name';
        try {
            $sourcedata = $this->connection->fetchAssoc($query, array('name' => $counterName));

            return !(empty($sourcedata));
        } catch (DBALException $e) {
            throw new CounterMigrationException('Error while checking if counter exists.', 0, $e);
        }
    }

    /**
     * @return int
     */
    public function getMigrationCounterVersion()
    {
        $query = 'SELECT `value` FROM `cms_config_parameter` WHERE `systemname` = \'database-migration-counter-version\'';
        $result = $this->connection->fetchColumn($query);

        return false === $result ? 0 : $result;
    }

    /**
     * {@inheritdoc}
     */
    public function saveMigrationCounterVersion($version)
    {
        $query = 'SELECT `id` FROM `cms_config_parameter` WHERE `systemname` = \'database-migration-counter-version\'';
        $result = $this->connection->fetchColumn($query);

        if (false === $result) {
            $id = TTools::GetUUID();
        } else {
            $id = $result;
        }

        $cmsConfig = TdbCmsConfig::GetInstance();

        $query = "INSERT INTO `cms_config_parameter` 
                  SET `id` = :id, 
                      `cms_config_id` = :configId, 
                      `systemname` = 'database-migration-counter-version',
                      `name` = 'Database migration counter version',
                      `value` = :value
                  ON DUPLICATE KEY UPDATE `value` = :value
                  "
        ;
        $this->connection->executeUpdate($query, array(
            'id' => $id,
            'configId' => $cmsConfig->id,
            'value' => (string) $version,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getAllCountersVersionOne()
    {
        $query = "SELECT * 
                    FROM `cms_config_parameter` 
                   WHERE `systemname` LIKE 'dbversion-meta-%' 
                     AND `value` LIKE '%buildNumbers%' 
                ORDER BY `systemname`";

        return $this->connection->fetchAll($query);
    }

    /**
     * {@inheritdoc}
     */
    public function createCountersVersionTwo(array $counterData)
    {
        $query = 'INSERT INTO `cms_migration_counter` SET `id` = :id, `name` = :name';
        $addCounterStatement = $this->connection->prepare($query);
        $query = 'INSERT INTO `cms_migration_file` SET `id` = :id, `cms_migration_counter_id` = :migrationCounterId, `build_number` = :buildNumber';
        $addFileStatement = $this->connection->prepare($query);
        foreach ($counterData as $name => $buildNumbers) {
            $migrationCounterId = TTools::GetUUID();
            $addCounterStatement->execute(array(
                'id' => $migrationCounterId,
                'name' => $name,
            ));
            foreach ($buildNumbers as $buildNumber) {
                $addFileStatement->execute(array(
                    'id' => TTools::GetUUID(),
                    'migrationCounterId' => $migrationCounterId,
                    'buildNumber' => $buildNumber,
                ));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCountersVersionOne($systemNamePattern, array $excludePatterns = array())
    {
        $query = 'DELETE FROM `cms_config_parameter` WHERE `systemname` LIKE :systemNamePattern';

        foreach ($excludePatterns as $excludePattern) {
            $quotedPattern = $this->connection->quote($excludePattern);
            $query .= " AND `systemname` NOT LIKE $quotedPattern";
        }

        $this->connection->executeUpdate($query, array(
            'systemNamePattern' => $systemNamePattern,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function createMigrationTablesVersionTwo()
    {
        if (true === \TGlobal::TableExists('cms_migration_counter')) {
            return;
        }

        $query = "CREATE TABLE `cms_migration_counter` (
                    `id` CHAR(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
                    `cmsident` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Key is used so that records can be easily identified in Chameleon',
                    `name` VARCHAR(255) NOT NULL COMMENT 'Name: ',
                    PRIMARY KEY ( `id` ),
                    UNIQUE (`cmsident`)
                  ) ENGINE = InnoDB";
        $this->connection->executeUpdate($query);

        $query = "CREATE TABLE `cms_migration_file` (
                    `id` CHAR(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
                    `cmsident` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Key is used so that records can be easily identified in Chameleon',
                    `cms_migration_counter_id` CHAR(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL REFERENCES `cms_migration_counter`(`id`),
                    `build_number` BIGINT NOT NULL COMMENT 'Build number: ',
                    PRIMARY KEY ( `id` ),
                    CONSTRAINT `fk_cms_migration_counter_id` FOREIGN KEY (`cms_migration_counter_id`) REFERENCES `cms_migration_counter`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                    UNIQUE (`cmsident`)
                  ) ENGINE = InnoDB";
        $this->connection->executeUpdate($query);
    }
}

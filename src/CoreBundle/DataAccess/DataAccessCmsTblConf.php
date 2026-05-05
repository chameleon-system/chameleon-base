<?php

namespace ChameleonSystem\CoreBundle\DataAccess;

use ChameleonSystem\CoreBundle\DataModel\TableConfigurationDataModel;
use Doctrine\DBAL\Connection;

class DataAccessCmsTblConf implements DataAccessCmsTblConfInterface
{
    private array $tableConfigRuntimeCache = [];
    public function __construct(readonly private Connection $connection)
    {
    }

    public function getTableConfigurations(): array
    {
        $query = 'SELECT `id`, `name`, `cms_usergroup_id` FROM `cms_tbl_conf`';
        $tableRows = $this->connection->fetchAllAssociative($query);

        return array_reduce($tableRows, static function (array $carry, array $row) {
            $carry[$row['id']] = new TableConfigurationDataModel($row['id'], $row['name'], $row['cms_usergroup_id']);

            return $carry;
        }, []);
    }

    public function isTableName(string $tableName): bool
    {
        $tableExists = $this->connection->fetchOne(
            'SELECT EXISTS(SELECT 1 FROM `cms_tbl_conf` WHERE `name` = :tableName)',
            ['tableName' => $tableName]
        );

        return (bool) $tableExists;
    }

    public function getPermittedRoles(string $action, string $tableName): array
    {
        if (false === array_key_exists($action, self::PERMISSION_MAPPING)) {
            return [];
        }

        $permissionTable = sprintf('cms_tbl_conf_%s', self::PERMISSION_MAPPING[$action]);

        $query = sprintf(
            'SELECT %1$s.`target_id`
                      FROM %1$s
                INNER JOIN `cms_tbl_conf` ON %1$s.`source_id` = `cms_tbl_conf`.`id`
                     WHERE `cms_tbl_conf`.`name` = :tableName',
            $this->connection->quoteIdentifier($permissionTable)
        );
        $permittedRoleRows = $this->connection->fetchAllAssociative(
            $query,
            ['tableName' => $tableName]
        );

        return array_map(
            static fn (array $row) => $row['target_id'],
            $permittedRoleRows
        );
    }

    public function getGroupIdForTable(string $tableName): ?string
    {
        $groupId = $this->connection->fetchOne(
            'SELECT `cms_usergroup_id` FROM `cms_tbl_conf` WHERE `name` = :tableName',
            ['tableName' => $tableName]
        );

        if (false === $groupId) {
            return null;
        }

        return $groupId;
    }

    public function getTableConfRowByName(string $tableName): ?array
    {
        if (true === array_key_exists($tableName, $this->tableConfigRuntimeCache)) {
            return $this->tableConfigRuntimeCache[$tableName];
        }
        $this->tableConfigRuntimeCache[$tableName] = $this->connection->fetchAssociative('SELECT * FROM `cms_tbl_conf` WHERE `name` = :tableName', ['tableName' => $tableName]);
        if (false === $this->tableConfigRuntimeCache[$tableName]) {
            $this->tableConfigRuntimeCache[$tableName] = null;
        }

        return $this->tableConfigRuntimeCache[$tableName];
    }

    private array $tableOrderFieldCache = [];
    public function getTableOrderFields(string $tableName): array
    {
        if (true === array_key_exists($tableName, $this->tableOrderFieldCache)) {
            return $this->tableOrderFieldCache[$tableName];
        }

        $query = 'SELECT `cms_tbl_display_orderfields`.*
                        FROM `cms_tbl_display_orderfields`
                  INNER JOIN `cms_tbl_conf` ON `cms_tbl_display_orderfields`.`cms_tbl_conf_id` = `cms_tbl_conf`.`id`
                       WHERE `cms_tbl_conf`.`name` = :targetTableName
                    ORDER BY `cms_tbl_display_orderfields`.`position` ASC
                     ';
        $this->tableOrderFieldCache[$tableName] = $this->connection->fetchAllAssociative($query, ['targetTableName' => $tableName]);

        return $this->tableOrderFieldCache[$tableName];
    }


}

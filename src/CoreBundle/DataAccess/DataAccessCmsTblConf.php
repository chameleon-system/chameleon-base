<?php

namespace ChameleonSystem\CoreBundle\DataAccess;

use ChameleonSystem\CoreBundle\DataModel\TableConfigurationDataModel;
use Doctrine\DBAL\Connection;

class DataAccessCmsTblConf implements DataAccessCmsTblConfInterface
{
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
}

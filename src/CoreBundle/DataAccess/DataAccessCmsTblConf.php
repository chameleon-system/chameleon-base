<?php

namespace ChameleonSystem\CoreBundle\DataAccess;

use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;
use Doctrine\DBAL\Connection;

class DataAccessCmsTblConf implements DataAccessCmsTblConfInterface
{
    public function __construct(readonly private Connection $connection)
    {
    }

    public function getTableNames(): array
    {
        $query = "SELECT `id`, `name` FROM `cms_tbl_conf`";
        $tableRows = $this->connection->fetchAllAssociative($query);

        return array_reduce($tableRows, static function (array $carry, array $row) {
            $carry[$row['id']] = $row['name'];

            return $carry;
        }, []);

    }

    public function getPermittedRoles(string $action, string $tableName): array
    {
        if (false === array_key_exists($action, self::PERMISSION_MAPPING)) {
            return [];
        }

        $permissionTable = sprintf('cms_tbl_conf_%s',self::PERMISSION_MAPPING[$action]);

        $query = sprintf(
            'SELECT %1$s.`target_id`
                      FROM %1$s
                INNER JOIN `cms_tbl_conf` ON %1$s.`source_id` = `cms_tbl_conf`.`id`
                     WHERE `cms_tbl_conf`.`name` = :tableName',
            $this->connection->quoteIdentifier($permissionTable)
        );
        return array_map(
            static fn(array $row) => $row['target_id'],
            $this->connection->fetchAllAssociative(
                $query,
                ['tableName' => $tableName]
            )
        );
    }


}
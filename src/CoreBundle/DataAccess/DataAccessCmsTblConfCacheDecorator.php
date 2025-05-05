<?php

namespace ChameleonSystem\CoreBundle\DataAccess;

use ChameleonSystem\CoreBundle\DataModel\TableConfigurationDataModel;
use esono\pkgCmsCache\CacheInterface;

class DataAccessCmsTblConfCacheDecorator implements DataAccessCmsTblConfInterface
{
    private ?array $tableConfigurations = null;
    private ?array $tableNameToIdMap = null;

    public function __construct(
        readonly private DataAccessCmsTblConfInterface $subject,
        readonly private CacheInterface $cache
    ) {
    }

    public function getTableConfigurations(): array
    {
        if (null !== $this->tableConfigurations) {
            return $this->tableConfigurations;
        }
        $keyParam = ['class' => __CLASS__, 'fnc' => 'tableNames'];
        $key = $this->cache->getKey($keyParam, false);

        $this->tableConfigurations = $this->cache->get($key);
        if (null !== $this->tableConfigurations) {
            return $this->tableConfigurations;
        }

        $this->tableConfigurations = $this->subject->getTableConfigurations();
        $this->cache->set($key, $this->tableConfigurations, [['table' => 'cms_tbl_conf', 'id' => null]]);

        return $this->tableConfigurations;
    }

    public function isTableName(string $tableName): bool
    {
        $tableNameToIdMap = $this->getTableNameToIdMap();

        return array_key_exists($tableName, $tableNameToIdMap);
    }

    private function getTableNameToIdMap(): array
    {
        if (null !== $this->tableNameToIdMap) {
            return $this->tableNameToIdMap;
        }

        $tables = $this->getTableConfigurations();
        $this->tableNameToIdMap = [];
        foreach (array_keys($tables) as $tableId) {
            $this->tableNameToIdMap[$tables[$tableId]->name] = $tableId;
        }

        return $this->tableNameToIdMap;
    }

    private function getTableConf(string $tableName): ?TableConfigurationDataModel
    {
        $mapping = $this->getTableNameToIdMap();
        if (false === array_key_exists($tableName, $mapping)) {
            return null;
        }

        return $this->tableConfigurations[$mapping[$tableName]] ?? null;
    }

    public function getGroupIdForTable(string $tableName): ?string
    {
        $tableConf = $this->getTableConf($tableName);

        if (null === $tableConf) {
            return null;
        }

        return $tableConf->cmsUsergroupId;
    }

    public function getPermittedRoles(string $action, string $tableName): array
    {
        $keyParam = [
            'class' => __CLASS__,
            'fnc' => 'getPermittedRoles',
            'action' => $action,
            'tableName' => $tableName,
        ];
        $key = $this->cache->getKey($keyParam, false);

        $permittedRoleIds = $this->cache->get($key);
        if (null !== $permittedRoleIds) {
            return $permittedRoleIds;
        }

        $permittedRoleIds = $this->subject->getPermittedRoles($action, $tableName);
        $this->cache->set($key, $permittedRoleIds, [['table' => 'cms_tbl_conf', 'id' => null]]);

        return $permittedRoleIds;
    }
}

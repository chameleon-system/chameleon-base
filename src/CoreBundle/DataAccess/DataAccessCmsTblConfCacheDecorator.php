<?php

namespace ChameleonSystem\CoreBundle\DataAccess;

use esono\pkgCmsCache\CacheInterface;

class DataAccessCmsTblConfCacheDecorator implements DataAccessCmsTblConfInterface
{
    public function __construct(readonly private DataAccessCmsTblConfInterface $subject, readonly private CacheInterface $cache)
    {
    }

    public function getTableNames(): array
    {
        $keyParam = ['class' => __CLASS__, 'fnc' => 'tableNames'];
        $key = $this->cache->getKey($keyParam, false);

        $tables = $this->cache->get($key);
        if (null !== $tables) {
            return $tables;
        }

        $tables = $this->subject->getTableNames();
        $this->cache->set($key, $tables, [['table' =>'cms_tbl_conf', 'id' => null]]);

        return $tables;
    }

    public function getPermittedRoles(string $action, string $tableName): array
    {
        $keyParam = ['class' => __CLASS__, 'fnc' => 'getPermittedRoles', 'action' => $action, 'tableName' => $tableName];
        $key = $this->cache->getKey($keyParam, false);

        $permittedRoleIds = $this->cache->get($key);
        if (null !== $permittedRoleIds) {
            return $permittedRoleIds;
        }

        $permittedRoleIds = $this->subject->getPermittedRoles($action, $tableName);
        $this->cache->set($key, $permittedRoleIds, [['table' =>'cms_tbl_conf', 'id' => null]]);

        return $permittedRoleIds;
    }


}
<?php

namespace ChameleonSystem\SecurityBundle\DataAccess;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class RightsDataAccess implements RightsDataAccessInterface
{
    private ?array $groups = null;
    private ?array $roles = null;
    private ?array $rights = null;

    public function __construct(readonly private Connection $databaseConnection)
    {
    }

    public function getGroupIdBySystemName(string $groupSystemName): ?string
    {
        $groups = $this->getAllGroups();

        return $groups[$groupSystemName] ?? null;
    }

    public function getRoleIdBySystemName(string $roleSystemName): ?string
    {
        $roles = $this->getAllRoles();

        return $roles[$roleSystemName] ?? null;
    }

    public function getRightIdBySystemName(string $rightSystemName): ?string
    {
        $rights = $this->getAllRights();

        return $rights[$rightSystemName] ?? null;
    }

    /**
     * @throws Exception
     */
    private function getAllGroups(): array
    {
        if (null !== $this->groups) {
            return $this->groups;
        }

        $query = 'SELECT `id`,`internal_identifier` FROM `cms_usergroup`';

        $this->groups = [];
        $result = $this->databaseConnection->fetchAllAssociative($query);
        foreach ($result as $row) {
            $this->groups[$row['internal_identifier']] = $row['id'];
        }

        return $this->groups;
    }

    /**
     * @throws Exception
     */
    private function getAllRoles(): array
    {
        if (null !== $this->roles) {
            return $this->roles;
        }

        $query = 'SELECT `id`,`name` FROM `cms_role`';

        $this->roles = [];
        $result = $this->databaseConnection->fetchAllAssociative($query);
        foreach ($result as $row) {
            $this->roles[$row['name']] = $row['id'];
        }

        return $this->roles;
    }

    /**
     * @throws Exception
     */
    private function getAllRights(): array
    {
        if (null !== $this->rights) {
            return $this->rights;
        }

        $query = 'SELECT `id`,`name` FROM `cms_right`';

        $this->rights = [];
        $result = $this->databaseConnection->fetchAllAssociative($query);
        foreach ($result as $row) {
            $this->rights[$row['name']] = $row['id'];
        }

        return $this->rights;
    }
}

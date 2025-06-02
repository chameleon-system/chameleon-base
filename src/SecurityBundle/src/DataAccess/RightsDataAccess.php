<?php

namespace ChameleonSystem\SecurityBundle\DataAccess;

use ChameleonSystem\SecurityBundle\Voter\CmsVoterPrefixConstants;
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

        // If the group name already has the prefix, use it as is
        if (str_starts_with($groupSystemName, CmsVoterPrefixConstants::GROUP)) {
            return $groups[$groupSystemName] ?? null;
        }

        // Otherwise, add the prefix and convert to uppercase
        $formattedGroupName = sprintf(CmsVoterPrefixConstants::GROUP.'%s', mb_strtoupper($groupSystemName));

        return $groups[$formattedGroupName] ?? null;
    }

    public function getRoleIdBySystemName(string $roleSystemName): ?string
    {
        $roles = $this->getAllRoles();

        // If the role name already has the prefix, use it as is
        if (str_starts_with($roleSystemName, CmsVoterPrefixConstants::ROLE)) {
            return $roles[$roleSystemName] ?? null;
        }

        // Otherwise, add the prefix and convert to uppercase
        $formattedRoleName = sprintf(CmsVoterPrefixConstants::ROLE.'%s', mb_strtoupper($roleSystemName));

        return $roles[$formattedRoleName] ?? null;
    }

    public function getRightIdBySystemName(string $rightSystemName): ?string
    {
        $rights = $this->getAllRights();

        // If the right name already has the prefix, use it as is
        if (str_starts_with($rightSystemName, CmsVoterPrefixConstants::RIGHT)) {
            return $rights[$rightSystemName] ?? null;
        }

        // Otherwise, add the prefix and convert to uppercase
        $formattedRightName = sprintf(CmsVoterPrefixConstants::RIGHT.'%s', mb_strtoupper($rightSystemName));

        return $rights[$formattedRightName] ?? null;
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
            $formattedName = sprintf(CmsVoterPrefixConstants::GROUP.'%s', mb_strtoupper($row['internal_identifier']));
            $this->groups[$formattedName] = $row['id'];
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
            $formattedName = sprintf(CmsVoterPrefixConstants::ROLE.'%s', mb_strtoupper($row['name']));
            $this->roles[$formattedName] = $row['id'];
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
            $formattedName = sprintf(CmsVoterPrefixConstants::RIGHT.'%s', mb_strtoupper($row['name']));
            $this->rights[$formattedName] = $row['id'];
        }

        return $this->rights;
    }
}

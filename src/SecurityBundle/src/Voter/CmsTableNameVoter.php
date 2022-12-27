<?php

namespace ChameleonSystem\SecurityBundle\Voter;

use ChameleonSystem\CoreBundle\DataAccess\DataAccessCmsTblConfInterface;
use ChameleonSystem\SecurityBundle\CmsUser\CmsUserModel;
use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CmsTableNameVoter extends Voter
{
    public function __construct(readonly private DataAccessCmsTblConfInterface $accessCmsTblConf)
    {
    }

    protected function supports(string $attribute, $subject)
    {
        if (false === in_array($attribute, CmsPermissionAttributeConstants::TABLE_EDITOR_ACTIONS, true)) {
            return false;
        }

        if (false === is_string($subject)) {
            return false;
        }

        $tables = $this->accessCmsTblConf->getTableNames();

        return (in_array($subject, $tables, true));
    }

    /**
     * @param string $attribute
     * @param string $subject
     * @param TokenInterface $token
     * @return bool|void
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        /** @var CmsUserModel|UserInterface $user */
        $user = $token->getUser();
        if (false === ($user instanceof CmsUserModel)) {
            return false;
        }

        if (CmsPermissionAttributeConstants::TABLE_EDITOR_ACCESS === $attribute) {
            if (true === $this->voteOnAttribute(CmsPermissionAttributeConstants::TABLE_EDITOR_ACCESS_ALL, $subject, $token)) {
                return true;
            }

            // also grand access if the user has edit permission
            return $this->voteOnAttribute(CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT_ALL, $subject, $token);
        }

        if (CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT === $attribute || CmsPermissionAttributeConstants::TABLE_EDITOR_DELETE === $attribute) {
            return $this->voteOnAttribute(CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT_ALL, $subject, $token);
        }

        return $this->userHasRoleForTable($attribute, $subject, $user);
    }


    private function userHasRoleForTable(string $attribute, string $tableName, CmsUserModel $user): bool
    {
        $permittedRoleIds = $this->accessCmsTblConf->getPermittedRoles($attribute, $tableName);
        $userRoleIds = array_keys($user->getRoles());

        $roleIntersection = array_intersect($permittedRoleIds, $userRoleIds);
        return (count($roleIntersection) > 0);
    }

}
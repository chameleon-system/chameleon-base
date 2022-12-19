<?php

namespace ChameleonSystem\SecurityBundle\CmsUser;

use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class CmsUserDataAccess implements UserProviderInterface, PasswordUpgraderInterface
{
    public function __construct(readonly private Connection $connection)
    {
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof CmsUserModel) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return CmsUserModel::class === $class || is_subclass_of($class, CmsUserModel::class);
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $query = "SELECT * FROM `cms_user` WHERE `login` = :username AND `allow_cms_login` = '1' LIMIT 0,1";
        $userRow = $this->connection->fetchAssociative($query, ['username' => $identifier]);

        if (false === $userRow) {
            throw new UserNotFoundException(
                sprintf('No cms user "%s" found - or user is not allowed to log into cms.', $userRow)
            );
        }

        $roleRows = $this->connection->fetchAllAssociative(
            "SELECT `cms_role`.* 
                     FROM `cms_role`
               INNER JOIN `cms_user_cms_role_mlt` ON `cms_role`.`id` = `cms_user_cms_role_mlt`.`target_id`
                    WHERE `cms_user_cms_role_mlt`.`source_id` = :userId",
            ['userId' => $userRow['id']]
        );
        $roles = array_map(static fn($row) => sprintf('ROLE_%s', mb_strtoupper($row['name'])), $roleRows);
        if (false === in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }

        return new CmsUserModel(
            $userRow['id'],
            $userRow['login'],
            $userRow['firstname'],
            $userRow['name'],
            $userRow['company'],
            $userRow['email'],
            $userRow['cms_language_id'],
            array_map(
                static fn(string $languageIsoCode) => trim($languageIsoCode),
                explode(',', $userRow['languages'])
            ),
            $userRow['cms_current_edit_language'],
            $userRow['crypted_pw'],
            $roles,
        );
    }

    public function loadUserByUsername(string $username)
    {
        return $this->loadUserByIdentifier($username);
    }


}
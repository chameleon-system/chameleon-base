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

        // todo - refresh only if the user changed.

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
        $roles = array_reduce($roleRows, static function (array $carry, array $row) {
            $carry[$row['id']] =  sprintf('ROLE_%s', mb_strtoupper($row['name']));

            return $carry;
        }, []);
        $roles['-'] = 'ROLE_CMS_USER';


        $userRightRows = $this->connection->fetchAllAssociative(
            "SELECT `cms_right`.`id`, `cms_right`.`name`
                     FROM `cms_right`
               INNER JOIN `cms_role_cms_right_mlt` ON `cms_right`.`id` = `cms_role_cms_right_mlt`.`target_id`
               INNER JOIN `cms_user_cms_role_mlt` ON `cms_role_cms_right_mlt`.`source_id` = `cms_user_cms_role_mlt`.`target_id`
                    WHERE `cms_user_cms_role_mlt`.`source_id` = :userId",
            ['userId' => $userRow['id']]
        );

        $userRights = array_reduce($userRightRows, static function (array $carry, array $row) {
            $carry[$row['id']] = sprintf('CMS_RIGHT_%s',mb_strtoupper($row['name']));

            return $carry;
        }, []);

        $userGroupRows = $this->connection->fetchAllAssociative(
            "SELECT `cms_usergroup`.`id`, `cms_usergroup`.`internal_identifier`
                     FROM `cms_usergroup`
               INNER JOIN `cms_user_cms_usergroup_mlt` ON `cms_user_cms_usergroup_mlt`.`target_id` = `cms_usergroup`.`id`
                    WHERE `cms_user_cms_usergroup_mlt`.`source_id` = :userId",
            ['userId' => $userRow['id']]
        );

        $userGroups = array_reduce($userGroupRows, static function (array $carry, array $row) {
            $carry[$row['id']] = sprintf('CMS_GROUP_%s', mb_strtoupper($row['internal_identifier']));

            return $carry;
        }, []);

        $userPortalRows = $this->connection->fetchAllAssociative(
            "SELECT `cms_portal`.`id`, `cms_portal`.`external_identifier`
                     FROM `cms_portal`
               INNER JOIN `cms_user_cms_portal_mlt` ON `cms_user_cms_portal_mlt`.`target_id` = `cms_portal`.`id`
                    WHERE `cms_user_cms_portal_mlt`.`source_id` = :userId",
            ['userId' => $userRow['id']]
        );

        $userPortals = array_reduce($userPortalRows, static function (array $carry, array $row) {
            $carry[$row['id']] = sprintf('CMS_PORTAL_%s', mb_strtoupper($row['external_identifier']));

            return $carry;
        }, []);

        $query = "SELECT `cms_language`.*
                    FROM `cms_language`
              INNER JOIN `cms_user_cms_language_mlt` ON `cms_user_cms_language_mlt`.`target_id` = `cms_language`.`id`
                   WHERE `cms_user_cms_language_mlt`.`target_id` = :userId
                    ";
        $languagesRows = $this->connection->fetchAllAssociative($query, ['userId' => $userRow['id']]);

        $languagesRows = array_reduce($languagesRows, static function (array $carry, array $row) {
            $carry[$row['iso_6391']] = $row['id'];

            return $carry;
            }, []);

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
            '' !== $userRow['cms_current_edit_language'] ? $userRow['cms_current_edit_language'] : null,
            $languagesRows,
            $userRow['crypted_pw'],
            $roles,
            $userRights,
            $userGroups,
            $userPortals
        );
    }

    public function loadUserByUsername(string $username)
    {
        return $this->loadUserByIdentifier($username);
    }


}
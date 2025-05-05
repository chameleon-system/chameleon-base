<?php

namespace ChameleonSystem\SecurityBundle\CmsUser;

use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;
use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @template-implements UserProviderInterface<CmsUserModel>
 */
class CmsUserDataAccess implements UserProviderInterface, PasswordUpgraderInterface
{
    public function __construct(readonly private Connection $connection)
    {
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        // maybe this should look like this: But I cannot test this at this moment as this method needed to be added during a symfony upgrade
        // $this->connection->update('cms_user', ['crypted_pw' => $newHashedPassword], ['id' => $user->getId()]);
        throw new \RuntimeException('Not implemented');
    }

    public function refreshUser(UserInterface $user): CmsUserModel|UserInterface
    {
        if (!$user instanceof CmsUserModel) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        if (false === $this->userHasBeenModified($user)) {
            return $user;
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return CmsUserModel::class === $class || is_subclass_of($class, CmsUserModel::class);
    }

    /**
     * returns the user with the login equal to the identifier passed, if that user is permitted to log into the cms backend.
     * Throws a UserNotFoundException otherwise.
     */
    public function loadUserByIdentifier(string $identifier): CmsUserModel|UserInterface
    {
        $query = "SELECT * FROM `cms_user` WHERE `login` = :username AND `allow_cms_login` = '1' LIMIT 0,1";
        $userRow = $this->connection->fetchAssociative($query, ['username' => $identifier]);

        if (false === $userRow) {
            throw new UserNotFoundException(
                sprintf('No cms user "%s" found - or user is not allowed to log into cms.', $identifier)
            );
        }

        return $this->createUserFromRow($userRow);
    }

    /**
     * returns the user with the login equal to the $username passed, if that user is permitted to log into the cms backend.
     *  Throws a UserNotFoundException otherwise.
     */
    public function loadUserByUsername(string $username): CmsUserModel|UserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    private function userHasBeenModified(CmsUserModel $user): bool
    {
        try {
            // during upgrade from chameleon 7.1 to 8.0 the field does not yet exist @todo remove try/catch in chameleon 9.0
            $query = 'SELECT `date_modified` FROM `cms_user` WHERE `id` = :userId';
            $dateModified = $this->connection->fetchOne($query, ['userId' => $user->getId()]);
            if (false === $dateModified) {
                return true;
            }

            return $user->getDateModified()->format('Y-m-d H:i:s') < $dateModified;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function loadUserWithBackendLoginPermissionFromSSOID(string $ssoType, string $ssoId): ?CmsUserModel
    {
        $query = "SELECT `cms_user`.*
                    FROM `cms_user`
              INNER JOIN `cms_user_sso` ON `cms_user`.`id` = `cms_user_sso`.`cms_user_id`
                   WHERE `cms_user_sso`.`type` = :ssoType
                     AND `cms_user_sso`.`sso_id` = :ssoId
                     AND `cms_user`.`allow_cms_login` = '1' LIMIT 0,1";
        $userRow = $this->connection->fetchAssociative($query, ['ssoType' => $ssoType, 'ssoId' => $ssoId]);

        if (false === $userRow) {
            return null;
        }

        return $this->createUserFromRow($userRow);
    }

    public function loadUserWithBackendLoginPermissionByEMail(string $email): ?CmsUserModel
    {
        $query = "SELECT * FROM `cms_user` WHERE `email` = :email AND `allow_cms_login` = '1' LIMIT 0,1";
        $userRow = $this->connection->fetchAssociative($query, ['email' => $email]);

        if (false === $userRow) {
            return null;
        }

        return $this->createUserFromRow($userRow);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function createUserFromRow(array $userRow): CmsUserModel
    {
        $roleRows = $this->connection->fetchAllAssociative(
            'SELECT `cms_role`.*
                     FROM `cms_role`
               INNER JOIN `cms_user_cms_role_mlt` ON `cms_role`.`id` = `cms_user_cms_role_mlt`.`target_id`
                    WHERE `cms_user_cms_role_mlt`.`source_id` = :userId',
            ['userId' => $userRow['id']]
        );
        $roles = array_reduce($roleRows, static function (array $carry, array $row) {
            $carry[$row['id']] = sprintf('ROLE_%s', mb_strtoupper($row['name']));

            return $carry;
        }, []);
        $roles[CmsUserRoleConstants::CMS_USER_FAKE_ID] = CmsUserRoleConstants::CMS_USER;

        $userRightRows = $this->connection->fetchAllAssociative(
            'SELECT `cms_right`.`id`, `cms_right`.`name`
                     FROM `cms_right`
               INNER JOIN `cms_role_cms_right_mlt` ON `cms_right`.`id` = `cms_role_cms_right_mlt`.`target_id`
               INNER JOIN `cms_user_cms_role_mlt` ON `cms_role_cms_right_mlt`.`source_id` = `cms_user_cms_role_mlt`.`target_id`
                    WHERE `cms_user_cms_role_mlt`.`source_id` = :userId',
            ['userId' => $userRow['id']]
        );

        $userRights = array_reduce($userRightRows, static function (array $carry, array $row) {
            $carry[$row['id']] = sprintf('CMS_RIGHT_%s', mb_strtoupper($row['name']));

            return $carry;
        }, []);

        $userGroupRows = $this->connection->fetchAllAssociative(
            'SELECT `cms_usergroup`.`id`, `cms_usergroup`.`internal_identifier`
                     FROM `cms_usergroup`
               INNER JOIN `cms_user_cms_usergroup_mlt` ON `cms_user_cms_usergroup_mlt`.`target_id` = `cms_usergroup`.`id`
                    WHERE `cms_user_cms_usergroup_mlt`.`source_id` = :userId',
            ['userId' => $userRow['id']]
        );

        $userGroups = array_reduce($userGroupRows, static function (array $carry, array $row) {
            $carry[$row['id']] = sprintf('CMS_GROUP_%s', mb_strtoupper($row['internal_identifier']));

            return $carry;
        }, []);

        $userPortalRows = $this->connection->fetchAllAssociative(
            'SELECT `cms_portal`.`id`, `cms_portal`.`external_identifier`
                     FROM `cms_portal`
               INNER JOIN `cms_user_cms_portal_mlt` ON `cms_user_cms_portal_mlt`.`target_id` = `cms_portal`.`id`
                    WHERE `cms_user_cms_portal_mlt`.`source_id` = :userId',
            ['userId' => $userRow['id']]
        );

        $userPortals = array_reduce($userPortalRows, static function (array $carry, array $row) {
            $carry[$row['id']] = sprintf('CMS_PORTAL_%s', mb_strtoupper($row['external_identifier']));

            return $carry;
        }, []);

        $query = 'SELECT `cms_language`.*
                    FROM `cms_language`
              INNER JOIN `cms_user_cms_language_mlt` ON `cms_user_cms_language_mlt`.`target_id` = `cms_language`.`id`
                   WHERE `cms_user_cms_language_mlt`.`source_id` = :userId
                    ';
        $languagesRows = $this->connection->fetchAllAssociative($query, ['userId' => $userRow['id']]);

        $languagesRows = array_reduce($languagesRows, static function (array $carry, array $row) {
            $carry[$row['iso_6391']] = $row['id'];

            return $carry;
        }, []);

        $ssoList = [];

        try {
            // during upgrade from chameleon 7.1 to 8.0 the table does not yet exist @todo remove try/catch in chameleon 9.0
            $ssoRows = $this->connection->fetchAllAssociative('SELECT * FROM `cms_user_sso` WHERE `cms_user_id` = :userId', ['userId' => $userRow['id']]);
            foreach ($ssoRows as $ssoRow) {
                $ssoList[] = new CmsUserSSOModel($ssoRow['cms_user_id'], $ssoRow['type'], $ssoRow['sso_id'], $ssoRow['id']);
            }
        } catch (\Exception $e) {
        }

        return new CmsUserModel(
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $userRow['date_modified'] ?? date('Y-m-d H:i:s')),
            $userRow['id'],
            $userRow['login'],
            $userRow['firstname'],
            $userRow['name'],
            $userRow['company'],
            $userRow['email'],
            $userRow['cms_language_id'],
            array_map(
                static fn (string $languageIsoCode) => trim($languageIsoCode),
                explode(',', $userRow['languages'])
            ),
            '' !== $userRow['cms_current_edit_language'] ? $userRow['cms_current_edit_language'] : null,
            $languagesRows,
            $userRow['crypted_pw'],
            $roles,
            $userRights,
            $userGroups,
            $userPortals,
            $ssoList,
            $userRow['dashboard_widget_config'],
            $userRow['google_authenticator_secret']
        );
    }

    public function createUser(CmsUserModel $user): CmsUserModel|UserInterface
    {
        $this->connection->beginTransaction();
        try {
            $this->connection->insert('cms_user', [
                'id' => $user->getId(),
                'login' => $user->getUserIdentifier(),
                'firstname' => $user->getFirstName(),
                'name' => $user->getLastName(),
                'company' => $user->getCompany(),
                'email' => $user->getEmail(),
                'cms_language_id' => $user->getCmsLanguageId(),
                'crypted_pw' => '-',
                'date_modified' => $user->getDateModified()->format('Y-m-d H:i:s'),
                'cms_current_edit_language' => $user->getCurrentEditLanguageIsoCode(),
                'languages' => implode(', ', $user->getAvailableLanguagesIsoCodes()),
                'dashboard_widget_config' => $user->getDashboardWidgetConfig(),
            ]);

            foreach ($user->getSsoIds() as $ssoId) {
                $ssoData = [
                    'cms_user_id' => $ssoId->getCmsUserId(),
                    'type' => $ssoId->getType(),
                    'sso_id' => $ssoId->getSsoId(),
                ];
                if (null !== $ssoId->getId()) {
                    $ssoData['id'] = $ssoId->getId();
                }
                $this->connection->insert('cms_user_sso', $ssoData);
            }

            foreach ($user->getAvailableEditLanguages() as $id => $code) {
                $this->connection->insert('cms_user_cms_language_mlt', [
                    'source_id' => $user->getId(),
                    'target_id' => $id,
                ]);
            }

            foreach ($user->getGroups() as $id => $code) {
                $this->connection->insert('cms_user_cms_usergroup_mlt', [
                    'source_id' => $user->getId(),
                    'target_id' => $id,
                ]);
            }

            foreach ($user->getRoles() as $id => $code) {
                $this->connection->insert('cms_user_cms_role_mlt', [
                    'source_id' => $user->getId(),
                    'target_id' => $id,
                ]);
            }

            foreach ($user->getPortals() as $id => $code) {
                $this->connection->insert('cms_user_cms_portal_mlt', [
                    'source_id' => $user->getId(),
                    'target_id' => $id,
                ]);
            }
        } catch (\Throwable $e) {
            $this->connection->rollBack();
            throw $e;
        }
        $this->connection->commit();

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function updateUser(CmsUserModel $user): CmsUserModel|UserInterface
    {
        $this->connection->beginTransaction();

        try {
            $this->connection->update('cms_user', [
                'login' => $user->getUserIdentifier(),
                'firstname' => $user->getFirstName(),
                'name' => $user->getLastName(),
                'company' => $user->getCompany(),
                'email' => $user->getEmail(),
                'cms_language_id' => $user->getCmsLanguageId(),
                'date_modified' => $user->getDateModified()->format('Y-m-d H:i:s'),
                'cms_current_edit_language' => $user->getCurrentEditLanguageIsoCode(),
                'languages' => implode(', ', $user->getAvailableLanguagesIsoCodes()),
            ], ['id' => $user->getId()]);

            $idList = [];
            foreach ($user->getSsoIds() as $ssoId) {
                $ssoData = [
                    'cms_user_id' => $ssoId->getCmsUserId(),
                    'type' => $ssoId->getType(),
                    'sso_id' => $ssoId->getSsoId(),
                ];
                if (null !== $ssoId->getId()) {
                    $idList[] = $ssoId->getId();
                    $ssoData['id'] = $ssoId->getId();
                    $this->connection->update('cms_user_sso', $ssoData, ['id' => $ssoId->getId()]);
                } else {
                    $idList[] = $this->connection->insert('cms_user_sso', $ssoData);
                }
            }
            $this->connection->executeQuery(
                'DELETE FROM `cms_user_sso` WHERE `cms_user_id` = :cmsUserId AND `id` NOT IN (:idList)',
                ['cmsUserId' => $user->getId(), 'idList' => $idList], ['idList' => Connection::PARAM_STR_ARRAY]
            );

            $this->connection->delete('cms_user_cms_language_mlt', ['source_id' => $user->getId()]);
            foreach ($user->getAvailableEditLanguages() as $id => $code) {
                $this->connection->insert('cms_user_cms_language_mlt', [
                    'source_id' => $user->getId(),
                    'target_id' => $id,
                ]);
            }
            $this->connection->delete('cms_user_cms_usergroup_mlt', ['source_id' => $user->getId()]);
            foreach ($user->getGroups() as $id => $code) {
                $this->connection->insert('cms_user_cms_usergroup_mlt', [
                    'source_id' => $user->getId(),
                    'target_id' => $id,
                ]);
            }

            $this->connection->delete('cms_user_cms_role_mlt', ['source_id' => $user->getId()]);
            foreach ($user->getRoles() as $id => $code) {
                $this->connection->insert('cms_user_cms_role_mlt', [
                    'source_id' => $user->getId(),
                    'target_id' => $id,
                ]);
            }

            $this->connection->delete('cms_user_cms_portal_mlt', ['source_id' => $user->getId()]);
            foreach ($user->getPortals() as $id => $code) {
                $this->connection->insert('cms_user_cms_portal_mlt', [
                    'source_id' => $user->getId(),
                    'target_id' => $id,
                ]);
            }
        } catch (\Throwable $e) {
            $this->connection->rollBack();
            throw $e;
        }

        $this->connection->commit();

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function setGoogleAuthenticatorSecret(CmsUserModel $user, string $secret): void
    {
        $this->connection->update('cms_user', [
            'google_authenticator_secret' => $secret,
        ], ['id' => $user->getId()]);
    }
}

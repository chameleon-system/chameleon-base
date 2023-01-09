<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DistributionBundle\Bootstrap;

use ChameleonSystem\CoreBundle\DatabaseAccessLayer\DatabaseAccessLayerFieldConfig;
use ChameleonSystem\CoreBundle\Security\Password\PasswordHashGeneratorInterface;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class InitialBackendUserCreator
{
    /**
     * @var LanguageServiceInterface
     */
    private $languageService;
    /**
     * @var Connection
     */
    private $databaseConnection;
    /**
     * @var DatabaseAccessLayerFieldConfig
     */
    private $fieldConfigDataAccess;
    /**
     * @var PasswordHashGeneratorInterface
     */
    private $passwordHashGenerator;

    public function __construct(
        LanguageServiceInterface $languageService,
        Connection $databaseConnection,
        DatabaseAccessLayerFieldConfig $fieldConfigDataAccess,
        PasswordHashGeneratorInterface $passwordHashGenerator
    ) {
        $this->languageService = $languageService;
        $this->databaseConnection = $databaseConnection;
        $this->fieldConfigDataAccess = $fieldConfigDataAccess;
        $this->passwordHashGenerator = $passwordHashGenerator;
    }

    public function create(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper): void
    {
        $query = "SELECT 1 FROM `cms_user` WHERE `login` != 'www' LIMIT 1";
        $hasUser = $this->databaseConnection->fetchColumn($query);
        if ('1' === $hasUser) {
            throw new \LogicException('This command can only be used to create an initial user, but at least one user already exists.');
        }

        $name = $this->getUsername($questionHelper, $input, $output);
        $password = $this->getPassword($questionHelper, $input, $output);

        $this->doCreate($name, $password);
    }

    private function getUsername(QuestionHelper $questionHelper, InputInterface $input, OutputInterface $output): string
    {
        $username = \getenv('APP_INITIAL_BACKEND_USER_NAME');
        if (false === $username) {
            $question = new Question('Please enter the name of the initial backend user (may be everything but "www"): ');
            $username = $questionHelper->ask($input, $output, $question);
        }

        if (null === $username) {
            throw new \InvalidArgumentException('No user name specified.');
        }

        if ('www' === $username) {
            throw new \InvalidArgumentException('The name of the user must not be "www".');
        }

        return $username;
    }

    private function getPassword(QuestionHelper $questionHelper, InputInterface $input, OutputInterface $output): string
    {
        $password = \getenv('APP_INITIAL_BACKEND_USER_PASSWORD');
        $minimumLength = $this->getMinimumPasswordLength();
        if (false === $password) {
            $password = $this->askPassword($questionHelper, $input, $output, $minimumLength);
        } else {
            if (\mb_strlen(\trim($password)) < $minimumLength) {
                throw new \InvalidArgumentException(sprintf('Password is too short. Please use at least %s characters.', $minimumLength));
            }
        }

        if (null === $password) {
            throw new \InvalidArgumentException('No password specified.');
        }

        return $this->passwordHashGenerator->hash($password);
    }

    private function askPassword(QuestionHelper $questionHelper, InputInterface $input, OutputInterface $output, int $minimumLength): ?string
    {
        $question = new Question(\sprintf('Please enter the password of the initial backend user (at least %s characters): ', $minimumLength));
        $question->setHidden(true);
        $question->setValidator(function ($value) use ($minimumLength) {
            if (\mb_strlen(\trim($value)) < $minimumLength) {
                throw new \InvalidArgumentException(sprintf('Password is too short. Please use at least %s characters.', $minimumLength));
            }

            return $value;
        });

        return $questionHelper->ask($input, $output, $question);
    }

    private function getMinimumPasswordLength(): int
    {
        $definition = $this->fieldConfigDataAccess->GetFieldDefinition('cms_user', 'crypted_pw');
        $minimumLength = $definition->GetFieldtypeConfigKey('minimumLength');
        if (false === \is_numeric($minimumLength)) {
            return \TCMSFieldPassword::DEFAULT_MINIMUM_PASSWORD_LENGTH;
        }

        return (int) $minimumLength;
    }

    private function doCreate(string $name, string $password): void
    {
        $userId = \TTools::GetUUID();

        $this->addUser($userId, $name, $password);
        $this->addUserRoles($userId);
        $this->addUserGroups($userId);
    }

    private function addUser(string $userId, string $name, string $password): void
    {
        $languageEn = $this->languageService->getLanguageFromIsoCode('en');

        $this->databaseConnection->insert('cms_user', [
            'id' => $userId,
            'cmsident' => null,
            'login' => $name,
            'crypted_pw' => $password,
            'company' => '',
            'department' => '',
            'firstname' => '',
            'name' => $name,
            'city' => '',
            'email' => '',
            'tel' => '',
            'fax' => '',
            'cms_language_id' => $languageEn->id,
            'user_tbl_conf_hidden' => '',
            'languages' => 'en',
            'images' => '1',
            'cms_current_edit_language' => 'en',
            'allow_cms_login' => '1',
            'cms_workflow_transaction_id' => '',
            'task_show_count' => '5',
            'is_system' => '1',
            'show_as_rights_template' => '1',
        ]);

        $this->connectUserToLanguages($userId);
        $this->connectUserToPortals($userId);
    }

    private function connectUserToLanguages(string $userId): void
    {
        $languageList = \TdbCmsLanguageList::GetList();
        $position = 0;
        while (false !== $language = $languageList->Next()) {
            $this->databaseConnection->insert('cms_user_cms_language_mlt', [
                'source_id' => $userId,
                'target_id' => $language->id,
                'entry_sort' => ++$position,
            ]);
        }
    }

    private function connectUserToPortals(string $userId): void
    {
        $portalList = \TdbCmsPortalList::GetList();
        $position = 0;
        while (false !== $portal = $portalList->Next()) {
            $this->databaseConnection->insert('cms_user_cms_portal_mlt', [
                'source_id' => $userId,
                'target_id' => $portal->id,
                'entry_sort' => ++$position,
            ]);
        }
    }

    private function addUserRoles(string $userId): void
    {
        $userRoleList = $this->databaseConnection->fetchAllAssociative('SELECT `id` FROM `cms_role`');

        $query = 'INSERT INTO `cms_user_cms_role_mlt` (`source_id`, `target_id`, `entry_sort`) VALUES (?, ?, ?)';
        $statement = $this->databaseConnection->prepare($query);
        $i = 0;
        foreach ($userRoleList as $subList) {
            $roleId = $subList['id'];
            $statement->execute([
                $userId,
                $roleId,
                $i++,
            ]);
        }
    }

    private function addUserGroups(string $userId): void
    {
        $userGroupList = $this->databaseConnection->fetchAllAssociative('SELECT `id` FROM `cms_usergroup`');

        $query = 'INSERT INTO `cms_user_cms_usergroup_mlt` (`source_id`, `target_id`, `entry_sort`) VALUES (?, ?, ?)';
        $statement = $this->databaseConnection->prepare($query);
        $i = 0;
        foreach ($userGroupList as $subList) {
            $userGroupId = $subList['id'];
            $statement->execute([
                $userId,
                $userGroupId,
                $i++,
            ]);
        }
    }
}

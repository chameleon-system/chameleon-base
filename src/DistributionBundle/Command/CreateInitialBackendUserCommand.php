<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DistributionBundle\Command;

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\DistributionBundle\Bootstrap\InitialBackendUserCreator;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command for creating backend users.
 */
#[\Symfony\Component\Console\Attribute\AsCommand(description: 'Creates a backend user.', name: 'chameleon_system:bootstrap:create_initial_backend_user')]
class CreateInitialBackendUserCommand extends Command
{
    /**
     * @var InitialBackendUserCreator
     */
    private $initialBackendUserCreator;

    public function __construct(InitialBackendUserCreator $initialBackendUserCreator)
    {
        parent::__construct('chameleon_system:bootstrap:create_initial_backend_user');
        $this->initialBackendUserCreator = $initialBackendUserCreator;
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setDefinition([])
            ->setHelp(<<<EOF
The <info>%command.name%</info> command creates the initial Chameleon backend user, either by accepting arguments from the environment
or interactively. The user created by this command is a basic stub that is minimally configured to be able to log into the backend.
The user should be edited in the backend thereafter.
Possible environment variables:
- APP_INITIAL_BACKEND_USER_NAME: The user name
- APP_INITIAL_BACKEND_USER_PASSWORD The password
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userId = $this->initialBackendUserCreator->create($input, $output, $this->getHelper('question'));

        $this->updateDefaultCmsUserIdFieldsWithNewUserId($userId);

        return 0;
    }

    private function updateDefaultCmsUserIdFieldsWithNewUserId(string $userId): void
    {
        $dbConnection = $this->getDatabaseConnection();

        $query = "SELECT TABLE_SCHEMA, TABLE_NAME, COLUMN_NAME
                    FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE COLUMN_NAME = 'cms_user_id'";

        $tables = $dbConnection->fetchAllAssociative($query);

        foreach ($tables as $table) {
            $updateQuery = sprintf("UPDATE `%s` SET `cms_user_id` = '%s' WHERE `cms_user_id` = '1'", $table['TABLE_NAME'], $userId);

            $dbConnection->executeQuery($updateQuery);
        }
    }

    private function getDatabaseConnection(): Connection
    {
        return ServiceLocator::get('database_connection');
    }
}

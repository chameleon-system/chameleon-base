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

use ChameleonSystem\DistributionBundle\Bootstrap\InitialBackendUserCreator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command for creating backend users.
 */
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
            ->setDefinition(array())
            ->setDescription('Creates a backend user.')
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
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->initialBackendUserCreator->create($input, $output, $this->getHelper('question'));

        return 0;
    }
}

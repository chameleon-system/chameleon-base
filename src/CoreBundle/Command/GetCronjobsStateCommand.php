<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Command;

use ChameleonSystem\CoreBundle\CronJob\CronjobEnablingServiceInterface;
use ChameleonSystem\CoreBundle\Exception\CronjobHandlingException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetCronjobsStateCommand extends Command
{
    /**
     * @var CronjobEnablingServiceInterface
     */
    private $cronjobEnablingService;

    public function __construct(CronjobEnablingServiceInterface $cronjobEnablingService)
    {
        parent::__construct('chameleon_system:cronjobs:state_check');

        $this->cronjobEnablingService = $cronjobEnablingService;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Checks if any cron job is running')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command outputs "running" if a cron job is currently running. Otherwise "idle".
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            if (true === $this->cronjobEnablingService->isOneCronjobRunning()) {
                $output->writeln('running');
            } else {
                $output->writeln('idle');
            }
        } catch (CronjobHandlingException $exception) {
            $output->writeln(sprintf('Cron job running state could not be checked: %s', $exception->getMessage()));

            return 1;
        }

        return 0;
    }
}

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

#[\Symfony\Component\Console\Attribute\AsCommand(description: 'Enables the cron job execution', name: 'chameleon_system:cronjobs:enable')]
class EnableCronjobsCommand extends Command
{
    private CronjobEnablingServiceInterface $cronjobEnablingService;

    public function __construct(CronjobEnablingServiceInterface $cronjobEnablingService)
    {
        parent::__construct('chameleon_system:cronjobs:enable');

        $this->cronjobEnablingService = $cronjobEnablingService;
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setHelp(<<<EOF
The <info>%command.name%</info> command enables all cronjob execution.
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->cronjobEnablingService->enableCronjobExecution();
        } catch (CronjobHandlingException $exception) {
            $output->writeln(sprintf('Cron job execution could not be enabled: %s', $exception->getMessage()));

            return 1;
        }

        return 0;
    }
}

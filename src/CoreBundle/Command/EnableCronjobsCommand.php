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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ChameleonSystem\CoreBundle\Exception\CronjobHandlingException;

class EnableCronjobsCommand extends Command
{
    /**
     * @var CronjobEnablingServiceInterface
     */
    private $cronjobEnablingService;

    public function __construct(CronjobEnablingServiceInterface $cronjobEnablingService)
    {
        parent::__construct('chameleon_system:cronjobs:enable');

        $this->cronjobEnablingService = $cronjobEnablingService;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Enables the cron job execution')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command enables all cronjob execution.
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
            $this->cronjobEnablingService->enableCronjobExecution();
        } catch (CronjobHandlingException $exception) {
            $output->writeln(sprintf('Cron job execution could not be enabled: %s', $exception->getMessage()));

            return 1;
        }

        return 0;
    }
}

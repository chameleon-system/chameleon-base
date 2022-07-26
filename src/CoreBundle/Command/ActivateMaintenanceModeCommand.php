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

use ChameleonSystem\CoreBundle\Exception\MaintenanceModeErrorException;
use ChameleonSystem\CoreBundle\Maintenance\MaintenanceMode\MaintenanceModeServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ActivateMaintenanceModeCommand extends Command
{
    /**
     * @var MaintenanceModeServiceInterface
     */
    private $maintenanceModeService;

    public function __construct(MaintenanceModeServiceInterface $maintenanceModeService)
    {
        parent::__construct('chameleon_system:maintenance_mode:activate');

        $this->maintenanceModeService = $maintenanceModeService;
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setDescription('Activates the maintenance mode')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command activates the maintenance mode.
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
            $this->maintenanceModeService->activate();
        } catch (MaintenanceModeErrorException $exception) {
            $output->writeln(sprintf('Maintenance mode could not be activated: %s', $exception->getMessage()));

            return 1;
        }

        return 0;
    }
}

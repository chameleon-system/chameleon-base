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
use ChameleonSystem\CoreBundle\Service\MaintenanceModeServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ActivateMaintenanceModeCommand extends Command
{
    public function __construct($name = null)
    {
        parent::__construct('chameleon_system:maintenance_mode:activate');
    }

    /**
     * {@inheritdoc}
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
        /**
         * @var $maintenanceModeService MaintenanceModeServiceInterface
         */
        $maintenanceModeService = ServiceLocator::get('chameleon_system_core.maintenance_mode_service');

        try {
            $maintenanceModeService->activate();
        } catch (MaintenanceModeErrorException $exception) {
            $output->writeln(sprintf('Maintenance mode could not be activated: %s', $exception->getMessage()));

            return 1;
        }

        return 0;
    }
}

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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command for counting updates that have not been executed yet.
 */
class UpdateCountCommand extends Command
{
    public function __construct($name = null)
    {
        parent::__construct('chameleon_system:update:count');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDefinition([])
            ->setDescription('Prints the count of all pending updates')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $updateManager = new \TCMSUpdateManager();
        $updateList = $updateManager->getAllUpdateFilesToProcess();

        $output->writeln(\count($updateList));

        return 0;
    }
}

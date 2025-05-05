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
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command for listing updates that have not been executed yet.
 */
#[\Symfony\Component\Console\Attribute\AsCommand(description: 'Lists all updates', name: 'chameleon_system:update:list')]
class ListUpdateCommand extends Command
{
    /**
     * @param string|null $name
     */
    public function __construct($name = null)
    {
        parent::__construct('chameleon_system:update:list');
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
The <info>%command.name%</info> command lists Chameleon updates not executed yet
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $updateManager = new \TCMSUpdateManager();
        $updateList = $updateManager->getAllUpdateFilesToProcess();

        $table = new Table($output);
        $table->setHeaders([
            'Bundle Name',
            'Build Number',
            'File Name',
        ]);
        $updateCount = 0;
        foreach ($updateList as $bundleName => $updates) {
            foreach ($updates as $update) {
                $table->addRow([
                    $bundleName,
                    $update->buildNumber,
                    $update->fileName,
                ]);
                ++$updateCount;
            }
        }
        $table->render();
        $output->writeln('');
        $output->writeln($updateCount.' updates total.');

        return 0;
    }
}

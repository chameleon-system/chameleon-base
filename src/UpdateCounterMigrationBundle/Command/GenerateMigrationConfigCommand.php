<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\UpdateCounterMigrationBundle\Command;

use ChameleonSystem\UpdateCounterMigrationBundle\Config\MigrationConfigGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateMigrationConfigCommand extends Command
{
    /**
     * @var MigrationConfigGenerator
     */
    private $migrationConfigGenerator;

    /**
     * @param MigrationConfigGenerator $migrationConfigGenerator
     */
    public function __construct(MigrationConfigGenerator $migrationConfigGenerator)
    {
        parent::__construct('chameleon_system:update_counter_migration:generate_config');
        $this->migrationConfigGenerator = $migrationConfigGenerator;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Generates the configuration needed to migrate update counters')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command generates the configuration values needed to update migration counters. Use the output of this command to configure the update counter migration:
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migrationConfigData = $this->migrationConfigGenerator->getMigrationConfigData();
        if (0 === count($migrationConfigData)) {
            $output->writeln('No bundles need to be migrated. Off to the pub!');

            return 0;
        }

        $output->writeln('chameleon_system_update_counter_migration:');
        $output->writeln('  mapping:');
        foreach ($migrationConfigData as $source => $target) {
            $output->writeln('    - '.$source.': '.$target);
        }

        return 0;
    }
}

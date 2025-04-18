<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\AutoclassesBundle\Command;

use ChameleonSystem\AutoclassesBundle\CacheWarmer\AutoclassesCacheWarmer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GenerateAutoclassesCommand Creates autoclasses from the console.
 */
#[AsCommand(description: 'Generates all autoclasses', name: 'chameleon_system:autoclasses:generate')]
class GenerateAutoclassesCommand extends Command
{
    /**
     * @var AutoclassesCacheWarmer
     */
    private $autoclassesCacheWarmer;

    public function __construct(AutoclassesCacheWarmer $autoclassesCacheWarmer)
    {
        parent::__construct();
        $this->autoclassesCacheWarmer = $autoclassesCacheWarmer;
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
The <info>%command.name%</info> command (re-)generates all autoclasses:
EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $output->writeln('Generating autoclasses...');
            $this->autoclassesCacheWarmer->updateAllTables();
            $output->writeln('<info>Done.</info>');
            $output->writeln('<info>Please make sure that your autoclasses folder is writable by the web server. Otherwise, actions in the backend modifying tables won\'t be able to update the classes.</info>');
            $output->writeln('<info>Preferably use ACLs or configure the system in a way that it uses the same user for command line operations and the web server.</info>');
            $output->writeln('<info>If nothing else helps, either do something like `sudo chown -R www-data app/cache` or, if you intend to switch between command line and web based cache write, do a nasty 777 on the folder.</info>');

            return 0;
        } catch (\Throwable $e) {
            $output->writeln('<error>There was an error generating the autoclasses: '.$e->getMessage().'</error>');
            $output->writeln('<error>Please delete the "autoclasses" and "autoclasse_" folders in the "var" directory and try again.</error>');
            throw $e;
        }
    }
}

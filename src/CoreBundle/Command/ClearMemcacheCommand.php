<?php

namespace ChameleonSystem\CoreBundle\Command;

use esono\pkgCmsCache\Cache;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command for clearing Memcached.
 */
#[\Symfony\Component\Console\Attribute\AsCommand(
    name: 'chameleon_system:memcache:clear',
    description: 'Clears the Memcached cache.'
)]
class ClearMemcacheCommand extends Command
{
    public function __construct(private readonly Cache $cache)
    {
        parent::__construct('chameleon_system:memcache:clear');
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->setHelp(<<<EOF
The <info>%command.name%</info> command clears the Memcached cache.
EOF);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->cache->clearAll();
        $output->writeln('<info>Memcached cache successfully cleared.</info>');
        return Command::SUCCESS;
    }
}
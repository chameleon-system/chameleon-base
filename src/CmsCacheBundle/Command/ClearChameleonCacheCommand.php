<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsCacheBundle\Command;

use esono\pkgCmsCache\CacheInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearChameleonCacheCommand extends Command
{
    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @param CacheInterface $cache
     * @param null|string    $name
     *
     * @throws LogicException When the command name is empty
     */
    public function __construct(CacheInterface $cache, $name = null)
    {
        parent::__construct($name);

        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('chameleon_system:cache:clear')
            ->setDescription('Clears the Chameleon cache.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->cache->clearAll();

        if (true === $this->cache->isActive()) {
            $output->writeln('<info>OK</info>');
        } else {
            $output->writeln('<comment>Note: Cache is not active.</comment>');
        }

        return 0;
    }
}

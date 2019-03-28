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
     * @param string|null    $name
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
        if (true === $this->cache->isActive()) {
            $this->cache->clearAll();
            $output->writeln('<info>OK</info>');
        } else {
            $output->writeln('<comment>Cache is not active, did not try to clear.</comment>');
        }

        return 0;
    }
}

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

use ChameleonSystem\AutoclassesBundle\CacheWarmer\AutoclassesCacheWarmer;
use ChameleonSystem\CoreBundle\Command\Helper\HtmlHelper;
use esono\pkgCmsCache\CacheInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command for executing updates.
 */
#[\Symfony\Component\Console\Attribute\AsCommand(description: 'Runs Chameleon database updates', name: 'chameleon_system:update:run')]
class RunUpdateCommand extends Command
{
    /**
     * @var CacheInterface
     */
    private $cache;
    /**
     * @var AutoclassesCacheWarmer
     */
    private $autoclassesCacheWarmer;

    public function __construct(CacheInterface $cache, AutoclassesCacheWarmer $autoclassesCacheWarmer)
    {
        parent::__construct('chameleon_system:update:run');
        $this->cache = $cache;
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
            ->setDefinition([])
            ->setHelp(<<<EOF
The <info>%command.name%</info> command runs Chameleon updates
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $updateManager = \TCMSUpdateManager::GetInstance();
        $updateList = $updateManager->getAllUpdateFilesToProcess();

        $output->writeln('Starting updates.');
        $helper = new HtmlHelper($output);
        $style = new OutputFormatterStyle(null, null, ['underscore']);
        $output->getFormatter()->setStyle('underscore', $style);
        foreach ($updateList as $bundleName => $updates) {
            foreach ($updates as $update) {
                $result = $updateManager->runSingleUpdate($update->fileName, $bundleName);
                $output->writeln('<underscore>'.$update->fileName.'</underscore>');
                $output->writeln('');
                $helper->render($result->getUpdateStatus());
                if (null !== $result->getMessage()) {
                    $helper->render($result->getMessage());
                }
                if (count($result->getExceptions()) > 0) {
                    /*
                     * @psalm-suppress InvalidArgument - Passing an array to HtmlHelper::render() is valid but not annotated.
                     */
                    $helper->render($result->getExceptions());
                }
                if (count($result->getInfoMessages()) > 0) {
                    /*
                     * @psalm-suppress InvalidArgument - Passing an array to HtmlHelper::render() is valid but not annotated.
                     */
                    $helper->render($result->getInfoMessages());
                }
                $output->writeln('');
            }
        }
        $output->writeln('Finished updates.');

        $output->writeln('Starting table updates.');
        $this->autoclassesCacheWarmer->updateAllTables();
        $output->writeln('Finished table updates.');

        $output->writeln('Starting virtual classes update.');
        \TCMSLogChange::UpdateVirtualNonDbClasses();
        $output->writeln('Finished update virtual classes.');

        $output->writeln('Starting cache clearing.');
        $this->cache->clearAll();
        $output->writeln('Finished cache clearing.');

        return 0;
    }
}

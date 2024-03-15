<?php

namespace ChameleonSystem\NewsletterBundle\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TdbPkgNewsletterCampaign;
use TdbPkgNewsletterCampaignList;

class SendNewsletterCommand extends Command
{
    private Connection $databaseConnection;

    protected static $defaultName = 'chameleon_system:newsletter:send-newsletter';

    public function __construct(Connection $databaseConnection)
    {
        parent::__construct();

        $this->databaseConnection = $databaseConnection;
    }

    protected function configure()
    {
        $this
            ->setDescription('Sends a specific newsletter by name.')
            ->addArgument('identifier', InputArgument::REQUIRED, 'Newsletter name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $identifier = $input->getArgument('identifier');

        $campaign = TdbPkgNewsletterCampaign::GetNewInstance();
        if (false === $campaign->LoadFromField('name', $identifier)) {
            $output->writeln('No newsletter found with the given identifier.');
            return 0;
        }

        try {
            $campaign->SendNewsletter();
            $output->writeln('Newsletter sent successfully.');

            return 0;
        } catch (\Exception $exception) {
            $output->writeln(sprintf('Exception: %s', $exception->getMessage()));

            return 1;
        }

    }
}

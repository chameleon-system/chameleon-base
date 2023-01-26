<?php

namespace ChameleonSystem\AutoclassesBundle\Command;

use ChameleonSystem\AutoclassesBundle\TableConfExport\TableConfExporterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DumpTableConfCommand extends Command
{
    private const ENTITY_NAMESPACE = '\ChameleonSystem\CoreBundle\Entity';

    public function __construct(
        private readonly TableConfExporterInterface $tableConfExporter,
        private readonly string $projectPath,
        private readonly array $tableClassMapping = [],
    ) {
        parent::__construct();
    }
    protected function configure()
    {
        $this->setName('chameleon_system:autoclasses:dump')
            ->setDescription('Export the table configurations as doctrine data models');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tables = $this->tableConfExporter->getTables();
        $targetDir = $this->projectPath.'/vendor/chameleon-system/chameleon-base/src/CoreBundle/Entity';
        $mappingDir = $this->projectPath.'/vendor/chameleon-system/chameleon-base/src/CoreBundle/Resources/config/doctrine';
        if (false===is_dir($targetDir)) {
            if (!mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $targetDir));
            }
        }
        if (false===is_dir($mappingDir)) {
            if (!mkdir($mappingDir, 0755, true) && !is_dir($mappingDir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $mappingDir));
            }
        }
        foreach ($tables as $table) {
            $output->write(sprintf('<info>Exporting %s...</info>',$table->name));

            try {
                $fqn = $this->tableConfExporter->export($table, self::ENTITY_NAMESPACE, $targetDir, $mappingDir);
                $output->writeln(sprintf('<info>%s</info>',$fqn));
            } catch (\Exception $e) {
                $output->writeln(sprintf('<error>Error: %s</error>',$e->getMessage()));
            }
        }

        return self::SUCCESS;
    }


}
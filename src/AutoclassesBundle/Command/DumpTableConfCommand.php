<?php

namespace ChameleonSystem\AutoclassesBundle\Command;

use ChameleonSystem\AutoclassesBundle\TableConfExport\LegacyTableExportConfig;
use ChameleonSystem\AutoclassesBundle\TableConfExport\TableConfExporterInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'chameleon_system:autoclasses:dump', description: 'Export the table configurations as doctrine data models')]
class DumpTableConfCommand extends Command
{
    public function __construct(
        private readonly TableConfExporterInterface $tableConfExporter,
        private readonly LegacyTableExportConfig $legacyTableExportConfig
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tables = $this->tableConfExporter->getTables();

        $tableNamespaceMapping = $this->getTableNamespaceMapping();
        foreach ($tables as $table) {
            $tableConfig = $this->legacyTableExportConfig->getTableConfig($table->name);
            $output->write(sprintf('<info>Exporting %s...</info>', $table->name));

            try {
                $targetDir = $tableConfig['targetDir'];
                $mappingDir = $tableConfig['configDir'];
                if (false === is_dir($targetDir)) {
                    if (!mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
                        throw new \RuntimeException(sprintf('Directory "%s" was not created', $targetDir));
                    }
                }
                if (false === is_dir($mappingDir)) {
                    if (!mkdir($mappingDir, 0755, true) && !is_dir($mappingDir)) {
                        throw new \RuntimeException(sprintf('Directory "%s" was not created', $mappingDir));
                    }
                }
                // disabled for now - the meta data yaml makes sense only after we moved to another admin so we can remove the table conf fully
                // if (false===is_dir($tableConfig['metaConfigDir'])) {
                //    if (!mkdir($tableConfig['metaConfigDir'], 0755, true) && !is_dir($tableConfig['metaConfigDir'])) {
                //        throw new \RuntimeException(sprintf('Directory "%s" was not created', $tableConfig['metaConfigDir']));
                //    }
                // }
                $fqn = $this->tableConfExporter->export(
                    $table,
                    $tableConfig['namespace'],
                    $targetDir,
                    $mappingDir,
                    $tableConfig['metaConfigDir'],
                    $tableNamespaceMapping
                );

                $output->writeln(sprintf('<info>%s</info>', $fqn));
            } catch (\Exception $e) {
                $output->writeln(sprintf('<error>Error: %s</error>', $e->getMessage()));
            }
        }

        return self::SUCCESS;
    }

    private function getTableNamespaceMapping(): array
    {
        $mapping = [];
        foreach ($this->legacyTableExportConfig->getConfig() as $mappings) {
            foreach ($mappings['tables'] as $table) {
                $mapping[$table] = $mappings['namespace'];
            }
        }

        return $mapping;
    }
}

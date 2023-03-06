<?php

namespace ChameleonSystem\AutoclassesBundle\Command;

use ChameleonSystem\AutoclassesBundle\TableConfExport\TableConfExporterInterface;
use ChameleonSystem\DataAccessBundle\ChameleonSystemDataAccessBundle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;

class DumpTableConfCommand extends Command
{
    public function __construct(
        private readonly TableConfExporterInterface $tableConfExporter,
        private readonly FileLocator $fileLocator,
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


        $tableNamespaceMapping = $this->getTableNamespaceMapping();
        foreach ($tables as $table) {
            $tableConfig = $this->getTableConfig($table->name);
            if (null === $tableConfig) {
                $output->writeln(sprintf('<warning>No Config for %s found - using default</warning>', $table->name));
                $tableConfig = [
                    'targetDir' => '@AppBundle/src/Entity',
                    'configDir' => 'AppBundle/config/doctrine',
                    'namespace' => '\\AppBundle\\Entity',
                    'tables' => [],
                ];
            }
            $output->write(sprintf('<info>Exporting %s...</info>',$table->name));

            try {

                if ('@' === substr( $tableConfig['targetDir'], 0, 1)) {
                    $bundle = substr($tableConfig['targetDir'], 0, strpos($tableConfig['targetDir'], '/'));
                    $bundleBasePath = $this->fileLocator->locate($bundle);

                    $tableConfig['targetDir'] = str_replace(sprintf('%s/', $bundle),$bundleBasePath, $tableConfig['targetDir']);
                    $tableConfig['configDir'] = str_replace(sprintf('%s/', $bundle),$bundleBasePath, $tableConfig['configDir']);
                }

                $targetDir = $tableConfig['targetDir'];
                $mappingDir = $tableConfig['configDir'];
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

                $fqn = $this->tableConfExporter->export($table, $tableConfig['namespace'], $targetDir, $mappingDir, $tableNamespaceMapping);
                $output->writeln(sprintf('<info>%s</info>',$fqn));
            } catch (\Exception $e) {
                $output->writeln(sprintf('<error>Error: %s</error>',$e->getMessage()));
            }
        }

        return self::SUCCESS;
    }

    private function getTableConfig(string $tableName): ?array
    {
        foreach ($this->tableClassMapping as $config) {
            if (in_array($tableName, $config['tables'], true)) {
                return $config;
            }
        }
        return null;
    }

    private function getTableNamespaceMapping(): array
    {
        $mapping = [];
        foreach ($this->tableClassMapping as $mappings) {
            foreach ($mappings['tables'] as $table) {
                $mapping[$table] = $mappings['namespace'];
            }
        }

        return $mapping;
    }


}
<?php

namespace ChameleonSystem\AutoclassesBundle\TableConfExport;

use ChameleonSystem\CoreBundle\DataModel\TableConfigurationDataModel;

interface TableConfExporterInterface
{
    /**
     * @return array<string,TableConfigurationDataModel>
     */
    public function getTables(): array;

    /**
     * @return string - fqn of the generated model
     *
     * @throws \Exception
     */
    public function export(
        TableConfigurationDataModel $table,
        string $namespace,
        string $targetDir,
        string $mappingDir,
        string $metaConfigDir,
        array $tableNamespaceMapping
    ): string;
}

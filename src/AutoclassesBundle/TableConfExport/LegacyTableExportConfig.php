<?php

namespace ChameleonSystem\AutoclassesBundle\TableConfExport;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpKernel\Config\FileLocator;

class LegacyTableExportConfig
{
    private array $legacyTableExportConfig;

    /** @psalm-suppress UndefinedAttributeClass */
    public function __construct(
        private readonly FileLocator $fileLocator,
        #[ArrayShape([
                [
                    'targetDir' => 'string',
                    'configDir' => 'string',
                    'metaConfigDir' => 'int',
                    'namespace' => 'string',
                    'tables' => 'string[]',
                ],
            ]
        )]
        array $tableClassMapping
    ) {
        $this->legacyTableExportConfig = [];
        foreach ($tableClassMapping as $key => $tableConfig) {
            if (!str_starts_with($tableConfig['targetDir'], '@')) {
                continue;
            }
            $bundle = substr($tableConfig['targetDir'], 0, strpos($tableConfig['targetDir'], '/'));
            $bundleBasePath = $this->fileLocator->locate($bundle);

            $tableConfig['targetDir'] = str_replace(
                sprintf('%s/', $bundle),
                $bundleBasePath,
                $tableConfig['targetDir']
            );
            $tableConfig['configDir'] = str_replace(
                sprintf('%s/', $bundle),
                $bundleBasePath,
                $tableConfig['configDir']
            );
            $tableConfig['metaConfigDir'] = str_replace(
                sprintf('%s/', $bundle),
                $bundleBasePath,
                $tableConfig['metaConfigDir']
            );

            $this->legacyTableExportConfig[$key] = $tableConfig;
        }
    }

    public function getConfig(): array
    {
        return $this->legacyTableExportConfig;
    }

    /**
     * @return array{
     *               targetDir: string,
     *               configDir: string,
     *               metaConfigDir: string,
     *               namespace: string,
     *               tables: string[],
     *         }
     */
    public function getTableConfig(string $tableName): array
    {
        foreach ($this->legacyTableExportConfig as $config) {
            if (in_array($tableName, $config['tables'], true)) {
                return $config;
            }
        }

        return $this->getDefaultTableConfig();
    }

    /**
     * @return array{
     *               targetDir: string,
     *               configDir: string,
     *               metaConfigDir: string,
     *               namespace: string,
     *               tables: string[],
     *         }
     */
    private function getDefaultTableConfig(): array
    {
        return [
            'targetDir' => '@AppBundle/src/Entity',
            'configDir' => 'AppBundle/config/doctrine',
            'metaConfigDir' => 'AppBundle/config/autoClass',
            'namespace' => '\\AppBundle\\Entity',
            'tables' => [],
        ];
    }
}

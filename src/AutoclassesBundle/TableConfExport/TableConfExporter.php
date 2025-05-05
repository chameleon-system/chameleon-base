<?php

namespace ChameleonSystem\AutoclassesBundle\TableConfExport;

use ChameleonSystem\AutoclassesBundle\DataAccess\AutoclassesDataAccessInterface;
use ChameleonSystem\CoreBundle\DataAccess\DataAccessCmsTblConfInterface;
use ChameleonSystem\CoreBundle\DataModel\TableConfigurationDataModel;
use Symfony\Component\Yaml\Yaml;

class TableConfExporter implements TableConfExporterInterface
{
    public function __construct(
        private readonly DataAccessCmsTblConfInterface $dataAccessCmsTblConf,
        private readonly AutoclassesDataAccessInterface $autoclassesDataAccess,
        private readonly \IPkgSnippetRenderer $snippetRenderer
    ) {
    }

    public function getTables(): array
    {
        return $this->dataAccessCmsTblConf->getTableConfigurations();
    }

    public function export(
        TableConfigurationDataModel $table,
        string $namespace,
        string $targetDir,
        string $mappingDir,
        string $metaConfigDir,
        array $tableNamespaceMapping
    ): string {
        $tableConf = $this->autoclassesDataAccess->getTableConfigData()[$table->id] ?? null;

        if (null === $tableConf) {
            throw new \Exception(
                sprintf(
                    'Unable to generate data class for table %s - no config found for table id %s',
                    $table->name,
                    $table->id
                )
            );
        }

        $fieldConfig = $this->autoclassesDataAccess->getFieldData()[$table->id] ?? null;
        if (null === $fieldConfig) {
            throw new \Exception(
                sprintf(
                    'Unable to generate data class for table %s - no fields found for table id id %s',
                    $table->name,
                    $table->id
                )
            );
        }
        $fields = [];
        $dataModelPartsList = [];
        $fieldConfig->GoToStart();
        while ($field = $fieldConfig->Next()) {
            if (false === $field instanceof DoctrineTransformableInterface || false === $field instanceof \TCMSField) {
                continue;
            }
            $fields[] = $field;

            $dataModelParts = $field->getDoctrineDataModelParts($namespace, $tableNamespaceMapping);
            if (null === $dataModelParts) {
                continue;
            }
            $dataModelPartsList[] = $dataModelParts;
        }
        $className = $this->snakeToPascalCase($tableConf['name']);
        $fqn = sprintf('%s\%s', $namespace, $className);

        $dataModelCode = $this->generateDataModelCode(
            $tableConf,
            $className,
            $fqn,
            $namespace,
            $fields,
            $dataModelPartsList,
            $tableNamespaceMapping
        );
        $dataModelMapping = $this->generateDataModelMapping(
            $tableConf,
            $className,
            $fqn,
            $namespace,
            $fields,
            $dataModelPartsList,
            $tableNamespaceMapping
        );

        // todo: re-enable once we move to another admin that may need this data.
        // $autoClassConfig = $this->generateAutoClassConfig($tableConf, $className, $fqn, $namespace);

        $mappingSubPathPos = strpos($mappingDir, '/config/doctrine');
        $extension = substr($mappingDir, $mappingSubPathPos + strlen('/config/doctrine/'));
        $mappingCleanPath = substr($mappingDir, 0, $mappingSubPathPos).'/config/doctrine';
        $mappingClass = $className;
        if ('' !== $extension && '/' !== $extension) {
            $mappingClass = str_replace('/', '.', trim($extension, '/').'/'.$className);
        }

        if (false === file_put_contents($targetDir.'/'.$className.'.php', $dataModelCode)) {
            throw new \RuntimeException(sprintf('Failed to write data model code to file (%s).', $targetDir.'/'.$className.'.php'));
        }
        if (false === file_put_contents($mappingCleanPath.'/'.$mappingClass.'.orm.xml', $dataModelMapping)) {
            throw new \RuntimeException(sprintf('Failed to write data model mapping to file (%s).', $mappingCleanPath.'/'.$mappingClass.'.orm.xml'));
        }

        // todo: re-enable once we move to another admin that may need this data.
        // file_put_contents($metaConfigDir.'/'.$mappingClass.'.yaml', $autoClassConfig);

        return $fqn;
    }

    /**
     * @param array $tableConf
     * @param \TCMSField[] $fields
     *
     * @throws \TPkgSnippetRenderer_SnippetRenderingException
     */
    public function generateDataModelMapping(
        mixed $tableConf,
        string $className,
        string $fqn,
        string $namespace,
        array $fields,
        array $propertyMappings
    ): string {
        $snippetRenderer = clone $this->snippetRenderer;
        $snippetRenderer->InitializeSource(
            'ChameleonSystemAutoclasses/mapping.xml.twig',
            \IPkgSnippetRenderer::SOURCE_TYPE_FILE
        );
        $snippetRenderer->clear();
        $snippetRenderer->setVar('table', $tableConf);
        $snippetRenderer->setVar('className', $className);
        $snippetRenderer->setVar('fqn', ltrim($fqn, '\\'));
        $snippetRenderer->setVar('namespace', ltrim($namespace, '\\'));
        $snippetRenderer->setVar('fields', $fields);
        $snippetRenderer->setVar(
            'propertyMappings',
            array_map(static fn (DataModelParts $part) => $part->getMappingXml(), $propertyMappings)
        );
        $snippetRenderer->setVar(
            'liveCycleCallbacks',
            array_map(static fn (DataModelParts $part) => $part->getLiveCycleCallbacks(), $propertyMappings)
        );

        return $snippetRenderer->render();
    }

    /**
     * @param array $tableConf
     * @param \TCMSField[] $fields
     * @param DataModelParts[] $dataModelPartsList
     *
     * @throws \TPkgSnippetRenderer_SnippetRenderingException
     */
    public function generateDataModelCode(
        mixed $tableConf,
        string $className,
        string $fqn,
        string $namespace,
        array $fields,
        array $dataModelPartsList
    ): string {
        $oSnippetRenderer = clone $this->snippetRenderer;
        $oSnippetRenderer->InitializeSource(
            'ChameleonSystemAutoclasses/class.php.twig',
            \IPkgSnippetRenderer::SOURCE_TYPE_FILE
        );
        $oSnippetRenderer->clear();
        $oSnippetRenderer->setVar('tableConf', $tableConf);
        $oSnippetRenderer->setVar('className', $className);
        $oSnippetRenderer->setVar('fqn', $fqn);
        $oSnippetRenderer->setVar('namespace', ltrim($namespace, '\\'));
        $oSnippetRenderer->setVar('fields', $fields);
        $oSnippetRenderer->setVar('dataModelPartsList', $dataModelPartsList);

        $selfName = ltrim(sprintf('%s\%s', $namespace, $className), '\\');
        $imports = [];
        foreach ($dataModelPartsList as $dataModel) {
            foreach ($dataModel->getClassImports() as $include) {
                if ($include === $selfName) {
                    continue;
                }
                $imports[] = $include;
            }
        }
        $imports = array_unique($imports);
        $oSnippetRenderer->setVar('imports', $imports);

        $content = $oSnippetRenderer->render();

        return $content;
    }

    private function getFieldName(\TCMSField $field): string
    {
        return $this->snakeToPascalCase($field->name);
    }

    private function snakeToPascalCase(string $string): string
    {
        $pascalCaseName = preg_replace_callback('/(^|_|\.)+(.)/', static function ($match) {
            return ('.' === $match[1] ? '_' : '').strtoupper($match[2]);
        }, $string);

        return $pascalCaseName;
    }

    private function indent(?string $string, int $indent): ?string
    {
        if (null === $string) {
            return null;
        }

        return str_replace("\n", "\n".str_repeat(' ', $indent), $string);
    }

    private function generateAutoClassConfig(
        array $tableConf,
        string $className,
        string $fqn,
        string $namespace
    ): string {
        $allTableExtensions = $this->autoclassesDataAccess->getTableExtensionData();
        $extensionRows = $allTableExtensions[$tableConf['id']] ?? [];

        $recordClassList = [];
        foreach ($extensionRows as $extensionRow) {
            if ('' === $extensionRow['name']) {
                continue;
            }
            $recordClassList[] = $extensionRow['name'];
        }
        $listClassList = [];
        foreach ($extensionRows as $extensionRow) {
            if ('' === $extensionRow['name_list']) {
                continue;
            }
            $listClassList[] = $extensionRow['name_list'];
        }
        $tableExtensionConfig = new AutoClassConfigurationDataModel(
            name: $tableConf['name'],
            record: new AutoClassConfigurationDefinition(
                entryClass: sprintf('Tdb%s', $className),
                exitClass: $tableConf['dbobject_extend_class'],
                classList: $recordClassList
            ),
            list: new AutoClassConfigurationDefinition(
                entryClass: sprintf('Tdb%sList', $className),
                exitClass: 'TCMSRecordList',
                classList: $listClassList
            ),
        );

        $tableConfigArray = $tableExtensionConfig->asArray();
        $configArray = [];
        $configArray['chameleon_system_autoclasses']['table_class_mapping'] = $tableConfigArray;

        return Yaml::dump($configArray);
    }
}

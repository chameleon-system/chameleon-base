<?php

namespace ChameleonSystem\AutoclassesBundle\TableConfExport;

use ChameleonSystem\AutoclassesBundle\DataAccess\AutoclassesDataAccessInterface;
use ChameleonSystem\CoreBundle\DataAccess\DataAccessCmsTblConfInterface;
use ChameleonSystem\CoreBundle\DataModel\TableConfigurationDataModel;

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

    public function export(TableConfigurationDataModel $table, string $namespace, string $targetDir, string $mappingDir): string
    {


        $tableConf = $this->autoclassesDataAccess->getTableConfigData()[$table->id] ?? null;

        if (null === $tableConf) {
            throw new \Exception(sprintf("Unable to generate data class for table %s - no config found for table id %s", $table->name, $table->id));
        }

        $fieldConfig = $this->autoclassesDataAccess->getFieldData()[$table->id] ?? null;
        if (null === $fieldConfig) {
            throw new \Exception(sprintf("Unable to generate data class for table %s - no fields found for table id id %s", $table->name, $table->id));
        }
        $fields = [];
        $classProperties = [];
        $propertyMappings = [];
        $fieldConfig->GoToStart();
        while ($field = $fieldConfig->Next()) {
            $fields[] = $field;
            $propertyMapping = $field->getDoctrineDataModelXml($namespace);
            if (null !== $propertyMapping) {
                $propertyMappings[] = $propertyMapping;
            }
            $classPropertyType = $this->getClassProperty($field, $namespace);
            if (null === $classPropertyType) {
                continue;
            }
            $classProperties[] = $classPropertyType;
        }
        $className = $this->snakeToCamelCase($tableConf['name'], false);
        $fqn = sprintf('%s\%s', $namespace, $className);

        $dataModelCode = $this->generateDataModelCode($tableConf, $className, $fqn, $namespace, $fields, $classProperties);
        $dataModelMapping = $this->generateDataModelMapping($tableConf, $className, $fqn, $namespace, $fields, $propertyMappings);

        file_put_contents($targetDir.'/'.$className.'.php', $dataModelCode);
        file_put_contents($mappingDir.'/'.$className.'.orm.xml', $dataModelMapping);

        return $fqn;

    }

    private function getClassProperty(\TCMSField $field, string $namespace): ?string
    {
        if ($field instanceof DoctrineTransformableInterface) {
            return $this->indent($field->getDoctrineDataModelAttribute($namespace), 4);
        }

        return null;
    }

    /**
     * @param array $tableConf
     * @param string $className
     * @param string $fqn
     * @param string $namespace
     * @param \TCMSField[] $fields
     * @param array<string> $classProperties
     * @return string
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
        $oSnippetRenderer = clone $this->snippetRenderer;
        $oSnippetRenderer->InitializeSource(
            'ChameleonSystemAutoclasses/mapping.xml.twig',
            \IPkgSnippetRenderer::SOURCE_TYPE_FILE
        );
        $oSnippetRenderer->clear();
        $oSnippetRenderer->setVar('table', $tableConf);
        $oSnippetRenderer->setVar('className', $className);
        $oSnippetRenderer->setVar('fqn', ltrim($fqn));
        $oSnippetRenderer->setVar('namespace', ltrim($namespace, '\\'));
        $oSnippetRenderer->setVar('fields', $fields);
        $oSnippetRenderer->setVar('propertyMappings', $propertyMappings);

        $content = $oSnippetRenderer->render();

        return $content;
    }

    /**
     * @param array $tableConf
     * @param string $className
     * @param string $fqn
     * @param string $namespace
     * @param \TCMSField[] $fields
     * @param array<string> $classProperties
     * @return string
     * @throws \TPkgSnippetRenderer_SnippetRenderingException
     */
    public function generateDataModelCode(
        mixed $tableConf,
        string $className,
        string $fqn,
        string $namespace,
        array $fields,
        array $classProperties
    ): string {
        $oSnippetRenderer = clone $this->snippetRenderer;
        $oSnippetRenderer->InitializeSource(
            'ChameleonSystemAutoclasses/test.php.twig',
            \IPkgSnippetRenderer::SOURCE_TYPE_FILE
        );
        $oSnippetRenderer->clear();
        $oSnippetRenderer->setVar('table', $tableConf);
        $oSnippetRenderer->setVar('className', $className);
        $oSnippetRenderer->setVar('fqn', $fqn);
        $oSnippetRenderer->setVar('namespace', ltrim($namespace, '\\'));
        $oSnippetRenderer->setVar('fields', $fields);
        $oSnippetRenderer->setVar('classProperties', $classProperties);

        $content = $oSnippetRenderer->render();

        return $content;
    }

    private function getFieldName(\TCMSField $field): string
    {
        return $this->snakeToCamelCase($field->name);
    }

    public function snakeToCamelCase(string $string, bool $lowerCaseFirst = true): string
    {
        $camelCasedName = preg_replace_callback('/(^|_|\.)+(.)/', function ($match) {
            return ('.' === $match[1] ? '_' : '').strtoupper($match[2]);
        }, $string);

        if ($lowerCaseFirst) {
            $camelCasedName = lcfirst($camelCasedName);
        }

        return $camelCasedName;
    }

    private function indent(?string $string, int $indent):?string
    {
        if (null === $string) {
            return null;
        }

        return str_replace("\n", "\n".str_repeat(' ', $indent), $string);
    }
}
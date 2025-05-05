<?php

namespace ChameleonSystem\AutoclassesBundle\TableConfExport;

interface DoctrineTransformableInterface
{
    /**
     * Return null if the field should not be transformed. Otherwise, return the attribute string (including type, default value etc).
     */
    public function getDoctrineDataModelParts(string $namespace, array $tableNamespaceMapping): DataModelParts;
}

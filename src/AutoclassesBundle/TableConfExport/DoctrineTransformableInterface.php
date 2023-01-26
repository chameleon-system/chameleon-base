<?php

namespace ChameleonSystem\AutoclassesBundle\TableConfExport;

interface DoctrineTransformableInterface
{
    /**
     * Return null if the field should not be transformed. Otherwise, return the attribute string (including type, default value etc)
     * @return string|null
     */
    public function getDoctrineDataModelAttribute(string $namespace): ?string;

    /**
     * return the xml config for the doctrine entity.
     */
    public function getDoctrineDataModelXml(string $namespace): ?string;
}
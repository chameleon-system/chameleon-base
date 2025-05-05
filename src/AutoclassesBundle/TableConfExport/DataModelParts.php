<?php

namespace ChameleonSystem\AutoclassesBundle\TableConfExport;

class DataModelParts
{
    public function __construct(
        private readonly string $property,
        private readonly string $methods,
        private readonly string $mappingXml,
        private readonly array $classImports = [],
        private readonly bool $defaultValue = false,
        /** @var array<string, array<string>> - key is the event, value a list of methods */
        private readonly array $liveCycleCallbacks = []
    ) {
    }

    public function merge(DataModelParts $additional): DataModelParts
    {
        $liveCycleCallbacks = $this->liveCycleCallbacks;
        foreach ($additional->liveCycleCallbacks as $event => $methods) {
            $newListOfMethods = $this->liveCycleCallbacks[$event] ?? [];
            array_push($newListOfMethods, ...$methods);
            $newListOfMethods = array_unique($newListOfMethods);
            $liveCycleCallbacks[$event] = $newListOfMethods;
        }

        return new DataModelParts(
            implode(",\n", [$this->property, $additional->property]),
            implode("\n", [$this->methods.$additional->methods]),
            sprintf("%s\n%s", $this->mappingXml, $additional->mappingXml),
            array_merge($this->classImports, $additional->classImports),
            $this->defaultValue || $additional->defaultValue,
            $liveCycleCallbacks
        );
    }

    public function getMappingXml(): string
    {
        return $this->mappingXml;
    }

    public function getLiveCycleCallbacks(): array
    {
        return $this->liveCycleCallbacks;
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function getMethods(): string
    {
        return $this->methods;
    }

    public function getClassImports(): array
    {
        return $this->classImports;
    }

    public function hasDefaultValue(): bool
    {
        return $this->defaultValue;
    }
}
